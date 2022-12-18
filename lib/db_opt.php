<?php

function backup_db() {
	$time=date("Y-m-d");
	$cmd="C:/xampp/mysql/bin/mysqldump.exe -uroot -pdaresay2014 b5270951>db_backup/b5270951_".$time.".sql";
	//exec("C:/xampp/mysql/bin/mysqldump.exe -uroot -pdaresay2014 b5270951>C:/xampp/htdocs/internal_0506/db_backup/b5270951.sql");
	exec($cmd);
}
function db_conn($mysql_database)

{
	ini_set('max_execution_time','1000');
	
	//$mysql_server_name='b-sjz8tiup25k9x6.bch.rds.gz.baidubce.com:3306';
	//$mysql_username='b_sjz8tiup25k9x6';
	//$mysql_password='TjuqYR3o2pg9amu2';
	//$mysql_database='b_sjz8tiup25k9x6';//云主机上的数据库名字
	$mysql_server_name='localhost';
	$mysql_username='root';
	$mysql_password='111111';
	$mysql_database='b_sjz8tiup25k9x6';//云主机上的数据库名字
	$conn=mysqli_connect($mysql_server_name,$mysql_username,$mysql_password,$mysql_database);
	if (!$conn)
	{
		die('Could not connect: ' . mysqli_connect_error());
	}
	mysqli_query("SET NAMES utf8");
	$db_selected = mysqli_select_db($mysql_database,$conn);
	return $conn;
	
}
?>
