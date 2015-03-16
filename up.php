<?php
	header("Content-Type:text/html;charset=GBK");
	//echo "<pre>";
		print_r($_FILES);
		if($_FILES["wen"]["error"]==UPLOAD_ERR_OK){
				move_uploaded_file($_FILES["wen"]["tmp_name"],"./uploads/".$_FILES['wen']['name']);
				if(is_file("./uploads/".$_FILES['wen']['name'])){
					echo "上传成功！";
				}
		}
		//echo $_FILES['wen']['name'][0];
		//print_r($_POST);
	//echo "</pre>";
?>