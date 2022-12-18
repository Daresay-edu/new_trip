<?php
	require_once("db_opt.php");
	$conn=db_conn("daresay_db");
	$classid=$_GET["mem"];
	//read the class record info and get the hour
	$table_name="students";
	$sql="SELECT * FROM {$table_name} WHERE classid='$classid'";
	$result=mysql_query($sql,$conn);
	$mem="";
	while ($row = mysql_fetch_assoc($result)) {
		if ($mem=="")
			$mem=$row['engname'];
		else
			$mem=$mem.";".$row['engname'];
	}
	echo $mem;
	mysql_close($conn);
?>