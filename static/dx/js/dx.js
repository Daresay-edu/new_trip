function howmany(x, y)
{
    return Math.floor(((x)+((y)-1))/(y))
}

function isNumber(n)
{
    return /^-?[\d.]+(?:e-?\d+)?$/.test(n);
}

function isIPv4Subnet(v)
{
    var pattern = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\/([0-9]|[1-2][0-9]|3[0-2]))?$/

    return pattern.test(v);
}

function isIPv4Range(v)
{
    var pattern = /^(((2[0-4]\d|25[0-5]|[01]?\d\d?)\.){3}(2[0-4]\d|25[0-5]|[01]?\d\d?))(-((2[0-4]\d|25[0-5]|[01]?\d\d?)\.){3}(2[0-4]\d|25[0-5]|[01]?\d\d?))?$/; 

    return pattern.test(v);
}

function ipv4_string2list(ipstr)
{
    if (ipstr.trim() == '')
        return [];

    return ipstr.split(',').map(function(v){
        return v.trim();
    })
}

function hostacl_address_valid(hoststr)
{
    hosts = ipv4_string2list(hoststr);
    for(var i = 0; i < hosts.length; i++) {
        if (!isIPv4Range(hosts[i]) && !isIPv4Subnet(hosts[i]))
            return false;
    }
    return true;
}

function serviceip_string_valid(ipstr)
{
    ips = ipv4_string2list(ipstr);
    for(var i = 0; i < ips.length; i++) {
        if (!isIPv4Range(ips[i])) {
            return false;
        }
    }
    return true;
}

function cluster_name_valid(name)
{
    var pattern = /^([a-zA-Z][a-zA-Z0-9]*((-|\.)[a-zA-Z0-9]+)*)$/;

    if (name.length > 32)
        return false;

    return pattern.exec(name) != null;
}

function ipv4_valid(ip)
{
    var pattern = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/;
    return pattern.exec(ip) != null;
}

function formated_size(value)
{
    if (value == 0)
        return '0 Bytes';

    var k = 1024,
        sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        i = Math.floor(Math.log(value) / Math.log(k));

    if (i >= sizes.length)
        i = sizes.length - 1;

    return parseFloat((value / Math.pow(k, i)).toFixed(2)) + sizes[i];
}

function formated_number(value)
{
    if (value == 0)
        return '0';

    var k = 1000,
        sizes = ['', 'K', 'M', 'B', 'T'],
        i = Math.floor(Math.log(value) / Math.log(k));

    if (i >= sizes.length)
        i = sizes.length - 1;

    return parseFloat((value / Math.pow(k, i)).toFixed(2)) + sizes[i];
}

function size_unit(size)
{
    return formated_size(size);
}

function size_to_count(size,size_unit){
    if(size_unit=='YB'){
        size=size*1024;
        size_unit="ZB";
    }
    if(size_unit=='ZB'){
        size=size*1024;
        size_unit="EB";
    }
    if(size_unit=='EB'){
        size=size*1024;
        size_unit="PB";
    }
    if(size_unit=='PB'){
        size=size*1024;
        size_unit="TB";
    }
    if(size_unit=='TB'){
        size=size*1024;
        size_unit="GB";
    }
    if(size_unit=='GB'){
        size=size*1024;
        size_unit="MB";
    }
    if(size_unit=='MB'){
        size=size*1024;
        size_unit="KB";
    }
    if(size_unit=='KB'){
        size=size*1024;
        size_unit="B";
    }
    return Math.floor(size);
}

function change_style_time(time)
{
    if (time >= 10)
        return time;

    return '0' + time;
}

function time_to_string(t)
{
    var d = new Date(t * 1000);

    return [d.getFullYear(),
            change_style_time(d.getMonth() + 1),
            change_style_time(d.getDate())].join('/') + ' ' +
           [change_style_time(d.getHours()),
            change_style_time(d.getMinutes()),
            change_style_time(d.getSeconds())].join(':')
}

/* page is locked by default, and will be unlocked on loaded,
 * so the initial value of page lockref is 1 */

var page_lockref = 1;

