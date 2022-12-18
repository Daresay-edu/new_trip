<?php
		// this file is used to get the password for each students by english name
								require 'public/Initialize.php';
								$engname=$_POST["EngName"];
								$passwd=$_POST["AdmPD"];
								if ($passwd != "345270951") {
									http_response_code(400);
									exit;
								}
								//$engname="alice";
								$tmp_ascii="";
								for($i=0;$i<strlen($engname);$i++){
									$tmp_ascii=$tmp_ascii.ord($engname[$i]);
									//echo ord($engname[$i]);
								}

								header('Content-Type: application/json');
								$tmp_ascii=substr($tmp_ascii,1,4);
							
								$ret_ascii=array("Name"=>$engname, "RetASCII"=>$tmp_ascii);
								echo json_encode($ret_ascii);
								exit;