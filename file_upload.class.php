<?php
	/*
	*	�ļ��ϴ��� FileUpload
	*	copyright by bluemoon
	*	1752295326@qq.com
	*	2014
	*
	*/
	class FileUpload{
		
		private $filepath;//����·��
		private $allowtype=array('gif','jpg','png','jpeg','apk','zip','rar','iso');
		private $maxsize=1048576;//����1M
		private $israndname=true;//�Ƿ��������
		private $originName;//Դ�ļ���
		private $tmpFileName;//��ʱ�ļ���
		private $fileTypes;//�ļ�����
		private $fileSizes;//�ļ�����
		private $newFileName;//���ļ���
		private $errorNum=0;//�����
		private $errorMess="";//���󱨸�

		function __construct($option=array()){
			foreach($option as $key=>$val){
				$key=strtolower($key);//�ѱ���ת��Сд
				if(!in_array($key,get_class_vars(get_class($this)))){
					continue;//�������ı��������ڣ����˳�
				}
				$this->setOption($key,$val);
			}
		}
		private function setOption($key,$val){//���ó�Ա����ֵ
			$this->$key=$val;//
		}
		private function checkFilePath(){//������ļ�·��
			if(empty($this->filepath)){//�ж��Ƿ�Ϊ��
				$this->setOption('errorNum',-5);//���������Ϊ-5
				return false;
			}
			if(!file_exists($this->filepath)||!is_writable($this->filepath)){//�ж��ļ����Ƿ���ڣ�����д
				if(!@mkdir($this->filepath)){//����Ŀ¼��@��ʾ���δ�����Ϣ
					$this->setOption('errorNum',-4);//??
					return false;
				}
			}
			return true;
		}
		private function checkFileSize(){//������ļ���С
			if($this->fileSizes > $this->maxsize){//??
				$this->setOption('errorNum',-2);
				return false;
			}else{
				return true;
			}
		}
		private function checkFileType(){//������ļ�����
			if(in_array(strtolower($this->fileTypes),$this->allowtype)){//
				return true;
			}else{
				$this->setOption('errorNum',-1);
				return false;
			}
		}
		private function proRandName(){//�����ļ�����
			date_default_timezone_set('PRC');//�����й���ʱ
			//$fileName=date("YmdHis").rand(100,999).".".$this->fileTypes;
			$fileName=date("YmdHis").rand(100,999).$this->originName;
			return $fileName;
		}
		private function setnewFileName(){//�����ļ���
			if($this->israndname){
				$this->setOption('newFileName',$this->proRandName());
			}else{
				$this->setOption('newFileName',$this->originName);
			}
		}
		function uploadFile($fileFieId){//�ϴ��ļ�
			$return=true;
			//���·��
			if(!$this->checkFilePath()){
				$this->errorMess=$this->getError();//���ô�����Ϣ
				return false;
			}
			$name=$_FILES[$fileFieId]['name'];
			$tmp_name=$_FILES[$fileFieId]['tmp_name'];
			$size=$_FILES[$fileFieId]['size'];
			$error=$_FILES[$fileFieId]['error'];
			if(is_Array($name)){//���ļ��ϴ�����
				for($i=0;$i<count($name);$i++){
					if($this->setFiles($name[$i],$tmp_name[$i],$size[$i],$error[$i])){//���ø�ֵ����
						if(!$this->checkFileType()||!$this->checkFileSize()){
							$errors[]=$this->getError();
							$return=false;
						}
					}else{
						$errors[]=$this->getError();//??
						$return=false;
					}
					if(!$return){
						$this->setFiles();//���û�д������¿�
					}
				}
				if($return==true){
					$newFileNames=array();//�����洢�ļ���
					for($i=0;$i<count($name);$i++){
						$this->setFiles($name[$i],$tmp_name[$i],$size[$i],$error[$i]);//���ø�ֵ����
						$this->setnewFileName();//�����ļ���
						if($this->copyFile()){
							$return=true;
							$newFileNames[]=$this->newFileName;
						}else{
							$return=false;
							$errors=$this->getError();
						}
					}
					$this->newFileName=$newFileNames;//���ļ����������¸�ֵ
				}
				$this->errorMess=$errors;
				return $return;
			}
			
		}
		private function setFiles($name="",$tmp_name="",$size="",$error=""){//�����ļ��ϴ�����Ϣ
			$this->setOption('errorNum',$error);
			if($error){
				return false;//����ʱ�˳�����ֵ
			}
			$this->setOption('originName',$name);
			$this->setOption('tmpFileName',$tmp_name);
			$arrStr=explode('.',$name);//����ָ��ļ����õ���׺
			$this->setOption('fileTypes',strtolower($arrStr[count($arrStr)-1]));//�����ļ�����
			$this->setOption('fileSizes',$size);
			return true;
		}
		private function getError(){//��ȡ������Ϣ
			switch($this->errorNum){
				case 4: $erStr="û���ļ����ϴ���";break;
				case 3: $erStr="�ļ�ֻ�в����ϴ���";break;
				case 2: $erStr="�ļ���С�����������ƣ�";break;
				case 1: $erStr="�ļ���С�������������ƣ�";break;
				case 0: $erStr="�ϴ��ɹ���";break;
				case -1: $erStr="��������ļ����ͣ�";break;
				case -2: $erStr="�ļ�̫�󣬲��ܳ���PHP�ű����Ƶ�".toSize($this->maxsize)."��";break;//??
				case -3: $erStr="�ϴ�ʧ�ܣ�";break;
				case -4: $erStr="��������ļ�·��ʧ�ܣ�";break;
				case -5: $erStr="����ָ���ϴ��ļ�·����";break;

				default: $erStr="δ֪����";
			}
			return $erStr;
		}
		private function copyFile(){
			if($this->errorNum==0){
				$filepath=rtrim($this->filepath,'/').'/';//ɾ���û�����·���ұߵ�б��
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
		function getNewFileName(){//��ȡ���ļ�����
			return $this->newFileName;
		}
		function getErrorMsg(){//��ȡ����
			return $this->errorMess;
		}
	}

	//�ļ���С��λת��
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