function page_lock(slow)
{
    slow = slow || false;

    page_lockref += 1;

    $(".locker-background, .locker-spinner, .locker-message").show();
    if (slow)
        $(".locker-spinner").hide();
    else
        $(".locker-message").hide();
}

function page_unlock()
{
    if ((page_lockref -= 1) <= 0) {
        $(".locker-background, .locker-spinner, .locker-message").hide();

        /* we have encountered unpaired lock and unlock */
        if (page_lockref < 0) {
            console.error("page lockref drop below zero: " + page_lockref);
        }
    }
}

/* all areas in current page will enter loading state */
function area_loading_all()
{
    $(".area-loaded").each(function(){
        area_loading($(this).parent().attr("id"));
    });
}

/* one area become loading state */
function area_loading(div_id)
{
    var ht = $("#"+div_id).height();
    $("#" + div_id).find('.area-loaded')
    .hide()
    .after(
        $('<div></div>')
        .height(ht)
        .addClass("hv-center")
        .addClass("area-loading")
        .append(
            $('<div></div>')
            .addClass("sp sp-circle")
        )
    );
}

function area_loaded(div_id)
{
    $(div_id).find('.area-loading').remove();
    $(div_id).find(".area-loaded").show();
}

function notify_api_error(resp)
{
    var error = jQuery.parseJSON(resp.responseText);
    toastr.error(error.errno+"</br>"+error.error);
}

function report_server_error(resp)
{
    var error = jQuery.parseJSON(resp.responseText);
    $('.main-header').hide();
    $('.alert').show()
    .find('.alert-link')
    .html('<i class="fas fa-exclamation-triangle"></i><span style="margin:10px;">' + error.errno + ':' + error.error + '</span>');
    $('.main').animate({scrollTop: 0},500);
}

function report_error(errmsg)
{
    $('.main-header').hide();
    $('.alert').show()
    .find('.alert-link')
    .html('<i class="fas fa-exclamation-triangle"></i><span style="margin:10px;">' + errmsg + '</span>');
    $('.main').animate({scrollTop: 0},500);
}


function get_errno(resp)
{
    return jQuery.parseJSON(resp.responseText).errno;
}

function user_group_filter(data) {
    var ug = new Array();
    for (var i = 0; i < data.length; i++) {
        if (data[i].type.toLowerCase() == 'admin')
            continue;
        ug.push(data[i]);
    }
    return ug;
}

function get_all_users(successCallback, failureCallback, async)
{
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/ID/users",
        async: async,
    })
    .done(function(data) {
        successCallback(user_group_filter(data));
    })
    .fail(failureCallback)
}

function get_all_groups(successCallback, failureCallback, async)
{
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/ID/groups",
        async: async,
    })
    .done(function(data) {
        successCallback(user_group_filter(data));
    })
    .fail(failureCallback)
}

function user_display_name(users, user)
{
    if (typeof(user) != 'string')
        return user.type.toLowerCase() + ':' + user.name;

    for (var i = 0; i < users.length; i++) {
        if (users[i].name == user)
            return users[i].type.toLowerCase() + ':' + users[i].name;
    }
    return user;
}

function group_display_name(groups, group)
{
    if (typeof(group) != 'string')
        return group.type.toLowerCase() + ':' + group.name;

    for (var i = 0; i < groups.length; i++) {
        if (groups[i].name == group)
            return groups[i].type.toLowerCase() + ':' + groups[i].name;
    }
    return group;
}

