<?php
								require 'public/Initialize.php';
								//require 'public/http_status.php';
								require_once("public/db_opt.php");
								$classid=$_POST["http_classid"];
								$lastday=$_POST["http_lastday"];
								
								//compare the lastday for students to use this system with today
								$err=0;
								if (strcmp($lastday, "0")) {
									$today=date("y-m-d");
									if (strtotime($lastday) < strtotime($today)) {
										$err=1;
									}
								}
								//$classid="k1006";
								$conn=db_conn("daresay_db");
								$table_name="class_info_record";
								$sql="SELECT * FROM {$table_name} where classid='$classid'";
								$result=mysql_query($sql,$conn);
								if(!$result)
									http_response_code(400);
								else {
									$big_hour=0;
									while ($row = mysql_fetch_assoc($result)) {
										$tmp_hour=$row['hour'];
										list($fir_hour,$sec_hour)=explode("-",$tmp_hour);
										if ($big_hour < $fir_hour) {
											$big_hour=$fir_hour;	
											$record_hour=$tmp_hour;
										}
									}
									$array=explode("-",$record_hour);
									if (intval($array[1]) == 192)
										$err=1;
									header('Content-Type: application/json');
									$current_hour=array("err"=>$err, "hour"=>intval($array[1]));
									echo json_encode($current_hour);
								}
								mysql_close($conn);
								exit;