<?php
	require_once("db_opt.php");
	$conn=db_conn("daresay_db");
	$classid=$_GET["hour"];
	//read the class record info and get the hour
	$table_name="class_info_record";
	$sql="SELECT * FROM {$table_name} where classid='$classid'";
	$result=mysql_query($sql,$conn);
	$big_hour=0;
	while ($row = mysql_fetch_assoc($result)) {
		$tmp_hour=$row['hour'];
		list($fir_hour,$sec_hour)=explode("-",$tmp_hour);
		if ($big_hour < $fir_hour) {
			$big_hour=$fir_hour;	
			$record_hour=$tmp_hour;
		}
	}
	echo $record_hour;
	mysql_close($conn);
?>