<?php
header("Cache-Control: no-cache, must-revalidate");
	echo "HRERERRWRWRREWREWR";
	require_once("db_opt.php");
	$engname = $_POST["engname"];
	$password=$_POST["password"];
	$conn=db_conn("daresay_db");
								
	$table_name="teachers";
									$sql="SELECT * FROM {$table_name} WHERE engname='$engname'";
									$result=mysql_query($sql,$conn);
									if (!$result) {
											mysql_close($conn);
										http_response_code (400);
									}
									$row = mysql_fetch_assoc($result);
									if ($password != $row['password']) {
											mysql_close($conn);
										http_response_code (400);
									} else {
										echo "<script>alert('test');</script>";
										//set session
										session_start();
										$_SESSION['username']=$engname;
 										if ($_SESSION['username'] == "admin") {
											$_SESSION['role'] = "admin";
										} else {
											$_SESSION['role'] = "ordinary";
										}
									mysql_close($conn);
										http_response_code (200);
									}
								return json_encode("1");
?>