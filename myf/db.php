<?php
// +----------------------------------------------------------------------
// | MyfPHP 闵益飞PHP MVC框架  -- 数据库操作类
// +----------------------------------------------------------------------
// | Copyright (c) 2012 http://www.minyifei.cn All rights reserved.
// +----------------------------------------------------------------------
// | 交流论坛：http://bbs.minyifei.cn
// +----------------------------------------------------------------------
// error_reporting(0);$_config =
require_once (dirname(APP_SYS_PATH) . '/config.php');
error_reporting(E_ALL & ~E_NOTICE ^ E_DEPRECATED);
class DB {

    //数据库连接标识
    protected $link = null;

    //当前操作的表
    public $table = '';

    //查询参数
    protected $options = array();

    //当前执行的SQL语句
    protected $sql = '';

    //数据库查询次数
    protected $queryCount = 0;

    //缓存次数
    protected $cacheCount = 0;

    //缓存路径
    protected $cachePath = '';

    //数据返回类型, 1代表数组, 2代表对象
    protected $returnType = 1;

    public static $debug = false;

    static private $_db = null;

    static private $_dbname = null;

    public static function getInstance($dbname) {
        if (DB::$debug) {
            dump(array("instance" => $dbname));
        }
        if (self::$_db == null || self::$_dbname != $dbname) {
            $db = array();
            $db["pconnect"] = false;
            $db["host"] = C("$dbname.DB_HOST");
            $db["port"] = C("$dbname.DB_PORT");
            $db["user"] = C("$dbname.DB_USER");
            $db["pwd"] = C("$dbname.DB_PWD");
            $db["database"] = C("$dbname.DB_NAME");
            $db['char'] = 'utf8';
            self::$_dbname = $dbname;
            self::$_db = new Db($db);
        }
        return self::$_db;
    }

    function __construct($db) {
        if (DB::$debug) {
            mlog("start-connect-db");
            $startTime = getMillisecond();
        }
        //根据配置使用不同函数连接数据库
        $db['host'] = isset($db['port']) ? $db['host'] . ':' . $db['port'] : $db['host'];
        $func = $db['pconnect'] ? 'mysql_pconnect' : 'mysql_connect';
        $this -> link = $func($db['host'], $db['user'], $db['pwd']);
        mysql_select_db($db['database'], $this -> link);
        mysql_query("SET NAMES '{$db['char']}'");
        mysql_query("SET interactive_timeout = 2;");        mysql_query("SET wait_timeout = 2;");

        $this -> cachePath = isset($db['cachepath']) ? $db['cachepath'] : './';
        if (DB::$debug) {
            mlog("end-connect-db");
            $endTime = getMillisecond();
            $log = array("数据库配置" => $db, "link" => $this -> link, "connect castTime" => $endTime - $startTime);
            mlog($log);
        }
    }

