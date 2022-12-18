<?php
								//未来我们的Online系统可以分为4类账户：学生，管理者，老师，销售
								require 'public/Initialize.php';
								//require 'public/http_status.php';
								require_once("public/db_opt.php");
								$classid=$_POST["Classid"];
								$engname=$_POST["EngName"];
								//$classid="k1003";
								//$engname="jason";
								//check if have the same engname in db
								if (strcmp($classid, "teacher") == 0) {
									$res=array("classid"=>$classid, "engname"=>$engname);
									header('Content-Type: application/json');
									$return=json_encode($res);
									echo $return;
								} else {
									$conn=db_conn("daresay_db");
									$sql="SELECT * FROM online_user WHERE engname='$engname' and classid='$classid'";
									$result=mysql_query($sql,$conn);
									if (!$result) {
										http_response_code(400);
									}

									$row = mysql_fetch_assoc($result);
									if ($row) {
										//echo $row;
										//echo $classid."班已经存在英文名字为".$engname."的学生!请重新输入!";
										header('Content-Type: application/json');
										//$res=array("classid"=>$row['classid'], "engname"=>$row['engname']);
										$return=json_encode($row);
										echo $return;
										//print_r($row);
										//print_r($return);
										//http_response_code(200);

									} else {
										http_response_code(400);
									}
								}
								mysql_close($conn);
								exit;
							