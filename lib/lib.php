<?php
include("err.php");
require_once("db_opt.php");
require_once "email.class.php";
#require_once("../phpmail/sendmail_interface.php");
########### functions for school ###############
function child_school_query() {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM child_school";
	$result=mysqli_query($conn, $sql);
	if (!$result) {
		$errmsg = "查询分校信息失败！";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		mysqli_close($conn);
		return $return;
	} else {
		$jarr = array();
		while ($rows=mysqli_fetch_array($result,MYSQLI_ASSOC)){
			array_push($jarr,$rows);
		}
		$return[] = DX_SUCCESS;
		$return[] = $jarr; 
		mysqli_close($conn);
		return $return;
	}
}
function get_all_school () {
	$return = array();
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM school";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "get all school failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		goto go_out;
	}

	$ret_arr = array();
	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$ret_arr[$i++] = $row; 
	}

	$return[] = DX_SUCCESS;
	$return[] = $ret_arr; 
go_out:
	mysql_close($conn);
	return $return;

}
########### functions for class ###############
function get_current_hour($classid, $return_sec_hour){
	require_once("db_opt.php");
	$conn=db_conn("daresay_db");
	//read the class record info and get the hour
	$table_name="class_info_record";
	$sql="SELECT * FROM {$table_name} where classid='$classid'";
	$result=mysql_query($sql,$conn);
	$big_hour=0;
	$record_hour=0;
	$record_hour_sec = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$tmp_hour=$row['hour'];
		$tmp_date=$row['date'];
		list($fir_hour,$sec_hour)=explode("-",$tmp_hour);
		if ($big_hour < $fir_hour) {
			$big_hour=$fir_hour;	
			$record_hour=$tmp_hour;
			$record_hour_sec = $sec_hour;
		}
	}
	mysql_close($conn);
	if ($return_sec_hour) {
		return $record_hour_sec;
	}else {
		return $record_hour;
	}
}	
function is_same_level_class($classid, $classid1)
{
    if (!strcmp(substr($classid, 0, 2), substr($classid1, 0, 2)))
	    return True;
    else
	    return False;
}
function who_need_pay($classid){
	require_once("db_opt.php");
	$conn=db_conn("daresay_db");
	$needpay_students=array();
	$i=0;

	$sql="SELECT * FROM students where classid='$classid'";
	$result=mysql_query($sql,$conn);
	$cur_hour = get_current_hour($classid, True);
	while ($row = mysql_fetch_assoc($result)) {
		$need_pay=$row['hour_end'];
		$engname=$row['engname'];
		if ($need_pay - $cur_hour<= 10) {
			$needpay_students[$i]=$row["engname"];
			$i++;
		}
	}
	mysql_close($conn);
	return $needpay_students;
}	
function get_stu_absent_by_classid($classid)
{
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM absent where classid='$classid'";
	$result=mysql_query($sql,$conn);
	$absents = array();
	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$absents[$i] = $row; 
		$i++;
	}
	mysql_close($conn);
	return $absents;
}
function get_all_stu_absents()
{
	require_once("db_opt.php");
	$conn=db_conn("daresay_db");
	$all_absents = array();
	$sql="SELECT * FROM absent";
	$result=mysql_query($sql,$conn);
	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$all_absents[$i] = $row;
		$i++;
	}
	mysql_close($conn);
	return $all_absents;
}
function who_need_makeup_thisweek($target_classid){
	# get all running class, makeup will happend in running class
	$cur_hour = get_current_hour($target_classid, True);
	require_once("db_opt.php");
	$conn=db_conn("daresay_db");
	$i=0;
        
	$class_info = get_class_info_byid($target_classid);
	list($fir_tm,$sec_tm) = array_pad(explode(",", $class_info['class_time'], 2), 2 , null);
        // get all absents
	$all_absents = get_all_stu_absents(); 
	$makeup_students=array();
	$index = 0;
	for ($i = 0; $i < count($all_absents); $i++){
            $find = False;
	    // filter the not same level classid and finished makeup.
            if (!is_same_level_class($target_classid, $all_absents[$i]['classid']) || !strcmp($all_absents[$i]['finish'], 'yes'))
	        continue; 

	    list($fir_ab, $sec_ab) = array_pad(explode("-", $all_absents[$i]['ab_hour'], 2), 2 , null);
	    if ($cur_hour + 2 == $sec_ab){
		    $makeup_tm = $fir_tm;
		    $find = True;
	    }
	    if ($cur_hour + 4 == $sec_ab){
		    $makeup_tm = $sec_tm;
		    $find = True;
	    }
	    if ($find) {
	        $makeup_students[$index]['target_class'] = $target_classid; 
	        $makeup_students[$index]['target_class_hour'] = $cur_hour; 
	        $makeup_students[$index]['classid'] = $all_absents[$i]['classid']; 
	        $makeup_students[$index]['engname'] = $all_absents[$i]['engname']; 
	        $makeup_students[$index]['makeup_hour'] = $all_absents[$i]['ab_hour']; 
	        $makeup_students[$index]['makeup_date'] = $makeup_tm; 
	        $index++;
	    }
	}
	return $makeup_students;
}	
function is_normal_class($classid) {
		$ret=0;
		$char=substr($classid, 0, 1);
		if ($char == "K" || 
			$char == "S" || 
			$char == "k" || 
			$char == "s") {
			$ret=1;
		}
		return $ret;
			
}
function get_running_class() {
	require_once("db_opt.php");
	$conn=db_conn("daresay_db");
	$running_class =array();
	$i=0;

	$sql="SELECT * FROM class";
	$result=mysql_query($sql,$conn);
	while ($row = mysql_fetch_assoc($result)) {
		$tmp=$row['classid'];
		if (!is_normal_class($tmp)) 
			continue;
		$sql="SELECT * FROM class_info_record WHERE classid='$tmp' and hour='191-192'";
        $result1=mysql_query($sql,$conn);
		$row1 = mysql_fetch_assoc($result1);
		if ($row1)
             continue;												
		$running_class[$i]=$tmp;
		$i++;										
		
	}
	mysql_close($conn);
	sort($running_class);
	return $running_class;
}
function get_normal_class() {
	require_once("db_opt.php");
	$conn=db_conn("daresay_db");
	$running_class =array();
	$i=0;

	$sql="SELECT * FROM class";
	$result=mysql_query($sql,$conn);
	while ($row = mysql_fetch_assoc($result)) {
		$tmp=$row['classid'];
		if (!is_normal_class($tmp)) 
			continue;											
		$running_class[$i]=$tmp;
		$i++;										
		
	}
	mysql_close($conn);
	sort($running_class);
	return $running_class;
}
function get_all_class() {
	require_once("db_opt.php");
	$conn=db_conn("daresay_db");
	$running_class =array();
	$i=0;

	$sql="SELECT * FROM class";
	$result=mysql_query($sql,$conn);
	while ($row = mysql_fetch_assoc($result)) {
		$tmp=$row['classid'];											
		$running_class[$i]=$tmp;
		$i++;										
		
	}
	mysql_close($conn);
	sort($running_class);
	return $running_class;
}

