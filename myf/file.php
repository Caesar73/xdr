<?php
// +----------------------------------------------------------------------
// | MyfPHP 闵益飞PHP MVC框架  -- 文件读写类
// +----------------------------------------------------------------------
// | Copyright (c) 2012 http://www.minyifei.cn All rights reserved.
// +----------------------------------------------------------------------
// | 交流论坛：http://bbs.minyifei.cn
// +----------------------------------------------------------------------
class File {
	
	/**
	 * 写文件
	 * @filename 文件路径
	 * @content 内容
	 */
	public function write($filename,$content){
		$dir = dirname($filename);
		is_dir($dir) or (createFolders(dirname($dir)) and mkdir($dir, 0777));
		
		@$fp = fopen($filename, "w");
		if(!$fp){
			return false;
		}else{
			fwrite($fp, $content);
			fclose($fp);
			return true;
		}
	}
	
	/**
	 * 读取文件内容
	 */
	public function read($filename){
		@$fp = fopen($filename, "r");
		if(!$fp){
			return null;
		}else{
			$content = fread($fp, filesize($filename));
			fclose($fp);
			return $content;
		}
	}
	
	/**
	 * 删除文件
	 */
	public function delete($filename){
		$res = @unlink($filename);
		if($res){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 读取目录下的文件名
	 */
	public static function  filelist($dir,$pattern=""){
		$arr=array();
		$dir_handle=opendir($dir);
		if($dir_handle)
		{
			// 这里必须严格比较，因为返回的文件名可能是“0”
			while(($file=readdir($dir_handle))!==false)
			{
				if($file==='.' || $file==='..')
				{
					continue;
				}
				$tmp=realpath($dir.'/'.$file);
				if(is_dir($tmp))
				{
					$retArr=file_list($tmp,$pattern);
					if(!empty($retArr))
					{
						$arr[]=$file;
					}
				}
				else
				{
					if($pattern==="" || preg_match($pattern,$tmp))
					{
						$arr[]=$file;
					}
				}
			}
			closedir($dir_handle);
		}
		return $arr;
	}
	
	
	public static function readCache2($filename){
		$file = dirname(APP_SYS_PATH)."/runtime/data/".$filename.'.php';
		if(file_exists($file))
		{
			$row = include $file;
			return $row;
		}
		return array();
	}
	
	
	/**
	 * 写入缓存
	 *
	 * @param string $filename
	 * @param       mixed   $data   缓存内容
	 * @return      bool            是否写入成功
	 */
	public static function writeCache($filename,$data)
	{
		$file = dirname(APP_SYS_PATH)."/runtime/data/".$filename.'.php';
		$dir = dirname($file);
		is_dir($dir) or (createFolders(dirname($dir)) and mkdir($dir, 0777));
		$data = '<?php return ' . var_export($data, TRUE) . '; ?>';
		return file_put_contents($file, $data);
	}
	
	
	/**
	 * 读取缓存
	 *
	 * @access      public
	 * @return      mixed   如果读取成功返回缓存内容, 否则返回NULL
	 */
	public static  function readCache($filename)
	{
		$file = dirname(APP_SYS_PATH)."/runtime/data/".$filename.'.php';
		if(file_exists($file))
		{

			$row = include $file;
			return $row;
		}
		return array();
	}
	
	
	
}


?>