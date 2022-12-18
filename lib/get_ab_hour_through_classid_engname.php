<?php
	require_once("db_opt.php");
	$conn=db_conn("daresay_db");
	$str=$_GET["classid_engname"];
	list($classid,$engname)=explode(";",$str);
	//read the class record info and get the hour
	$table_name="absent";
	$sql="SELECT * FROM {$table_name} WHERE classid='$classid' and engname='$engname'";
	$result=mysql_query($sql,$conn);
	$ab_hour="";
	while ($row = mysql_fetch_assoc($result)) {
		if ($ab_hour=="")
			$ab_hour=$row['ab_hour'];
		else
			$ab_hour=$ab_hour.";".$row['ab_hour'];
	}
	echo $ab_hour;
	mysql_close($conn);
?>