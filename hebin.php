<?php
 include"./PDO_MYSQL.php";
	//mergeFile("./Uploads/hebin.avi");
	date_default_timezone_set('PRC');//中国
	function mergeFile($targetFile){
		$file=fopen($targetFile,"wb");
		$num=0;
		while($num<22){
			$url="./Uploads/44_1.avi.".$num.".silice";
			$cfile=fopen($url, "rb");
			$content=fread($cfile,filesize($url));
			fclose($cfile);
			fwrite($file, $content);
			$num++;
		}
		fclose($file);
	}
	$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);//设置错误为抛出异常
	try{
		$sql="select * from books;";
		//$sql="insert into books(cid, name, price, num, desn, ptime) values(3, 'php', '34.5', '10', 'good', '2132131321');"
		$sql="insert into uploadFileSlice values('file.html','1000','html','./uploads/','500','20','md5','2014');";
		//$result=$pdo->exec($sql);
		$result=$pdo->query($sql);
		print_r($result);
	}catch(PDOException $e){
			echo "数据库链接失败：".$e->getMessage();
			exit;
	}
	echo "fgdf";
	
?>