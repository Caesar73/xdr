<?php
/**
 * 基层Dao层
 */
class BaseDao{
	
	private $tableName;
	private $dbname;
	
    /**
     * 创建基础数据库DAO应用
     * @Param tableName 数据表名称
     * @param dbname 数据库链接名 默认是 DEFAULT_DB
     */
	function __construct($tableName,$dbname="DEFAULT_DB") {
		$this->tableName = $tableName;
		$this->dbname = $dbname;
	}
	
	/**
     * 添加单条记录
     */
    public function add($data){
        $db = M($this->tableName,$this->dbname);
        return $db->add($data);
    }
	
    /**
     * 更新记录
     */
    public function update($id,$data,$idName="id"){
        $db = M($this->tableName,$this->dbname);
        return $db->where($idName."=".$id)->update($data);
    }
    
    /**
     * 通过条件更新记录
     */
    public function updateByWhere($where,$data){
        $db = M($this->tableName,$this->dbname);
        return $db->where($where)->update($data);
    }
    
    /**
     * 获取所有记录内容
     */
    public function findAll($filter="1=1",$order=""){
        $db = M($this->tableName,$this->dbname);
		if(empty($order)){
	        return $db->where($filter)->findAll();
		}else{
	        return $db->where($filter)->order($order)->findAll();
		}
    }
    
    /**
     * 通过编号获取内容
     */
    public function findById($id,$idName="id"){
        return $this->find($idName."=".$id);
    }
	
	/**
	 * 查询单条记录
	 */
	public function find($filter){
	   $db = M($this->tableName,$this->dbname);
	   return $db->where($filter)->find();
	}
	
	/**
	 * 根据编号删除记录
	 */
	public function delete($id,$idName="id"){
        $db = M($this->tableName,$this->dbname);
		return $db->delete($idName."=".$id);
	}
	
	/**
	 * 通过条件删除记录
	 */
	public function deleteByWhere($where){
		$db = M($this->tableName,$this->dbname);
		return $db->delete($where);
	}
	
    /**
     * 获取记录数
     */
	public function num($fileter="1=1"){
		$db = M($this->tableName,$this->dbname);
		return $db->count($fileter);
	}
	
    /**
     * 分页获取记录
     */
	public function findByPage($filter="1=1",$pageCount=20,$pageIndex=1,$order="id desc"){
		$db = M($this->tableName,$this->dbname);
		$start = ($pageIndex-1)*$pageCount;
		$data = $db->where($filter)->order($order)->limit($start.",".$pageCount)->findAll();
		foreach ($data as $key => $value) {
			$data[$key]["index"]=$start+$key+1;
		}
		$count = $db->count($filter);
		$res = array(
			"data"=>$data,
			"totalCount"=>$count,
			"pageIndex"=>$pageIndex,
			"pageCount"=>$pageCount
		);
		return $res;
	}
	
	/**
	 * 开始一个事务.
	 */
	public function beginTransaction(){
		$db = M($this->tableName,$this->dbname);		
		$db->begin();	    
	}
	
	/**
	 * 提交一个事务.
	 */
	public function commitTransaction(){
	    $db = M($this->tableName,$this->dbname);
		$db->commit();	
	}
	
	/**
	 * 回滚一个事务.
	 */
	public function rollbackTransaction(){
	    $db = M($this->tableName,$this->dbname);
		$db->rollback();		
	}
}
