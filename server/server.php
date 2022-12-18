<?php
function decode_chinese ($string) {
	return iconv('euc-cn', 'utf-8', $string);

}
function test_encoding ($string) {
	$encode = mb_detect_encoding($string, array("ASCII","UTF-8","GB2312","GBK","BIG5")); 
	echo $encode;

}
function success($data='') {
	if ($data != '') {
		$return=json_encode($data);
		echo $return;
	}
	return http_response_code(200);
}
function error($data) {
	if ($data != '') {
		$res=array("errmsg"=>$data);
		$return=json_encode($res);
		echo $return;
	}
	return http_response_code(400);
}
function return_json($data) {
	header('Content-Type: application/json');
	//$res=array("classid"=>$row['classid'], "engname"=>$row['engname']);
	$return=json_encode($row);
	echo $return;
}
	//should add all server functions here
	header("Content-type: text/html;charset=utf-8");
	require 'public/Initialize.php';
	require_once("../lib/lib.php");
	require_once("../lib/err.php");

	switch($_GET["action"]) {
	    case "child_school_query":
		list($errno, $data) = child_school_query(); 
		if ($errno) {
			return http_response_code(400);
		} else {
			header('Content-Type: application/json');
			echo json_encode($data);
		}
		break;
	    case "stu_query":
		list($errno, $data) = student_query(); 
		if ($errno) {
			return http_response_code(400);
		} else {
			header('Content-Type: application/json');
			echo json_encode($data);
		}
		break;
	    case "demo_stu_add":
        	$chname = $_POST["ch_name"];
        	$engname = $_POST["eng_name"];
        	$age = $_POST["age"];
        	$gender = $_POST["gender"];
        	$school = $_POST["school"];
        	$phone = $_POST["phone"];
        	//$demo_class = $_POST["demo_class"];
        	$demo_date = $_POST["demo_date"];
        	$chief_teacher = $_POST["chief_teacher"];
        	$assis_teacher = $_POST["assis_teacher"];
        	$way = $_POST["way"];
        	$state = $_POST["state"];
        	$sale = $_POST["sale"];

			list($errno, $data) = demo_student_add($chname, $engname, $age, $gender, $school, 
							 $phone, $demo_class, $demo_date, $chief_teacher, 
							 $assis_teacher, $way, $state, $sale); 
			if ($errno) {
				return http_response_code(400);
			} else {
				return http_response_code(200);
			}

			break;
	    case "demo_stu_query":
			list($errno, $data) = demo_student_query(); 
			if ($errno) {
				return http_response_code(400);
			} else {
				header('Content-Type: application/json');
				echo json_encode($data);
				//return json_encode($data);
			}
			break;
	    case "parents_message_add":
        	$classid = $_POST["p_classid"];
        	$engname = $_POST["p_engname"];
        	$title = $_POST["message_title"];
        	$phone = $_POST["parents_phone"];
        	$message = $_POST["message_content"];

			$mail_title = "New parent message from [".$classid." ".$engname."]";
			$mail_content = "Title: ".$title."\n\n\n\nPhone: ".$phone."\n\n\n\nMessage: ".$message;
			send_mail_to_admin($mail_title, $mail_content);

			list($errno, $data) = parents_message_add ($classid, $engname, $title, $phone, $message); 
			if ($errno) {
				return http_response_code(400);
			} else {
				return http_response_code(200);
			}
			break;
	    case "password_modify":
        	$classid = $_POST["p_classid"];
        	$engname = $_POST["p_engname"];
        	$new_password = $_POST["new_password"];

			list($errno, $data) = password_modify ($classid, $engname, $new_password); 
			if ($errno) {
				return http_response_code(400);
			} else {
				return http_response_code(200);
			}
			break;
		case "absent_status_chg":
        	$classid = $_POST["p_classid"];
        	$engname = $_POST["p_engname"];
        	$new_password = $_POST["new_password"];

			list($errno, $data) = password_modify ($classid, $engname, $new_password); 
			if ($errno) {
				return http_response_code(400);
			} else {
				return http_response_code(200);
			}
			break;
		case "win_add":
        	$school=$_POST["school"];
			$number=$_POST["number"];
			$phone=$_POST["phone"];
			$type_v=$_POST["tval"];
        	$date=date("Y-m-d");
    
			list($errno, $data) = add_report ($school, $number, $phone, $date, $type_v); 
			if ($errno) {
				return http_response_code(400);
			} else {
				return http_response_code(200);
			}
			break;
		case "win_query":
        	$school=$_POST["school"];
			$number=$_POST["number"];
			$phone=$_POST["phone"];
        	$date=date('y-n-j',time());
    
			list($errno, $data) = add_report ($school, $number, $phone, $date); 
			if ($errno) {
				return http_response_code(400);
			} else {
				return http_response_code(200);
			}
			break;
		case "win_stu_add":
			$type_v=$_REQUEST["tval"];
        	$school=$_REQUEST["school"];
			$number=$_REQUEST["number"];
			$in3=$_REQUEST["input3"];
			$in4=$_REQUEST["input4"];
			$in5=$_REQUEST["input5"];
			$in6=$_REQUEST["input6"];
			$in7=$_REQUEST["input7"];
			$in8=$_REQUEST["input8"];
			$in9=$_REQUEST["input9"];
			$in10=$_REQUEST["input10"];
			$in11=$_REQUEST["input11"];
			$in12=$_REQUEST["input12"];
			$in13=$_REQUEST["input13"];
			$in14=$_REQUEST["input14"];
			$in15=$_REQUEST["input15"];
        	$date=date("Y-m-d");
    
			list($errno, $data) = win_add_stu_report ($type_v, $date, $number, $school, $in3, $in4, $in5, $in6, $in7, $in8, $in9, $in10, $in11, $in12, $in13, $in14, $in15); 
			if ($errno) {
				//echo $data;
				return http_response_code(400);
			} else {
				return http_response_code(200);
			}
			break;
		case "win_teacher_add":
			$type_v=$_REQUEST["tval"];
        	$school=$_REQUEST["input2"];
			$number=$_REQUEST["input1"];
			$in3=$_REQUEST["input3"];
			$in4=$_REQUEST["input4"];
			$in5=$_REQUEST["input5"];
			$in6=$_REQUEST["input6"];
			$in7=$_REQUEST["input7"];
			$in8=$_REQUEST["input8"];
			$in9=$_REQUEST["input9"];
			$in10=$_REQUEST["input10"];
			$in11=$_REQUEST["input11"];
			$in12=$_REQUEST["input12"];
        	$date=date("Y-m-d");
    
			list($errno, $data) = win_add_teacher_report ($type_v, $date, $number, $school, $in3, $in4, $in5, $in6, $in7, $in8, $in9, $in10, $in11, $in12); 
			//list($errno, $data) = win_add_teacher_report ("1", "2020", "1", "1", "1", "1", "1", "1", "1", "1", "1", "1", "1"); 
			if ($errno) {
				echo $data;
				return http_response_code(400);
			} else {
				return http_response_code(200);
			}
			break;
		case "add_practise":
			$grade = $_POST["grade"];
			$subject = $_POST["subject"];
			$hour = $_POST["hour"];
			$question = $_POST["question"];
			$has_pic = $_POST["has_pic"];
			$choice = $_POST["choice"];
			$answer = $_POST["answer"];
			$anA = $_POST["anA"];
			$anB = $_POST["anB"];
			$anC = $_POST["anC"];
			$anD = $_POST["anD"];
			$filepath = 'nofile';
			if ($has_pic == 'yes') {
				$imgname = $_FILES['pic']['name'];
				$tmp = $_FILES['pic']['tmp_name'];
				$filepath = 'photo/'.$grade."-".$subject."-".$hour."-".$imgname;
				if ($_FILES['pic']['error']!=0) {
					//return http_response_code(400);
					error("Get file failed from client.");
				}
			
				if(!move_uploaded_file($tmp, $filepath)){
					//return http_response_code(400);
					error("Save file failed.");
				}
			}
			
			list($errno, $data) = add_practise($grade, $subject, $hour, $question, $filepath, $choice, $answer, $anA, $anB, $anC, $anD);
			if ($errno) {
				//echo $data;
				error($data);
			} else {
				success('');
			}
			
			break;
		case "read_practise":
			$grade = $_REQUEST["grade"];
			$subject = $_REQUEST["subject"];
			$from = $_REQUEST["from"];
			$end = $_REQUEST["end"];
			
			list($errno, $data) = read_practise($grade, $subject, $from, $end);
			if ($errno) {
				//echo $data['question'];
				error($data);
			} else {
				success($data);
			}
			
			break;
		case "hss_add_src":
			$grade = $_POST["grade"];
			$subject = $_POST["subject"];
			$num = $_POST["num"];
			$name = $_POST["name"];
			$src = $_POST["src"];
			
			$imgname = $_FILES['pic']['name'];
			$tmp = $_FILES['pic']['tmp_name'];
			$filepath = 'photo/hss/'.$grade."-".$subject."-".$name."-".$imgname;
			if ($_FILES['pic']['error']!=0) {
					//return http_response_code(400);
				error("Get file failed from client.");
			}
			
			if(!move_uploaded_file($tmp, $filepath)){
				//return http_response_code(400);
				error("Save file failed.");
			}
			$root = "http://daresay.gz01.bdysite.com/internal_0506/server/";
			list($errno, $data) = hss_add_src($grade, $subject, $num, $name, $src, $root.$filepath);
			if ($errno) {
				//echo $data;
				error($data);
			} else {
				success('');
			}
			break;
		case "hss_find_src":
			$grade = $_REQUEST["grade"];
			$subject = $_REQUEST["subject"];
			if($grade=="primary")
				$grade="小学";
			else if($grade=="junior") 
				$grade="初中";
			else if($grade=="senior") 
				$grade="高中";
			
			if($subject=="grammer")
				$subject="语法";
			else if($subject=="phonetic")
				$subject="音标";
			else if($subject=="listening")
				$subject="53听力";
			
			list($errno, $data) = hss_find_src($grade, $subject);
			if ($errno) {
				//echo $data['question'];
				error($data);
			} else {
				success($data);
			}
			
			break;
		case "hss_del_src":
			$grade = $_POST["grade"];
			$subject = $_POST["subject"];
			$name = $_POST["name"];
						
			list($errno, $data) = hss_del_src($grade, $subject, $name);
			if ($errno) {
				//echo $data;
				error($data);
			} else {
				success('');
			}
			
			break;
		case "class_current_hour":
			$classid = $_REQUEST["classid"];
		        $cur_hour = get_current_hour($classid, True);	
			success($cur_hour);
			break;
		case "who_need_pay":
		        $running_class = get_running_class();	
			$cnt = count($running_class);
			$all = array();
                        for($i=0;$i<$cnt;$i++){
		            $each_class = who_need_pay($running_class[$i]);
			    if (count($each_class)) {
				$all[$running_class[$i]] = $each_class;
			    }
                        }
			success($all);
			break;
		// tell me each monday
		case "who_need_makeup_this_week":
			$running_class = get_running_class();
                        $cnt = count($running_class);
                        $all = array();
                        //$absents = get_all_stu_absents();
                        //success($absents);
			$j = 0;
                        for($i=0;$i<$cnt;$i++){
		            $each_makeup = who_need_makeup_thisweek($running_class[$i]);
			    if (count($each_makeup)) {
				$all[$running_class[$i]] = $each_makeup;
			    }
                        }
			//sort by absent student classid
                        success($all);
			break;
	}
										
	
								