// for pagination
$.fn.Pagination = function (options) {
    let myDoom = this;
    if (options.count <= options.limit) {
        myDoom.empty();
        return;
    }
    options = options || {};
    options.page = options.page || 1;
    options.count = options.count || 1;
    options.limit = options.limit || 15;
    options.groups = options.groups || 4;
    options.prev = options.prev || '<i class="fas fa-chevron-left"></i>';
    options.next = options.next || '<i class="fas fa-chevron-right"></i>';
    options.first = options.first || '<i class="fas fa-step-backward"></i>';
    options.last = options.last || '<i class="fas fa-step-forward"></i>';
    options.onPageChange = options.onPageChange || function (page) {console.log(page)};
    let PageFloat = Math.floor(options.groups / 2),
        maxPage = Math.ceil(options.count / options.limit),
        pageListHtml = "";
    let i = options.page - PageFloat;
    if (options.page + PageFloat > maxPage ){ i = maxPage - (PageFloat * 2);}
    if (i < 1){i = 1 ;}
    do {
        let Selected = "";
        if (i === options.page){
            Selected = 'active';
        }
        pageListHtml += '<li class="page-list '+Selected+'"><a href="#">'+i+'</a></li>';
        i ++;
    }while ((i <= (options.page + PageFloat) || options.page - PageFloat <= 0 && i < (options.page + PageFloat + (PageFloat + 2 - options.page) ))  && i <= maxPage )

    let html = '<nav aria-label="Page navigation">' +
        '<ul class="pagination pagination-sm">' +
        '<li><a href="#" class="pager-item" aria-label="first"><span aria-hidden="true">'+ options.first +'</span></a></li>' +
        '<li><a href="#" class="pager-item" aria-label="prev"><span aria-hidden="true">'+ options.prev +'</span></a></li>' +
        pageListHtml +
        '<li><a href="#" class="pager-item" aria-label="next"><span aria-hidden="true">'+ options.next +'</span></a></li>' +
        '<li><a href="#" class="pager-item" aria-label="last"><span aria-hidden="true">'+ options.last +'</span></a></li>' +
        '</ul></nav>';

    myDoom.off('click');
    myDoom.empty();
    myDoom.append(html);
    myDoom.on('click', '.pagination .page-list', function() {
        options.page = parseInt($(this).text());
        myDoom.Pagination(options);
        options.onPageChange(parseInt($(this).text()));
    });

    myDoom.on('click','.pagination .pager-item',function () {
        let label = $(this).attr('aria-label');
        let page = 1;
        if (label === 'first'){
            page = 1;
        }
        else if (label === 'prev'){
            page = options.page - 1;
            if (page < 1 )    page = 1;
        }else if (label === 'next'){
            page = options.page +1;
            if (page > maxPage) page = maxPage;
        }else if (label === 'last'){
            page = maxPage;
        }
        options.page = page;
        myDoom.Pagination(options);
        options.onPageChange(page);
    })
}

function email_valid(email_addr) {
    var patrn = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
    return patrn.exec(email_addr) != null;
}

function cluster_inited(successCallback, failureCallback, async) {
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/cluster/init",
        async: async,
    })
    .done(successCallback)
    .fail(failureCallback)
}

function get_cluster_info(successCallback, failureCallback, async) {
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/cluster/nodes",
        async: async,
    })
    .done(successCallback)
    .fail(failureCallback)
}

function get_version(successCallback, failureCallback, async) {
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/version",
        async: async,
    })
    .done(successCallback)
    .fail(failureCallback)
}

function prompt_yes (msg) {
    while(true) {
        var yes = prompt(msg);
        if (yes == null)
            return false; 
        else if (yes == "yes")
            return true;
    }
}

function discover_nodes(successCallback, failureCallback, async) {
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/neighbour",
        async: async,
    })
    .done(successCallback)
    .fail(failureCallback)
}

function get_gui_mgmt_node(successCallback, failureCallback, async) {
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/gui/mgmt-node",
        async: async,
    })
    .done(successCallback)
    .fail(failureCallback)
}

function brick_capacity(datalun_size,datalun_count,vhs_count){
    function rounddown(value, granularity){
        return Math.floor(value / granularity) * granularity;
    }
    
    datalun_size = rounddown(Number(datalun_size - 8*1024*1024), 1024*1024*1024);
    if (vhs_count > 0){
         var bg_count = datalun_size / (1024*1024*1024);
         bg_count = bg_count-Math.ceil(bg_count/datalun_count) * vhs_count;
         datalun_size = bg_count*1024*1024*1024;
    }
    var the_brick_capacity=datalun_size * datalun_count;
    if(the_brick_capacity<0){
        the_brick_capacity=0;
    }
    return the_brick_capacity;
}

