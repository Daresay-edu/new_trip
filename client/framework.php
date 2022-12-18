<!DOCTYPE html>
<html>
<head>
    <title>哆啦</title>
<?php include "common/head.php" 
?>
    <style>
        .locker-background {
            height:100%;
            position:absolute;
            top:0%;
            left:0%;
            width:100%;
            background-color:black;
            z-index:1001;
            -moz-opacity:0.2;
            opacity:.2;
            filter:alpha(opacity=70);
        }
        .locker-spinner {
            position:absolute;
            top:50%;
            left:50%;
            z-index:1002;
        }
        .locker-message {
            width:350px;
            height:180px;
            padding-top:29px;
            background-color:white;
            border:1px solid #F5F5F5;
            border-radius:2px;
            position: absolute;
            top: 37%;
            left: 40%;
            z-index: 1003;
        }
        .version {
            position: absolute;
            top: -8px;
            right: -43px;
            color: white;
        }
        .sys-set {
            height: 6vh;
            border-top: 1px solid #4E5968;
            padding: 0px;
            margin: 0px;
        }
        .sys-set i {
            color: #ADAFB1;
        }
        .sys-set .fa-caret-up {
            font-size: 10px;
        }
        .sys-set a>i {
            width: 20px;
        }
        .logo-container {
            height: 8vh;
        }
        .menu-container {
            height: calc(100vh - 14vh);
        }
        .dashboard {
            border-top:1px solid #4E5968;
        }
        .main-header {
            padding-bottom: 1px;
            margin-bottom: 20px;
            color: #9AA7B8;
        }
    </style>
    <script>
        var cur_module = 'dashboard';
        function redirect(page) {
	    //$.cookie('module', page);
            var header_str = '';
            switch(page){
	        case 'dashboard':
	            $('.main-func').load('dashboard.php');
	            break;
	        case 'child_school':
	            $('.main-func').load('child_school.php');
	            break;
	        case 'teacher':
	            $('.main-func').load('dashboard.php');
	            break;
	        case 'school':
	            $('.main-func').load('school.php');
	            break;
	        case 'student':
		    header_str = '学生管理 > 通用'
	            $('.main-func').load('student.php');
	            break;
            }
            $('.main-header h4').html(header_str);
        }
        $(function(){
	    //redirect(document.cookie['module']);
            page_unlock();
            $.ajaxSetup({ cache: false });
            area_loading_all();

            $('.menu-container a').on('click',function(){
                $(this).parent("li").siblings("li.menu-item").children('ul').slideUp(200);
                if ($(this).next().css('display') == "none") {
                    $(this).next('ul').slideDown(200);
                    $(this).parent('li').addClass('menu-item-show').siblings('li').removeClass('menu-item-show');
                }else{
                    $(this).next('ul').slideUp(200);
                    $(this).parent('li').removeClass('menu-item-show');
                }
            });
            $('.menu-container a').hover(function(){
                $(this).find('.menu-icon, span').css('color', 'white');
            },function(){
                if (!$(this).parent().hasClass('active')) {
                    $(this).find('.menu-icon, span').css('color', '#ADAFB1');
                    $(this).find('.badge').css('color', 'white');
                }
            });

           // var title_str = '';
           // if ($('.'+'{{ menu_item }}').length > 0) {
           //     var dom = $('.'+'{{ menu_item }}');
           //     dom.addClass('active').closest('.menu-item').children('ul').slideDown(200);
           //     dom.closest('.menu-item').addClass('menu-item-show').siblings('li').removeClass('menu-item-show');

           //     if (dom.hasClass('menu-item')) {
           //         title_str = dom.find('span').text();
           //     } else {
           //         title_str = dom.closest('.menu-item').children('a').find('span').text();
           //         title_str += ' > ' + dom.find('span').eq(1).text();
           //     }
           // } else {
           //     if ('{{ menu_item }}' == 'menu-license')
           //         title_str = '{{_("License")}}';
           // }

           // $('.main-header').css('border-bottom', '1px solid #39AFD1').find('h4').html(title_str);

        });
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="locker-background"></div>
    <div class="locker-spinner sp sp-circle"></div>
    <div class="locker-message" align="center" hidden>
        <div class="sp sp-circle"></div>
        <p>{{_("Do not close or leave this page and execute any background commands.")}}</p>
        <p>{{_("Please wait several minutes.")}}</p>
    </div>
    <div id="content">
        <div class="col-xs-2 menu">
            <div class="hv-center logo-container">
                <div class="topbar-logo" style="border-bottom:1px solid #4E5968">
                    <span class="version">
                    </span>
                </div>
            </div>
            <div class="pre-scrollable menu-container">
                <ul>
                    <li class="menu-item menu-dashboard">
                        <a href="javascript:redirect('dashboard')">
                            <i class="fas fa-tachometer-alt menu-icon"></i>
                            <span>概览</span>
                        </a>
                    </li>
                    <li class="menu-item menu-cluster">
                        <a href="javascript:redirect('child_school')">
                            <i class="fas fa-server menu-icon"></i>
                            <span>分校管理</span>
                            <span class="badge pull-right active"></span>
                        </a>
                    </li>
                    <li class="menu-item menu-cluster">
                        <a href="javascript:redirect('school')">
                            <i class="fas fa-server menu-icon"></i>
                            <span>班级管理</span>
                            <span class="badge pull-right active"></span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:;"><i class="fas fa-network-wired menu-icon"></i>
                            <span>员工管理</span>
                            <i class="fas fa-chevron-down menu-item-more"></i>
                        </a>
                        <ul>
                            <li class="menu-serviceip">
                                <a href="/serviceip">
                                    <span></span>
                                    <span>教师管理</span>
                                </a>
                            </li>
                            <li class="menu-zone">
                                <a href="/zone">
                                    <span></span>
                                    <span>其他人员管理</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:;">
                            <i class="fas fa-folder-open menu-icon"></i>
                            <span>学生管理</span>
                            <span class="badge pull-right active"></span>
                            <i class="fas fa-chevron-down menu-item-more"></i>
                        </a>
                        <ul>
                            <li class="menu-fs">
                                <a href="javascript:redirect('student')">
                                    <span></span>
                                    <span>学生新增</span>
                                    <span class="badge pull-right active-sec"></span>
                                </a>
                            </li>
                            <li class="menu-quota">
                                <a href="/quota">
                                    <span></span>
                                    <span>学生考勤</span>
                                </a>
                            </li>
                            <li class="menu-file">
                                <a href="/file/list">
                                    <span></span>
                                    <span>Browse</span>
                                </a>
                             </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:;">
                            <i class="fas fa-share-square menu-icon"></i>
                            <span>销售管理</span>
                            <i class="fas fa-chevron-down menu-item-more"></i>
                        </a>
                        <ul>
                            <li class="menu-nfs">
                                <a href="/share/nfs">
                                    <span></span>
                                    <span>NFS</span>
                                </a>
                            </li>
                            <li class="menu-cifs">
                                <a href="/share/cifs">
                                    <span></span>
                                    <span>CIFS</span>
                                </a>
                            </li>
                            <li class="menu-ftp">
                                <a href="/share/ftp">
                                    <span></span>
                                    <span>FTP</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:;">
                            <i class="fas fa-share-square menu-icon"></i>
                            <span>财务报告</span>
                            <i class="fas fa-chevron-down menu-item-more"></i>
                        </a>
                        <ul>
                            <li class="menu-nfs">
                                <a href="/share/nfs">
                                    <span></span>
                                    <span>NFS</span>
                                </a>
                            </li>
                            <li class="menu-cifs">
                                <a href="/share/cifs">
                                    <span></span>
                                    <span>CIFS</span>
                                </a>
                            </li>
                            <li class="menu-ftp">
                                <a href="/share/ftp">
                                    <span></span>
                                    <span>FTP</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="sys-set">
                <div class="col-xs-12" style="height:100%;">
                    <div class="col-xs-4 hv-center" style="height:100%;">
                        <i class="fas fa-question-circle fa-lg" onClick="window.open('/help');"></i>
                    </div>
                    <div class="col-xs-4 hv-center" style="height:100%;">
                        <span class='dropup' >
                            <i class="fas fa-cog dropdown-toggle fa-lg" data-toggle="dropdown"></i>
                            <i class="fas fa-caret-up dropdown-toggle" data-toggle="dropdown"></i>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                <li>
                                    <a href="/license">
                                        <i class="fas fa-id-badge"></i>
                                        <span>设置</span>
                                    </a>
                                </li>
	                    </ul>
                        </span>
                    </div>
                    <div class="col-xs-4 hv-center" style="height:100%;">
                        <span class='dropup'>
                            <i class="fas fa-user dropdown-toggle fa-lg" data-toggle="dropdown"></i>
                            <i class="fas fa-caret-up dropdown-toggle" data-toggle="dropdown"></i>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                <li onClick="$('#account-passwd').modal('show')">
                                    <a href="#">
                                        <i class="fas fa-key"></i>
                                        <span>修改密码</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/logout">
                                        <i class="fas fa-sign-out-alt fa-g"></i>
                                        <span>退出</span>
                                    </a>
                                </li>
	                    </ul>
                        </span>
                    </div>
                </div>
            </div>
            </div>
       </div>
       <div class="col-xs-10 main pre-scrollable">
               <div class="main-header">
                   <span><h4></h4></span>
               </div>
               <div class="alert alert-danger alert-dismissible" role="alert" hidden>
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                   <a href="#" class="alert-link"></a>
               </div>
               <div class="main-func">
               </div>
       </div>
    </div>
</div>
</body>
</html>

