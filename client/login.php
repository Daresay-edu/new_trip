<!DOCTYPE html>
<html>
<head>
    <title>哆啦</title>

<?php include "common/head.php" 
?>
    <style>
        html {
            height: 100%;
        }
        body {
            min-height: 100%;
            background: linear-gradient(#313A46, #2D69B9);
        }
        .login-container {
            width: 300px;
            margin: auto;
        }
        .login-container-inner {
            margin: 0;
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%)
        }
        .login-box {
            width:380px;
            height:280px;
            border-radius: 3px;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
            background-color: #f8f9fa;
            padding: 15px;
        }
        .login-label {
            line-height: 35px;
            text-align: left;
            margin-top:10px;
        }
        .login-input {
            text-align:center;
        }
        .login-button {
            margin-top:20px;
            border:1px solid #509ee1;
            color:#fff;
            background:#2D67B5;
        }
        .login-footer{
            background:#f5f5f5;
            border-top-style:solid;
            border-top-color:#e0dfdf;
            border-top-width:1px;
            height:79px;
            margin:0;
            position:relative;
            text-align:center;
            line-height:79px;
        }
        .copyright {
            color: white;
        }
    </style>
    <script>
        $(function(){
        function check_login(){
            login.action = "/login";
            login.method = "post";
            login.submit();
        }
    </script>
</head>
<body>
    <form id='login' name="login" method="post">
    <div class="container">
    <div class="login-container">
    <div class="login-container-inner">
        <div style="height:69px;" class="hv-center"><div class="login-logo"></div></div>
        <div class="login-box">
            <div>
                <!-- username -->
                <div>
                    <div class="col-xs-12 login-label">用户名</div>
                    <div class="col-xs-12">
                        <input class="login-input form-control" form="login" name="username" id="username" type="text" autofocus="autofocus">
                    </div>
                </div>

                <!-- password -->
                <div>
                    <div class="col-xs-12 login-label">密码</div>
                    <div class="col-xs-12">
                        <input class="login-input form-control" form="login" name="password" id="password" type="password" autocomplete="off">
                    </div>
                </div>

                <!-- login button -->
                <div>
                    <div class="col-xs-12 login-input">
                        <input class="login-button btn col-xs-12" form="login" type="submit" value="登录" onClick="return check_login()">
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </form>
</body>
</html>