function is_fs_running(){
    var fs_info;
    $.ajax({
        type: 'GET',
        url: '/api/v1/fs',
        async: false,
        success: function(data) {
            fs_info = data;
        },
        error: function(data) {
            report_server_error(data);
        }
    })

    return fs_info && fs_info.status == 'started' ? true : false;
}

function scroll_to_center(dom) {
    $('html,body').animate(
        {
            scrollTop:dom.offset().top-70
        },
        500
    );
}

function get_all_luns(successCallback, failureCallback, async)
{
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/lun",
        async: async,
    })
    .done(successCallback)
    .fail(failureCallback)
}

function multiselect_init(dom, opts=null, onChgCallbk=null)
{
    //opts should like {'filter':bool, 'colloptgrpdef':bool, 'btnWidth':str, 'maxHeight': int, 'dropUp':bool}
    var filter = (dom.find('option').length > 15);
    var colloptgrpdef = (dom.find('option').length > 15);
    var btnWidth = '100%';
    var maxHeight = 200;
    var dropUp = false;
    if (opts != null) {
        if (opts.hasOwnProperty('filter'))
            filter = opts.filter;
        if (opts.hasOwnProperty('colloptgrpdef'))
            colloptgrpdef = opts.colloptgrpdef;
        if (opts.hasOwnProperty('btnWidth'))
            btnWidth = opts.btnWidth;
        if (opts.hasOwnProperty('maxHeight'))
            maxHeight = opts.maxHeight;
        if (opts.hasOwnProperty('dropUp'))
            dropUp = opts.dropUp;
    }
    //see more options and functions on http://davidstutz.github.io/bootstrap-multiselect/
    dom.multiselect("destroy").multiselect({
        buttonWidth: btnWidth,
        disableIfEmpty: true,
        enableCaseInsensitiveFiltering: filter,
        includeSelectAllOption:true,
        enableClickableOptGroups: true,
        enableCollapsibleOptGroups: true,
        collapseOptGroupsByDefault: colloptgrpdef,
        maxHeight: maxHeight,
        dropUp: dropUp,
	buttonTextAlignment: 'left',
        buttonText:function(options,select){
            if(options.length === 0){
                return "None";
            }else{
                var labels=[];
                options.each(function(){
                    if($(this).attr('label')!==undefined){
                        labels.push($(this).attr('label'));
                    }else{
                        labels.push($(this).html());
                    }
                });
                return labels.join(',')+'';
            }
        },
        onChange: function(option, checked, select) {
            if (onChgCallbk != null)
                onChgCallbk(option, checked, dom.val());
        },
    });
}

function file_stat(file, successCallback, failureCallback, async) {
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/namespace/"+file+"?metadata",
        async: async,
    })
    .done(function(data) {
        successCallback(data);
    })
    .fail(failureCallback)
}

function get_events(successCallback, failureCallback, async) {
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/events",
        async: async,
    })
    .done(function(data) {
        successCallback(data);
    })
    .fail(failureCallback)
}

function get_license(successCallback, failureCallback, async) {
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/license",
        async: async,
    })
    .done(function(data) {
        successCallback(data);
    })
    .fail(failureCallback)
}

function get_fs_info(successCallback, failureCallback, async) {
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/fs",
        async: async,
    })
    .done(function(data) {
        successCallback(data);
    })
    .fail(failureCallback)
}

function get_nas_clients(protocol, successCallback, failureCallback, async) {
    failureCallback = failureCallback || report_server_error;
    async = async || false;

    $.ajax({
        url: "/api/v1/shares/" + protocol + '?clients',
        async: async,
    })
    .done(function(data) {
        successCallback(data);
    })
    .fail(failureCallback)
}

function get_event_level_class(level){
    switch (level) {
        case 'INFO':
        case 'DEBUG':
        case 'NOTICE':
            return 'fas fa-info-circle fa-5g safe';
        case 'WARNING':
            return 'fas fa-exclamation-triangle warn';
        case 'ERROR':
        case 'CRITICAL':
        case 'ALERT':
        case 'EMERG':
            return 'fas fa-times-circle dangerous';
    }
}
