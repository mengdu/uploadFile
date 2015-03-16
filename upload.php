<!doctype html>
<htmL>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
	<style>
		body{
			background:#000000;
			color:#159800;
			
		}
	</style>
</head>
<body>
<?php
	//header("Content-Type:text/html;charset=UTF-8");
	require "file_upload.class.php";
	$c=array('maxsize'=>pow(2,20)*200,'filepath'=>'./Upload',
	'allowtype'=>array('doc','xls','txt','jpg','gif','png','dmp','apk','mp3','mp4','html','php','wmv','zip','rar','iso'));
	$up=new FileUpload($c);
	echo "<pre>";
	if($up->uploadFile('wen')){//传入表单的name
		//echo "<a href='".'./upload/'.$up->getNewFileName()."'>".$up->getNewFileName().'</a><br/>';
		foreach($up->getNewFileName() as $furl)
		{
			echo "<a href='".'./upload/'.$furl."'>".$furl.'</a><br/>';
		}
	}else{
		print_r($up->getErrorMsg());
		
	}

var_dump($up);
echo "</pre>";
	、/*echo "<pre>";
		print_r($_FILES);
		if($_FILES["wen"]["error"][0]==UPLOAD_ERR_OK){
				move_uploaded_file($_FILES["wen"]["tmp_name"][0],"./uploads/".$_FILES['wen']['name'][0]);
				if(is_file("./uploads/".$_FILES['wen']['name'][0])){
					echo "上传成功！";
				}
		}
		//echo $_FILES['wen']['name'][0];
	echo "</pre>";	*/
?>
</body>
</html>