function get_class_info_byid($classid){
	require_once("db_opt.php");
	$return = array ();
	$conn=db_conn("daresay_db");
	//read the class record info and get the hour
	$table_name="class";
	$sql="SELECT * FROM {$table_name} where classid='$classid'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		return null;
	}

	$row = mysql_fetch_assoc($result);
	return $row;
}	

function get_class_date($classid){
	require_once("db_opt.php");
	$return = array ();
	$conn=db_conn("daresay_db");
	//read the class record info and get the hour
	$table_name="class_info_record";
	$sql="SELECT * FROM {$table_name} where classid='$classid'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "get all class failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		goto go_out;
	}

	$tmp_date = "";
	while ($row = mysql_fetch_assoc($result)) {
		$tmp_date=$row['date'];
	}
	$return[] = DX_SUCCESS;
	$return[] = $tmp_date; 
go_out:
	mysql_close($conn);
	return $return;
}	

function print_remind_by_classid($classid) {
	if (is_normal_class($classid)) {
		static $num=1;
		$conn=db_conn("daresay_db");
		$table_name="class";
		$sql="SELECT * FROM {$table_name} WHERE classid='$classid'";
		$result=mysql_query($sql,$conn);
		if (!$result)
			die("SQL: {$sql}<br>Error:".mysql_error());	
		$row = mysql_fetch_assoc($result);
		$current_hour = get_current_hour($classid, False);
		if($current_hour == "191-192")
			return 1;

		list($errno, $class_date) = get_class_date($classid);
		if ($errno) 
			echo "get class date failed";
		
	        $school = $row['school'];
		list($fir_tm,$sec_tm) = array_pad(explode(",", $row['class_time'], 2), 2 , null);
	    echo "<h1 align='center'>".$num++." 班级: ".$classid."   课时：".$current_hour." [".$class_date."]   上课时间：".$row['class_time']."</h1>";
		echo "<table border='1' align='center'>";

		list($fir_class,$sec_class) = array_pad(explode("-", $current_hour, 2), 2 , null);
		//who need to pay
		
		//$sql="SELECT * FROM students where classid='$classid'";
		//$result=mysql_query($sql,$conn);
		//while ($row = mysql_fetch_assoc($result)) {
		//	$need_pay=$row['hour_end'];
		//	$engname=$row['engname'];
		//	if ($need_pay - $sec_class <= 10) {
		//		echo "<tr>";
					
		//			echo "<td cellpadding='20' bgcolor='7ECD8C'><B>[缴费提醒]</B>".$row['engname']."缴费到第<B>".$need_pay."</B>课时</td>";
		//			echo "<td><input type='button' id='makeup_finish' value='Pay' onClick=''/></td>";
		//		echo "</tr>";
		//	}
		//}
		
		//who need to make up
		$sql="SELECT * FROM absent";
		$result=mysql_query($sql,$conn);
		$i=0;
		while ($row = mysql_fetch_assoc($result)) {
			$ab_hour=$row['ab_hour'];
			$aclassid=$row['classid'];
			$engname=$row['engname'];
			$finish=$row['finish'];
			$cl1 = substr($classid,0,2);
			$cl2 = substr($aclassid,0,2);
			list($fir_ab, $sec_ab) = array_pad(explode("-", $ab_hour, 2), 2, null);
			if (($fir_ab <= ($fir_class+4)) && ($sec_class < $sec_ab) && ($cl1 == $cl2) && ($finish=="no")) {
				if ($fir_class+2 == $fir_ab)
					$makeup_tm = $fir_tm;
				else 
					$makeup_tm = $sec_tm;
				echo "<tr>";
					$str="[补课通知]请".$aclassid."班".$engname."于".$makeup_tm."来".$classid."(".$school."校区)补第".$ab_hour."课时，收到请回复。如果当天不能来补课，请微信中通知我，谢谢您的配合！@all";
					echo "<td>".$str."<input type='text' id='$i' value='$str'/></td>";
					echo "<td><a href='#' target='_self' id='$aclassid&$engname&$ab_hour&$classid&$i' onClick='javascript:return makeup_finish(this.id);'>DONE</a></td>";
				echo "</tr>";
			}
			$i++;
		}	
		echo "</table>";	
		return 1;
	} else {
		return 0;
	}
} 
function print_class_record_info($classid) {
	$conn=db_conn("daresay_db");
	$table_name="class_info_record";
	$sql="SELECT * FROM {$table_name} where classid='$classid' order by id desc";
	$result=mysql_query($sql,$conn);
	if (!$result)
		die("SQL: {$sql}<br>Error:".mysql_error());
	echo $classid."班课程记录"."<br/><br/>";
	echo "<table border='1' width='700' border-collapse='collapse'>";
	echo "<tr>";
		echo "<td>课时</td>";
		echo "<td>课程内容</td>";
		echo "<td>上课时间</td>";
		echo "<td>缺勤学生</td>";
		echo "<td>备注</td>";
	echo "</tr>";
	$i=1;
	while ($row = mysql_fetch_assoc($result)) {
		echo "<tr>";
		echo "<td>".$row['hour']."</td>";
		echo "<td>".$row['class_info']."</td>";
		echo "<td>".$row['date'].$row['week']."</td>";
		echo "<td>".$row['absent']."</td>";
		echo "<td>".$row['note']."</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "<br/><br/>";
							
}
########### functions for students ###############
function gen_password ($engname) {
    $tmp_ascii = "";
    for($i=0;$i<strlen($engname);$i++){
    	$tmp_ascii=$tmp_ascii.ord($engname[$i]);
    	//echo ord($engname[$i]);
    }
    return substr($tmp_ascii,1,4);
}
function student_add ($name, $engname, $age, $sex, 
                           $school, $phone, $classid, $charge, 
			   $hour_begin, $hour_end, $pay_time,$credit, $note) {
	$return = array();
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM students WHERE engname='$engname' and classid='$classid'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query students failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		goto go_out;
	}
	$row = mysql_fetch_assoc($result);
	if ($row) {
		$errmsg = "students already exist.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		goto go_out;
	}

	// insert into students table
	$sql="INSERT INTO students (name, engname, classid, age, phone,school, pay_time, 
		charge,  hour_begin, hour_end, note, sex, credit)
	      VALUES ('$name', '$engname', '$classid', '$age', '$phone', '$school', '$pay_time',
	      '$charge','$hour_begin', '$hour_end','$note', '$sex', '$credit');";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Insert students failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		goto go_out; 
	} else {
		//send mail to admin
		$where = "888";
        send_mail_to_admin("New Student From ".$where, $name."-".$age."岁-".$school."-".$phone);
		
		//insert student to online user table
		$lastday=0;
		//get passwd
		$tmp_ascii = gen_password($engname);
		$hour_begin = 0;
		$sql="INSERT INTO online_user (name, engname, classid, passwd, hour_begin, hour_end, lastday, note, access_times)
			VALUES ('$name', '$engname', '$classid','$tmp_ascii', '$hour_begin', '$hour_end','$lastday', '$note', '0');";
		$result=mysql_query($sql,$conn);
		if (!$result)
			die("SQL: {$sql}<br>Error:".mysql_error());
        $errmsg = "Success";
		$return[] = DX_SUCCESS;
		$return[] = $tmp_ascii; 
		goto go_out; 
	}
