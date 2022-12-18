<style>
    a{
        color:black;
    }
    .submodules-href:hover{
        background-color: #f5f5f5;
    }
    table {
        padding: 0px;
        margin: 0px !important;
        border: 0px;
    }
    th, td{
        background: white;
        border: 0px;
    }
    .nodes {
        padding-left:0px;
    }
    .nodes .panel-body {
        height:90px;
    }
    .nodes a>panel-body {
        padding:0px;
        margin:0px;
    }
    .license {
        padding:0px;
    }
    .license .panel-body {
        height:90px;
    }
    .license .panel-body>a>div{
        padding:0px;margin:0px;
    }
    .client {
        padding-left:0px;
    }
    .client .panel-body {
        height:90px;
    }
    .client .panel-body>a>div{
        padding:0px;margin:0px;
    }
    .event {
        padding:0px;
    }
    .event .panel-heading a {
        text-decoration: underline;
        color:#509EE1;
    }
    .event .panel-body {
        height: 190px;
        padding: 0px;
    }
    .fs-echart-container {
        margin-bottom:0px;
    }
    .fs-echart-container .panel{
        margin-left:15px;
    }
    .fs-echart-container .panel-body {
        height:342px;
    }
    .frontend-network .panel-body{
        height: 320px;
    }
</style>
<script>
</script>
<div class="col-xs-8" style="padding:0px; margin-bottom:0px;">
    <div class="col-xs-4 nodes">
        <div class="panel panel-default">
            <div class="panel-heading">{{_("Nodes")}}</div>
            <div id="nodes_area" class="panel-body" align="center">
                <a href="../node" class="area-loaded">
                    <div class="col-xs-4" id="run_node_count"></div>
                    <div class="col-xs-4" id="warn_node_count"></div>
                    <div class="col-xs-4" id="dead_node_count"></div>
                </a>
            </div>
        </div>
    </div>
    <div class="col-xs-4 client">
        <div class="panel panel-default">
            <div class="panel-heading">{{_("Client Connections")}}</div>
            <div id="client_conn_area" class="panel-body" align="center">
                <a href="#" class="area-loaded">
                    <div class="col-xs-6">
                        <div class="title-bar nfs-clnt-title"></div>
                        <div class="title-bar"><span class="label label-info nfs-clnt-cnt"></span></div>
                    </div>
                    <div class="col-xs-6">
                        <div class="title-bar cifs-clnt-title"></div>
                        <div class="title-bar"><span class="label label-info cifs-clnt-cnt"></span></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="col-xs-4 license">
        <div class="panel panel-default">
            <div class="panel-heading">{{_("License")}}</div>
            <div id="license_area" class="panel-body" align="center">
                <a href="../license" class="area-loaded">
                    <div class="col-xs-6">
                        <div class="title-bar node-cnt-title"></div>
                        <div class="title-bar"><span class="label label-info node-count"></span></div>
                    </div>
                    <div class="col-xs-6">
                        <div class="title-bar days-left-title"></div>
                        <div class="title-bar"><span class="label expiry-date"></span></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="col-xs-12 event">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{_("Event")}}
                <a href="/event" class="pull-right">{{_("All")}}</a>
            </div>
            <div id="event_area" class="panel-body pre-scrollable" align="center">
                     <table class="table area-loaded">
                         <thead>
                         </thead>
                         <tbody>
                         </tbody>
                     </table>
            </div>
        </div>
        </div>
</div>
<div class="col-xs-4 sub-modules fs-echart-container" align="center">
    <div class="panel panel-default">
        <div class="panel-heading">{{_("File System")}}</div>
        <div class="panel-body" id="fs_echart" align="center" onClick="window.location.href='/fs'">
            <div class="area-loaded"></div>
        </div>
    </div>
</div>
<div class="col-xs-12 sub-modules frontend-network">
    <div class="panel panel-default">
        <div class="panel-heading">{{_("Frontend Network")}}</div>
        <div class="panel-body" id="nic_line_group" align="left">
            <div class="area-loaded"></div>
        </div>
    </div>
</div>
{% endblock %}