    /**
     * 连接数据库
     *
     * @access      public
     * @param       array    $db  数据库配置
     * @return      resource 数据库连接标识
     */
    public function connect($db) {
        //根据配置使用不同函数连接数据库
        $db['host'] = isset($db['port']) ? $db['host'] . ':' . $db['port'] : $db['host'];
        $func = $db['pconnect'] ? 'mysql_pconnect' : 'mysql_connect';
        $this -> link = $func($db['host'], $db['user'], $db['pwd']);
        mysql_select_db($db['database'], $this -> link);
        mysql_query("SET NAMES '{$db['char']}'");
        $this -> cachePath = isset($db['cachepath']) ? $db['cachepath'] : './';
        return $this -> link;
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 查询符合条件的一条记录
     *
     * @access      public
     * @param       string    $where  查询条件
     * @param       string    $field  查询字段
     * @param       string    $table  表
     * @return      mixed             符合条件的记录
     */
    public function find($where = NULL, $field = '*', $table = '') {
        return $this -> findAll($where, $field, $table, FALSE);
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 查询符合条件的所有记录
     *
     * @access      public
     * @param       string    $where  查询条件
     * @param       string    $field  查询字段
     * @param       string    $table  表
     * @return      mixed             符合条件的记录
     */
    public function findAll($where = NULL, $field = '*', $table = '', $all = TRUE) {
        $this -> options['where'] = is_null($where) ? $this -> options['where'] : $where;
        $this -> options['field'] = isset($this -> options['field']) ? $this -> options['field'] : $field;
        $this -> options['table'] = $table == '' ? $this -> table : $table;
        $sql = "SELECT {$this->options['field']} FROM `{$this->options['table']}` ";
        $sql .= isset($this -> options['join']) ? ' LEFT JOIN ' . $this -> options['join'] : '';
        $sql .= isset($this -> options['where']) ? ' WHERE ' . $this -> options['where'] : '';
        $sql .= isset($this -> options['group']) ? ' GROUP BY ' . $this -> options['group'] : '';
        $sql .= isset($this -> options['having']) ? ' HAVING ' . $this -> options['having'] : '';
        $sql .= isset($this -> options['order']) ? ' ORDER BY ' . $this -> options['order'] : '';
        $sql .= isset($this -> options['limit']) ? ' LIMIT ' . $this -> options['limit'] : '';
        $this -> sql = $sql;
        $row = NULL;

        $result = $this -> query();
        $row = $all === TRUE ? $this -> fetchAll($result) : $this -> fetch($result);
        $this -> options = array();

        return $row;
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 读取结果集中的所有记录到数组中
     *
     * @access public
     * @param  resource  $sql  查询语句
     * @return array
     */
    public function findSqlAll($sql) {
        $result = $this -> query($sql);
        $this -> options = array();
        return $this -> fetchAll($result);
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 读取结果集中的所有记录到数组中
     *
     * @access public
     * @param  resource  $sql  查询语句
     * @return array
     */
    public function findSqlOne($sql) {
        $result = $this -> query($sql);
        $this -> options = array();
        return $this -> fetch($result);
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 读取结果集中的所有记录到数组中
     *
     * @access public
     * @param  resource  $where  查询语句
     * @return array
     */
    public function count($where = null) {
        $this -> options['where'] = is_null($where) ? $this -> options['where'] : $where;
        $sql = "SELECT count(*) as count FROM `{$this->table}` ";
        $sql .= isset($this -> options['where']) ? ' WHERE ' . $this -> options['where'] : '';
        $this -> sql = $sql;
        $row = NULL;
        $result = $this -> query();
        $row = $this -> fetch($result);
        $row = intval($row["count"]);
        $this -> options = array();
        return $row;
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 读取结果集中的所有记录到数组中
     *
     * @access public
     * @param  resource  $result  结果集
     * @return array
     */
    public function fetchAll($result = NULL) {
        $rows = array();
        while ($row = $this -> fetch($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 读取结果集中的一行记录到数组中
     *
     * @access public
     * @param  resource  $result  结果集
     * @param  int       $type    返回类型, 1为数组, 2为对象
     * @return mixed              根据返回类型返回
     */
    public function fetch($result = NULL, $type = NULL) {
        $result = is_null($result) ? $this -> result : $result;
        $type = is_null($type) ? $this -> returnType : $type;
        $func = $type === 1 ? 'mysql_fetch_assoc' : 'mysql_fetch_object';
      	$obj = strips(@$func($result));
		return empty($obj)?array():$obj;
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 执行SQL命令
     *
     * @access      public
     * @param       string    $sql    SQL命令
     * @param       resource  $link   数据库连接标识
     * @return      mixed             数据库结果集
     */
    public function query($sql = '', $link = NULL) {
        $this -> queryCount++;
        $sql = empty($sql) ? $this -> sql : $sql;
        $link = is_null($link) ? $this -> link : $link;
        if (DB::$debug) {
            mlog("start-query-mysql");
        }
        $this -> result = mysql_query($sql, $link);
        if (DB::$debug) {
            mlog("end-query-mysql");
            $log = array("SQL语句" => $sql, "结果" => $this -> result);
            mlog($log);
        }
		if(C("OPEN_SQL_LOG")){
			$admin = session("admin");
			$log  = array("sql"=>$sql);
			if(!empty($admin)){
				$log["admin"]=session('admin');
			}
			Log::write(json_encode($log));
		}
        if (is_resource($this -> result)) {
            return $this -> result;
        }
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 执行SQL命令
     *
     * @access      public
     * @param       string    $sql    SQL命令
     * @param       resource  $link   数据库连接标识
     * @return      bool              是否执行成功
     */
    public function execute($sql = '', $link = NULL) {
        $this -> queryCount++;
        $sql = empty($sql) ? $this -> sql : $sql;
        $link = is_null($link) ? $this -> link : $link;
        if (Db::$debug) {
            $log = array("SQL语句" => $sql);
            mlog($log);
        }
		if(C("OPEN_SQL_LOG")){
			$admin = session("admin");
			$log  = array("sql"=>$sql);
			if(!empty($admin)){
				$log["admin"]=session('admin');
			}
			Log::write(json_encode($log));
		}
        if (mysql_query($sql, $link)) {
            return TRUE;
        }
        return FALSE;
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 插入记录
     *
     * @access public
     * @param  array  $data  插入的记录, 格式:array('字段名'=>'值', '字段名'=>'值');
     * @param  string $table 表名
     * @return bool          当前记录id
     */
    public function add($data, $table = NULL) {
        $table = is_null($table) ? $this -> table : $table;
        $sql = "INSERT INTO `{$table}`";
        $fields = $values = array();
        $field = $value = '';
        //遍历记录, 格式化字段名称与值
        foreach ($data as $key => $val) {
            $fields[] = "`{$table}`.`{$key}`";
            $values[] = "'".mysql_real_escape_string($val)."'";
        }
        $field = join(',', $fields);
        $value = join(',', $values);
        unset($fields, $values);
        $sql .= "({$field}) VALUES({$value})";
        $this -> sql = $sql;
        $this -> execute();
        $this -> options = array();
        return $this -> insertId();
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 批量插入记录 最多一次可以插入5000条记录
     *
     * @access public
     * @param  array  $datas  插入的记录, 格式:array(array('字段名'=>'值', '字段名'=>'值'));
     * @param  string $table 表名
     * @return bool
     */
    public function adds($datas, $table = NULL) {
        $table = is_null($table) ? $this -> table : $table;
        $sql = "INSERT INTO `{$table}`";
        $fields = $values = array();
        $field = $value = '';
        $vals = array();
        foreach ($datas as $dk => $data) {
            //遍历记录, 格式化字段名称与值
            foreach ($data as $key => $val) {
                $fields[] = "`{$table}`.`{$key}`";
                $values[] = "'{$val}'";
            }
            $field = join(',', $fields);
            $value = join(',', $values);
            unset($fields, $values);
            $vals[] = '(' . $value . ')';
        }

        $sql .= "({$field}) VALUES " . implode(",", $vals);
        $this -> sql = $sql;
        $this -> execute();
        $this -> options = array();
        return true;
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 删除记录
     *
     * @access public
     * @param  string  $where  条件
     * @param  string  $table  表名
     * @return bool            影响行数
     */
    public function delete($where = NULL, $table = NULL) {
        $table = is_null($table) ? $this -> table : $table;
        $where = is_null($where) ? $this -> options['where'] : $where;
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $this -> sql = $sql;
        $this -> execute();
        $this -> options = array();
        return $this -> affectedRows();
    }

    //---------------------- 华丽分割线 ------------------------

    /**
     * 更新记录
     *
     * @access public
     * @param  array   $data   更新的数据 格式:array('字段名' => 值);
     * @param  string  $where  更新条件
     * @param  string  $table  表名
     * @return bool            影响多少条信息
     */
    public function update($data, $where = NULL, $table = NULL) {
        $table = is_null($table) ? $this -> table : $table;
        $where = is_null($where) ? $this -> options['where'] : $where;
        $sql = "UPDATE `{$table}` SET ";
        $values = array();
        foreach ($data as $key => $val) {
            $val = is_numeric($val) ? $val : "'{$val}'";
            $values[] = "`{$table}`.`{$key}` = {$val}";
        }
        $value = join(',', $values);
        $this -> sql = $sql . $value . " WHERE {$where}";
        $this -> execute();
        $this -> options = array();
        return $this -> affectedRows();

    }
	
	/**
	 * 开始一个事务.
	 */
	public function begin(){		
		mysql_query("SET AUTOCOMMIT=0");			
	    mysql_query('begin');	
	}
	
	/**
	 * 提交一个事务.
	 */
	public function commit(){
	    mysql_query('commit');
		mysql_query("SET AUTOCOMMIT=1");
	}
	
	/**
	 * 回滚一个事务.
	 */
	public function rollback(){
	    mysql_query('rollback');
		mysql_query("SET AUTOCOMMIT=1");
	}

    //---------------------- 华丽分割线 ------------------------

    /**
     * 缓存当前查询
     *
     * @access      public
     * @param       string    $name   缓存名称
     * @param       int       $time   缓存有效时间, 默认为60秒
     * @param       string    $path   缓存文件存放路径
     * @return      object            数据库操作对象
     */
    public function cache($name = '', $time = 60, $path = '') {
        $this -> options['cache'] = TRUE;
        $this -> options['cacheTime'] = $time;
        $this -> options['cacheName'] = empty($name) ? md5($this -> sql) : $name;
        $this -> options['cachePath'] = empty($path) ? $this -> cachePath : $path;
        return $this;
    }

    //---------------------- 华丽分割线 ------------------------

    //自动加载函数, 实现特殊操作
    public function __call($func, $args) {
        if (in_array($func, array('field', 'join', 'where', 'order', 'group', 'limit', 'having'))) {
            $this -> options[$func] = array_shift($args);
            return $this;
        } elseif ($func === 'table') {
            $this -> options['table'] = array_shift($args);
            $this -> table = $this -> options['table'];
            return $this;
        }
        if (DB::$debug) {
            //如果函数不存在, 则抛出异常
            exit('Call to undefined method Db::' . $func . '()');
        }
    }

    //返回上一次操作所影响的行数
    public function affectedRows($link = null) {
        $link = is_null($link) ? $this -> link : $link;
        return mysql_affected_rows($link);
    }

    //返回上一次操作记录的id
    public function insertId($link = null) {
        $link = is_null($link) ? $this -> link : $link;
        return mysql_insert_id($link);
    }

    //清空结果集
    public function free($result = null) {
        $result = is_null($result) ? $this -> result : $result;
        return mysql_free_result($result);
    }

    //返回错误信息
    public function getError($link = NULL) {
        $link = is_null($link) ? $this -> link : $link;
        return mysql_error($link);
    }

    //返回错误编号
    public function getErrno($link = NULL) {
        $link = is_null($link) ? $this -> link : $link;
        return mysql_errno($link);
    }

}

/**
 * 设置当前操作表
 */
function M($table = null, $dbname = "DEFAULT_DB") {
    $db = DB::getInstance($dbname);
    if ($table) {
        $db -> table(C("$dbname.DB_PREFIX") . $table);
    }
    return $db;
}

/**
 * 设置当前操作数据库
 */
function D($dbname = "DEFAULT_DB") {
    $db = DB::getInstance($dbname);
    return $db;
}


 