go_out:
	mysql_close($conn);
	return $return;
}
function password_modify ($classid, $engname, $new_password) {
	$return = array();
	$conn=db_conn("daresay_db");
	$sql="UPDATE online_user SET passwd='$new_password' WHERE classid='$classid' AND engname='$engname'";
										 
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "modify passowrd failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$errmsg = "Success.";
		$return[] = DX_SUCCESS;
		$return[] = $errmsg; 
		return $return;
	}
	mysql_close($conn);
}
########### functions for students ###############
function student_query () {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM students";
	$result=mysqli_query($conn,$sql);
	if (!$result) {
		$errmsg = "Query student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		mysqli_close($conn);
		return $return;
	} else {
		$jarr = array();
		while ($rows=mysqli_fetch_array($result,MYSQLI_ASSOC)){
			array_push($jarr,$rows);
		}
		$return[] = DX_SUCCESS;
		$return[] = $jarr; 
		mysqli_close($conn);
		return $return;
	}
}


########### functions for demo students ###############
function demo_student_add ($chname, $engname, $age, $gender, 
                           $school, $phone, $demo_date, 
			   $chief_teacher, $assis_teacher, $way, $state, $reception, $sale, $note) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM demo_students WHERE engname='$engname' and name='$chname'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$row = mysql_fetch_assoc($result);
                if ($row) {
			$errmsg = "Student already exist.";
			$return[] = DX_ERROR;
			$return[] = $errmsg; 
			return $return;
                } else {
			//insert students to demo_students table
			$sql="INSERT INTO demo_students (name, engname, age, gender, phone, school, 
				demo_date, chief_teacher, assis_teacher,  state, way, reception, saleman, stuid, join_into, note)
			      VALUES ('$chname', '$engname', '$age','$gender', '$phone', '$school', 
			      '$demo_date','$chief_teacher', '$assis_teacher', '$state', '$way', '$reception', '$sale', '0', '0', '$note');";
			$result=mysql_query($sql,$conn);
			if (!$result) {
				$errmsg = "Add demo student fail";
				$return[] = DX_ERROR;
				$return[] = $errmsg; 
				return $return;
			} else { 
                		$errmsg = "Success";
				$return[] = DX_SUCCESS;
				$return[] = $errmsg; 
				return $return;
			}
		}
	}
	mysql_close($conn);
}

