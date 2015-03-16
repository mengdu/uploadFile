<?php
	/*
	*	文件上传类 FileUpload
	*	copyright by bluemoon
	*	1752295326@qq.com
	*	2014
	*
	*/
	class FileUpload{
		
		private $filepath;//保存路径
		private $allowtype=array('gif','jpg','png','jpeg','apk','zip','rar','iso');
		private $maxsize=1048576;//允许1M
		private $israndname=true;//是否随机名字
		private $originName;//源文件名
		private $tmpFileName;//临时文件名
		private $fileTypes;//文件类型
		private $fileSizes;//文件类型
		private $newFileName;//新文件名
		private $errorNum=0;//错误号
		private $errorMess="";//错误报告

		function __construct($option=array()){
			foreach($option as $key=>$val){
				$key=strtolower($key);//把变量转成小写
				if(!in_array($key,get_class_vars(get_class($this)))){
					continue;//如果输入的变量不存在，则退出
				}
				$this->setOption($key,$val);
			}
		}
		private function setOption($key,$val){//设置成员属性值
			$this->$key=$val;//
		}
		private function checkFilePath(){//检查问文件路径
			if(empty($this->filepath)){//判断是否为空
				$this->setOption('errorNum',-5);//将错误号设为-5
				return false;
			}
			if(!file_exists($this->filepath)||!is_writable($this->filepath)){//判断文件夹是否存在，及可写
				if(!@mkdir($this->filepath)){//创建目录，@表示屏蔽错误信息
					$this->setOption('errorNum',-4);//??
					return false;
				}
			}
			return true;
		}
		private function checkFileSize(){//检查问文件大小
			if($this->fileSizes > $this->maxsize){//??
				$this->setOption('errorNum',-2);
				return false;
			}else{
				return true;
			}
		}
		private function checkFileType(){//检查问文件类型
			if(in_array(strtolower($this->fileTypes),$this->allowtype)){//
				return true;
			}else{
				$this->setOption('errorNum',-1);
				return false;
			}
		}
		private function proRandName(){//生成文件名字
			date_default_timezone_set('PRC');//设置中国区时
			//$fileName=date("YmdHis").rand(100,999).".".$this->fileTypes;
			$fileName=date("YmdHis").rand(100,999).$this->originName;
			return $fileName;
		}
		private function setnewFileName(){//设置文件名
			if($this->israndname){
				$this->setOption('newFileName',$this->proRandName());
			}else{
				$this->setOption('newFileName',$this->originName);
			}
		}
		function uploadFile($fileFieId){//上传文件
			$return=true;
			//检查路径
			if(!$this->checkFilePath()){
				$this->errorMess=$this->getError();//设置错误信息
				return false;
			}
			$name=$_FILES[$fileFieId]['name'];
			$tmp_name=$_FILES[$fileFieId]['tmp_name'];
			$size=$_FILES[$fileFieId]['size'];
			$error=$_FILES[$fileFieId]['error'];
			if(is_Array($name)){//多文件上传操作
				for($i=0;$i<count($name);$i++){
					if($this->setFiles($name[$i],$tmp_name[$i],$size[$i],$error[$i])){//调用赋值函数
						if(!$this->checkFileType()||!$this->checkFileSize()){
							$errors[]=$this->getError();
							$return=false;
						}
					}else{
						$errors[]=$this->getError();//??
						$return=false;
					}
					if(!$return){
						$this->setFiles();//如果没有错误，重新空
					}
				}
				if($return==true){
					$newFileNames=array();//用来存储文件名
					for($i=0;$i<count($name);$i++){
						$this->setFiles($name[$i],$tmp_name[$i],$size[$i],$error[$i]);//调用赋值函数
						$this->setnewFileName();//设置文件名
						if($this->copyFile()){
							$return=true;
							$newFileNames[]=$this->newFileName;
						}else{
							$return=false;
							$errors=$this->getError();
						}
					}
					$this->newFileName=$newFileNames;//把文件名数组重新赋值
				}
				$this->errorMess=$errors;
				return $return;
			}
			
		}
		private function setFiles($name="",$tmp_name="",$size="",$error=""){//设置文件上传的信息
			$this->setOption('errorNum',$error);
			if($error){
				return false;//错误时退出设置值
			}
			$this->setOption('originName',$name);
			$this->setOption('tmpFileName',$tmp_name);
			$arrStr=explode('.',$name);//按点分隔文件名得到后缀
			$this->setOption('fileTypes',strtolower($arrStr[count($arrStr)-1]));//设置文件类型
			$this->setOption('fileSizes',$size);
			return true;
		}
		private function getError(){//获取错误信息
			switch($this->errorNum){
				case 4: $erStr="没有文件被上传！";break;
				case 3: $erStr="文件只有部分上传！";break;
				case 2: $erStr="文件大小超过表单中限制！";break;
				case 1: $erStr="文件大小超过服务器限制！";break;
				case 0: $erStr="上传成功！";break;
				case -1: $erStr="不允许的文件类型！";break;
				case -2: $erStr="文件太大，不能超过PHP脚本限制的".toSize($this->maxsize)."！";break;//??
				case -3: $erStr="上传失败！";break;
				case -4: $erStr="建立存放文件路径失败！";break;
				case -5: $erStr="必须指定上传文件路径！";break;

				default: $erStr="未知错误！";
			}
			return $erStr;
		}
		private function copyFile(){
			if($this->errorNum==0){
				$filepath=rtrim($this->filepath,'/').'/';//删除用户传入路径右边的斜线
				$filepath.=$this->newFileName;
				if(@move_uploaded_file($this->tmpFileName,$filepath)){
					return true;
				}else{
					$this->setOption('errorNum',-3);
					return false;
				}
			}else{
				return false;
			}
		}
		function getNewFileName(){//获取新文件名字
			return $this->newFileName;
		}
		function getErrorMsg(){//获取错误
			return $this->errorMess;
		}
	}

	//文件大小单位转换
	function toSize($Size){
		$dw="Bytes";
		if($Size>pow(2,30)){
			$Size=round($Size/pow(2,30),2);
			$dw="GB";
		}else if($Size>pow(2,20)){
			$Size=round($Size/pow(2,20),2);
			$dw="MB";
		}else if($Size>pow(2,10)){
			$Size=round($Size/pow(2,10),2);
			$dw="KB";
		}
		return $Size.$dw;
	}
?>