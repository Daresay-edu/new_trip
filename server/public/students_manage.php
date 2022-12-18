<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title>DareSay Education</title>
<style type="text/css">
table
{
	border-collapse:collapse;
}
table, tr, td
{
border: 1px solid;
}
.title td
{
	text-align:center;
width:140px;
}
.content td
{
	text-align:center;
width:140px;
height:57px;
}
</style>
</head>

<?php
require_once("../lib/db_opt.php");
//$mysql_server_name='localhost';
//$mysql_username='root';
//$mysql_password='daresay2014';
//$mysql_database='daresay_db';
//$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password,$mysql_database);
//if (!$conn)
//{
//	die('Could not connect: ' . mysql_error());
//}
//
//$db_selected = mysql_select_db("daresay_db",$conn);
$conn=db_conn("daresay_db");
$sql="SELECT * FROM students WHERE id='2';";
//$sql="INSERT INTO students (id, classid, name, englishname, age, phone, charge, pay_time, hour_num) 
//	VALUES ('2', 'K2001', '古朴', 'Benson', '9', '13502005710', '1850', '2015-08-23', '48');";
$result=mysql_query($sql,$conn);
if (!$result) 
	die("SQL: {$sql}<br>Error:".mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	echo $row['name'];
}
mysql_close($conn);
echo "Hello!数据库mycounter已经成功建立！";
?>