function demo_student_query () {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM demo_students";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		mysql_close($conn);
		return $return;
	} else {
		$jarr = array();
		while ($rows=mysqli_fetch_array($result,MYSQLI_ASSOC)){
			array_push($jarr,$rows);
		}
		$return[] = DX_SUCCESS;
		$return[] = $jarr; 
		mysql_close($conn);
		return $return;
	}
}

function demo_student_query_by_name ($name, $engname) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM demo_students WHERE name='$name' and engname='$engname'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$row = mysql_fetch_assoc($result);
		$return[] = DX_SUCCESS;
		$return[] = $row; 
		return $return;
	}
	mysql_close($conn);
}
function demo_student_query_by_date ($from, $end, $state, $saleman) {
	$conn=db_conn("daresay_db");
	if ($saleman == 'admin') {
		$sql="SELECT * FROM demo_students";
	} else {
		$sql="SELECT * FROM demo_students where saleman='$saleman'";
	}
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$ret_arr = array();
		$i = 0;
		while ($row = mysql_fetch_assoc($result)) {
			$tmp_date = $row['demo_date'];
			if (strtotime($tmp_date) < strtotime($from) || strtotime($tmp_date) > strtotime($end))
				continue;
			if (strcmp($state, $row['state']) && strcmp($state, 'All'))
				continue;
			$ret_arr[$i++] = $row; 
		}

		$return[] = DX_SUCCESS;
		$return[] = $ret_arr; 
		return $return;
	}
	mysql_close($conn);
}
function demo_student_query_update_state ($state) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM demo_students";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$ret_arr = array();
		$i = 0;
		while ($row = mysql_fetch_assoc($result)) {
			$tmp_date = $row['demo_date'];
			if (strtotime($tmp_date) < strtotime($from) || strtotime($tmp_date) > strtotime($end))
				continue;
			$ret_arr[$i++] = $row; 
		}

		$return[] = DX_SUCCESS;
		$return[] = $ret_arr; 
		return $return;
	}
	mysql_close($conn);
}

