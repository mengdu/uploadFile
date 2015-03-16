<?php
	header("Content-Type:text/html;charset=GBK");
		print_r($_POST);
		print_r($_FILES);
		
		if($_FILES["wen"]["error"]==UPLOAD_ERR_OK){
				move_uploaded_file($_FILES["wen"]["tmp_name"],"./uploads/".$_POST["fileName"].".".$_POST["blodNum"].".silice");
				if(is_file("./uploads/".$_FILES['wen']['name'])){
					echo "上传成功！";
				}
		}
	//echo "</pre>";
?>