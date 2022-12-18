<?php                         //现有文件功能用于批量修改班级变更
												//echo "<scripts>alert(test);</scripts>";
												require_once("db_opt.php");
		                                        require_once("lib.php");
		                                        $conn=db_conn("daresay_db");
												 
					
		                                        //$table_name="absent";
												//$table_name="students";
												//$table_name="class_info_record";
												$table=array("absent", "students", "class_info_record");
												
												$len=count($table);
												for ($i=0;$i<$len;$i++) {
													$table_name=$table[$i];
													echo "<scripts>alert(".$table_name.");</scripts>";
													$oldclassid="K2017";
													$newclassid="K3017";	
                     
													$sql="SELECT * FROM {$table_name} WHERE classid='$oldclassid'";
													$result=mysql_query($sql,$conn);
												
													if (!$result)
														die("SQL: {$sql}<br>Error:".mysql_error());
                                               									
													while ($row = mysql_fetch_assoc($result)) {
													//echo "<scripts>alert(test);</scripts>";
														if (strcmp($row['classid'], $oldclassid)==0) {
															echo "<scripts>alert(".$row['classid'].");</scripts>";
															if (strcmp($table_name, "absent")==0) {
																$engname=$row['engname'];
																$ab_hour=$row['ab_hour'];
																$sql="UPDATE {$table_name} SET classid='$newclassid' WHERE engname='$engname' and classid='$oldclassid' and ab_hour='$ab_hour'";
															} else if (strcmp($table_name, "students")==0) {
																$engname=$row['engname'];
																$sql="UPDATE {$table_name} SET classid='$newclassid' WHERE engname='$engname' and classid='$oldclassid'";
															} else if (strcmp($table_name, "class_info_record")==0) {
																$hour=$row['hour'];
																$sql="UPDATE {$table_name} SET classid='$newclassid' WHERE hour='$hour' and classid='$oldclassid'";
										
															}
													  
									
															$result1=mysql_query($sql,$conn);
															if (!$result1)
																die("SQL: {$sql}<br>Error:".mysql_error());	
														}
													}
												}
												
												
?>