function demo_student_delete ($name, $engname) {
	$conn=db_conn("daresay_db");
	$sql="DELETE FROM demo_students WHERE engname='$engname' and name='$name'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Delete demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$errmsg = "Success.";
		$return[] = DX_SUCCESS;
		$return[] = $errmsg; 
		return $return;
	}
	mysql_close($conn);
}

function demo_student_modify ($oldname, $oldengname, $chname, $engname, $age, $gender, 
                           $school, $phone, $demo_date, 
			   $chief_teacher, $assis_teacher, $way, $state, $reception, $sale, $stuid, $join_into, $note) {
	$conn=db_conn("daresay_db");
	$sql="UPDATE demo_students SET name='$chname', engname='$engname', age='$age', gender='$gender', 
		school='$school', phone='$phone', demo_date='$demo_date', 
		chief_teacher='$chief_teacher', assis_teacher='$assis_teacher', way='$way',
		state='$state', reception='$reception', saleman='$sale', stuid='$stuid', join_into='$join_into', note='$note' 
		WHERE name='$oldname' AND engname='$oldengname'";
										 
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "modify demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$errmsg = "Success.";
		$return[] = DX_SUCCESS;
		$return[] = $errmsg; 
		return $return;
	}
	mysql_close($conn);
}

function demo_student_turn_real($oldname, $oldengname, $chname, $engname, $stuid, $join_into) {
	$conn=db_conn("daresay_db");
	$sql="UPDATE demo_students SET name='$chname', engname='$engname', state='已报名', stuid='$stuid', join_into='$join_into'
		WHERE name='$oldname' AND engname='$oldengname'";
										 
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "modify demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
	} else {
		$errmsg = "Success.";
		$return[] = DX_SUCCESS;
		$return[] = $errmsg; 
	}
go_out:
	mysql_close($conn);
	return $return;
}
########### functions for parents message ###############
function parents_message_add($classid, $engname, $title, $phone, $message) {
	$return = array();
	$conn=db_conn("daresay_db");
	$sql="INSERT INTO parents_message (engname, classid, title, phone, message)
	      VALUES ('$engname', '$classid', '$title', '$phone', '$message');";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Insert message failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		goto go_out; 
	} else {

                $errmsg = "Success";
		$return[] = DX_SUCCESS;
		$return[] = $errmsg; 
		goto go_out; 
	}
go_out:
	mysql_close($conn);
	return $return;
}
########### functions for mail ###############
function send_mail($email_addr,$email_title,$email_content) {
			//$classid = $_POST["classid"];
			//$class_num = $_POST["begin_class"];
			$smtpserver = "smtp.sina.com";//SMTP服务器
			$smtpserverport =587;//SMTP服务器端口
			$smtpusermail = "daresay2014@sina.com";//SMTP服务器的用户邮箱
			$smtpuser = "daresay2014@sina.com";//SMTP服务器的用户帐号
			$smtppass = "daresay20140506";//SMTP服务器的用户密码 就是邮箱登陆密码
			$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
			
			$smtpemailto=$email_addr;
			$mailtitle=$email_title;
			$mailcontent=$email_content;
			$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
			$smtp->debug = false;//是否显示发送的调试信息
			$state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);
	
}
function send_mail_to_admin ($mail_title, $mail_content) {
	$admin_mail = "18020023616@163.com";
        send_mail($admin_mail, $mail_title, $mail_content);
} 
########### functions for teacher ###############
function get_all_teachers () {
	$return = array();
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM teachers";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "get all school failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		goto go_out;
	}

	$ret_arr = array();
	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$ret_arr[$i++] = $row; 
	}

	$return[] = DX_SUCCESS;
	$return[] = $ret_arr; 
