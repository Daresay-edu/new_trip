<?php
								require 'public/Initialize.php';
								//require 'public/http_status.php';
								//require_once("public/db_opt.php");
								$in_grade=$_POST["in_grade"];
								$in_unit=$_POST["in_unit"];
								$p_count=$_POST["in_p_count"]; //total practice count
								$in_chosed=$_POST["in_chosed"]; //already played string such as 1&2&3...
								$in_playedcount=$_POST["in_playedcount"];//already played count
								
								$chosed_arr=explode("&", $in_chosed);
								
								$arr=range(1,$p_count);
								Shuffle($arr);
								$found=0;
								//$num=$arr[0];
								for ($i=0;$i<count($arr);$i++){
									$got_num=$arr[$i];
									$found=0;
									for ($j=0;$j<count($chosed_arr);$j++){
										if($arr[$i] == $chosed_arr[$j]) {
											$found=1;
											break;
											
										}
											
									}
									if ($found == 0)
										break;
								}
								
								
								if($found == 1) {
									$out_chosed="all";
								} else {
									$out_chosed=$in_chosed."&".$got_num;
								}
								$out_name=$in_grade."_".$in_unit."_p".$got_num;
								$out_playedcount=$in_playedcount+1;
								header('Content-Type: application/json');
								$p_name=array("Name"=>$out_name,"Chosed"=>$out_chosed, "PlayedCnt"=>$out_playedcount);
								echo json_encode($p_name);
								exit;