go_out:
	mysql_close($conn);
	return $return;
} 
########### functions for JiaWei ###############
function add_report ($school, $number, $phone, $date, $type_v) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM win WHERE school='$school' and date='$date'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query report failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$row = mysql_fetch_assoc($result);
        if ($row) {
			$sql="UPDATE win SET number='$number' WHERE school='$school' AND date='$date'";
			$result=mysql_query($sql,$conn);
            $errmsg = "Success";
			$return[] = DX_SUCCESS;
			$return[] = $errmsg; 
			return $return;
        } else {
			//insert students to demo_students table
			$sql="INSERT INTO win (school, number, phone, date, type_v)
			      VALUES ('$school', '$number', '$phone','$date', '$type_v');";
			$result=mysql_query($sql,$conn);
			if (!$result) {
				$errmsg = "Add report fail";
				$return[] = DX_ERROR;
				$return[] = $errmsg; 
				return $return;
			} else { 
                $errmsg = "Success";
				$return[] = DX_SUCCESS;
				$return[] = $errmsg; 
				return $return;
			}
		}
	}
	mysql_close($conn);
}
########### functions for JiaoWei ###############
function win_query_by_date ($date, $type_v) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM win";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$ret_arr = array();
		$i=0;
		while ($row = mysql_fetch_assoc($result)) {
			if ($date==strtotime($row['date']) && $type_v == $row['type_v']) {
				$ret_arr[$i++] = $row; 
			}
		}

		$return[] = DX_SUCCESS;
		$return[] = $ret_arr; 
		return $return;
	}
	mysql_close($conn);
}
########### functions for JiaoWei ###############
function win_add_stu_report ($type_v, $date, $in1, $in2, $in3, $in4, $in5, $in6, $in7, $in8, $in9, $in10, $in11, $in12, $in13, $in14, $in15) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM win_stu WHERE stu_2='$in2' and date='$date'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query report failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$row = mysql_fetch_assoc($result);
        if ($row) {
			$sql="UPDATE win_stu SET stu_1='$in1',stu_2='$in2',stu_3='$in3',stu_4='$in4',stu_5='$in5',stu_6='$in6',stu_7='$in7',stu_8='$in8',stu_9='$in9', stu_10='$in10',stu_11='$in11',stu_12='$in12',stu_13='$in13',stu_14='$in14',stu_15='$in15' WHERE stu_2='$in2' AND date='$date'";
			$result=mysql_query($sql,$conn);
            if (!$result) {
				$errmsg = "Update report fail";
				$return[] = DX_ERROR;
				$return[] = $errmsg; 
				return $return;
			} else { 
                $errmsg = "Success";
				$return[] = DX_SUCCESS;
				$return[] = $errmsg; 
				return $return;
			}
        } else {
			//insert students to demo_students table
			$sql="INSERT INTO win_stu (stu_1, stu_2, stu_3, stu_4, stu_5, stu_6, stu_7, stu_8, stu_9, stu_10, stu_11, stu_12, stu_13, stu_14, stu_15, type_v, date)
			      VALUES ('$in1', '$in2', '$in3','$in4', '$in5', '$in6', '$in7','$in8', '$in9', '$in10', '$in11','$in12', '$in13', '$in14', '$in15', '$type_v', '$date');";
			$result=mysql_query($sql,$conn);
			if (!$result) {
				$errmsg = "Add report fail";
				$return[] = DX_ERROR;
				$return[] = $errmsg; 
				return $return;
			} else { 
                $errmsg = "Success";
				$return[] = DX_SUCCESS;
				$return[] = $errmsg; 
				return $return;
			}
		}
	}
	mysql_close($conn);
}
########### functions for JiaoWei ###############
function win_stu_query_by_date ($date, $type_v) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM win_stu";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$ret_arr = array();
		$i=0;
		while ($row = mysql_fetch_assoc($result)) {
			if ($date==strtotime($row['date']) && $type_v == $row['type_v']) {
				$ret_arr[$i++] = $row; 
			}
		}

		$return[] = DX_SUCCESS;
		$return[] = $ret_arr; 
		return $return;
	}
	mysql_close($conn);
}
########### functions for JiaoWei ###############
function win_add_teacher_report ($type_v, $date, $in1, $in2, $in3, $in4, $in5, $in6, $in7, $in8, $in9, $in10, $in11, $in12) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM win_teacher WHERE tea_2='$in2' and date='$date'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query report failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$row = mysql_fetch_assoc($result);
        if ($row) {
			$sql="UPDATE win_teacher SET tea_1='$in1',tea_2='$in2',tea_3='$in3',tea_4='$in4',tea_5='$in5',tea_6='$in6',tea_7='$in7',tea_8='$in8',tea_9='$in9', tea_10='$in10',tea_11='$in11',tea_12='$in12' WHERE tea_2='$in2' AND date='$date'";
			$result=mysql_query($sql,$conn);
            $errmsg = "Success";
			$return[] = DX_SUCCESS;
			$return[] = $errmsg; 
			return $return;
        } else {
			//insert students to demo_students table
			$sql="INSERT INTO win_teacher (tea_1, tea_2, tea_3, tea_4, tea_5, tea_6, tea_7, tea_8, tea_9, tea_10, tea_11, tea_12, type_v, date)
			      VALUES ('$in1', '$in2', '$in3','$in4', '$in5', '$in6', '$in7','$in8', '$in9', '$in10', '$in11', '$in12','$type_v', '$date');";
			$result=mysql_query($sql,$conn);
			if (!$result) {
				$errmsg = "Add report fail";
				$return[] = DX_ERROR;
				$return[] = $errmsg; 
				return $return;
			} else { 
                $errmsg = "Success";
				$return[] = DX_SUCCESS;
				$return[] = $errmsg; 
				return $return;
			}
		}
	}
	mysql_close($conn);
}
########### functions for JiaoWei ###############
function win_teacher_query_by_date ($date, $type_v) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM win_teacher";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query demo student failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$ret_arr = array();
		$i=0;
		while ($row = mysql_fetch_assoc($result)) {
			if ($date==strtotime($row['date']) && $type_v == $row['type_v']) {
				$ret_arr[$i++] = $row; 
			}
		}

		$return[] = DX_SUCCESS;
		$return[] = $ret_arr; 
		return $return;
	}
	mysql_close($conn);
}
########### functions for practise ###############
function add_practise ($grade, $subject, $hour, $question, $pic_path, $choice, $answer, $anA, $anB, $anC, $anD) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM practise WHERE grade='$grade' and subject='$subject' and hour='$hour' and question='$question'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query practise failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$row = mysql_fetch_assoc($result);
        if ($row) {
			$sql="UPDATE practise SET pic_path='$pic_path',choice='$choice',answer='$answer',choice_A='$anA',choice_B='$anB',choice_C='$anC',choice_D='$anD' WHERE grade='$grade' and subject='$subject' and hour='$hour' and question='$question'";
			$result=mysql_query($sql,$conn);
            $errmsg = "Update Success";
			$return[] = DX_SUCCESS;
			$return[] = $errmsg; 
			return $return;
        } else {
			$sql="INSERT INTO practise (grade, subject, hour, question, pic_path, choice, answer, choice_A, choice_B, choice_C, choice_D)
			      VALUES ('$grade', '$subject', '$hour','$question', '$pic_path', '$choice', '$answer','$anA', '$anB', '$anC', '$anD');";
			$result=mysql_query($sql,$conn);
			if (!$result) {
				$errmsg = "Add practise fail";
				$return[] = DX_ERROR;
				$return[] = $errmsg; 
				return $return;
			} else { 
				$errmsg = "Success";
				$return[] = DX_SUCCESS;
				$return[] = $errmsg; 
				return $return;
			}
		}
	}

	mysql_close($conn);
}
function read_practise($grade, $subject, $from, $end) {
	$conn=db_conn("daresay_db");
	
	$sql="SELECT * FROM practise WHERE grade='$grade' and subject='$subject'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "read practise fail";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	}
	$i=0;
	$arr = array();
	while ($row = mysql_fetch_assoc($result)) {
		$tmp = explode("-", $row['hour']);
		if ((int)$from <= (int)$tmp[0] && (int)$tmp[1] <= (int)$end) {
			array_push($arr, $row);
			$i++;
		}
	}
	//echo $arr;
	if ($i) {
		$num = mt_rand(0, $i-1);
		$errmsg = $arr[$num];
		//echo $num;
		//echo $errmsg;
		$return[] = DX_SUCCESS;
		$return[] = $errmsg; 
		return $return;
	} else {
		$errmsg = 'No record.';
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	}
	
	mysql_close($conn);
}
########### functions for HSS ###############
function hss_add_src ($grade, $subject, $num, $name, $src, $filepath) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM hss_src WHERE grade='$grade' and subject='$subject' and name='$name'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query source failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$row = mysql_fetch_assoc($result);
        if ($row) {
			$sql="UPDATE hss_src SET pic_path='$filepath',src='$src',num='$num' WHERE grade='$grade' and subject='$subject' and name='$name'";
			$result=mysql_query($sql,$conn);
            $errmsg = "Update Success";
			$return[] = DX_SUCCESS;
			$return[] = $errmsg; 
			return $return;
        } else {
			$sql="INSERT INTO hss_src (grade, subject, num, name, src, pic_path)
			      VALUES ('$grade', '$subject', '$num','$name','$src', '$filepath');";
			$result=mysql_query($sql,$conn);
			if (!$result) {
				$errmsg = "Add source fail";
				$return[] = DX_ERROR;
				$return[] = $errmsg; 
				return $return;
			} else { 
				$errmsg = "Success";
				$return[] = DX_SUCCESS;
				$return[] = $errmsg; 
				return $return;
			}
		}
	}

	mysql_close($conn);
}
function hss_find_src ($grade, $subject) {
	$conn=db_conn("daresay_db");
	$sql="SELECT * FROM hss_src WHERE grade='$grade' and subject='$subject'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "read src fail";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	}
	$i=0;
	$arr = array();
	while ($row = mysql_fetch_assoc($result)) {
		array_push($arr, $row);
		$i++;
		
	}
		
	mysql_close($conn);
	//echo $arr;
	if ($i) {
		$errmsg = $arr;
		//echo $num;
		//echo $errmsg;
		$return[] = DX_SUCCESS;
		$return[] = $errmsg; 
		return $return;
	} else {
		$errmsg = 'No record.';
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	}

}
function hss_del_src($grade, $subject, $name) {
	$conn=db_conn("daresay_db");
	
	$sql="SELECT * FROM hss_src WHERE grade='$grade' and subject='$subject' and name='$name'";
	$result=mysql_query($sql,$conn);
	if (!$result) {
		$errmsg = "Query source failed.";
		$return[] = DX_ERROR;
		$return[] = $errmsg; 
		return $return;
	} else {
		$row = mysql_fetch_assoc($result);
        if ($row) {
			//echo $row['name'];
			//remove picture file
			unlink($row['pic_path']);
			//echo $grade;
			$sql="DELETE  FROM hss_src WHERE grade='$grade' and subject='$subject' and name='$name'";
			$result=mysql_query($sql, $conn);
			if (!$result) {
				$errmsg = "DELETE source fail";
				$return[] = DX_ERROR;
				$return[] = $errmsg; 
				return $return;
			} else { 
				$errmsg = "Success";
				$return[] = DX_SUCCESS;
				$return[] = $errmsg; 
				return $return;
			}
		} else {
			$errmsg = "No record";
			$return[] = DX_ERROR;
			$return[] = $errmsg; 
			return $return;
		}
	}
	
	mysql_close($conn);
}
/** 
 * 创建(导出)Excel数据表格 
 * @param  array   $list        要导出的数组格式的数据 
 * @param  string  $filename    导出的Excel表格数据表的文件名 
 * @param  array   $indexKey    $list数组中与Excel表格表头$header中每个项目对应的字段的名字(key值) 
 * @param  array   $startRow    第一条数据在Excel表格中起始行 
 * @param  [bool]  $excel2007   是否生成Excel2007(.xlsx)以上兼容的数据表 
 * 比如: $indexKey与$list数组对应关系如下: 
 *     $indexKey = array('id','username','sex','age'); 
 *     $list = array(array('id'=>1,'username'=>'YQJ','sex'=>'男','age'=>24)); 
 */  
function exportExcel($list,$filename,$indexKey,$startRow=1,$excel2007=false){  
	error_reporting(0);
    //文件引入  
    require_once 'excel/PHPExcel.php';  
    require_once 'excel/PHPExcel/Writer/Excel2007.php';  
      
    if(empty($filename)) $filename = time();  
    if( !is_array($indexKey)) return false;  
      
    $header_arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M', 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');  
    //初始化PHPExcel()  
    $objPHPExcel = new PHPExcel();  
      
    //设置保存版本格式  
    if($excel2007){  
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);  
        $filename = $filename.'.xlsx';  
    }else{  
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);  
        $filename = $filename.'.xls';  
    }  
      
    //接下来就是写数据到表格里面去  
    $objActSheet = $objPHPExcel->getActiveSheet();  
    //$startRow = 1;  
    foreach ($list as $row) {  
        foreach ($indexKey as $key => $value){  
            //这里是设置单元格的内容  
            $objActSheet->setCellValue($header_arr[$key].$startRow,$row[$value]);  
        }  
        $startRow++;  
    }  
      
    // 下载这个表格，在浏览器输出  
    //header("Pragma: public");  
    //header("Expires: 0");  
    //header("Cache-Control:must-revalidate, post-check=0, pre-check=0");  
    //header("Content-Type:application/force-download");  
    //header("Content-Type:application/vnd.ms-execl");  
    //header("Content-Type:application/octet-stream");  
    //header("Content-Type:application/download");;  
    //header('Content-Disposition:attachment;filename='.$filename.'');  
    //header("Content-Transfer-Encoding:binary");  
    //$objWriter->save('php://output');  
	if (file_exists(CACHE_PATH . $filename)){
//$this->logger->error('file realpath:'.realpath(CACHE_PATH . $file_name));
header( 'Pragma: public' );
header( 'Expires: 0' );
header( 'Content-Encoding: none' );
header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
header( 'Cache-Control: public' );
header( 'Content-Type: application/vnd.ms-excel');
header( 'Content-Description: File Transfer' );
header( 'Content-Disposition: attachment; filename=' . $filename );
header( 'Content-Transfer-Encoding: binary' );
header( 'Content-Length: ' . filesize ( CACHE_PATH . $filename ) );
readfile ( CACHE_PATH . $filename );
} else {
$this->logger->error('export model :'.$id.' 错误：未生产文件');
echo '';
}
}
?>
