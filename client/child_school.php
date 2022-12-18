<script>
    function render_student(data){
	var dom = $('.student tbody')
        if (data.length == 0) {
		 dom.append('<tr><td colspan="6">'+
                '<div class="table-tips">无学员信息</div>'+
                '</td></tr>');
	} else {
            $('.check_all').removeClass('disabled').addClass('enabled');
            for (i = 0; i < data.length; i++) {
                row = $('<tr></tr>');
                row.data('stu_id', data[i]['name']);
                row.addClass('common');

                chk = $('<td></td>');
                chk.append(
                   $('<input class="del_check" name="del_check" type="checkbox"/>')
                   .on('click', function (){
                       var has_checked = false;
                       $('input:checkbox[name=del_check]').each(function(){
                           if (this.checked) {
                               $('.fa-trash-alt').parent().removeClass('disabled').addClass('enabled');
                               has_checked = true;
                           }
                       });
                       if (!has_checked) {
                           $('.check_all').attr('checked', false);
                           $('.fa-trash-alt').parent().addClass('disabled');
                       }
                   })
                );
                row.append(
                    chk,
                    $('<td></td>').text(data[i]['name']),
                    $('<td></td>').text(data[i]['classid']),
                    $('<td></td>').text(data[i]['age']),
                    $('<td></td>').text(data[i]['sex']),
                    $('<td></td>').text(data[i]['phone'])
                );
                dom.append(row);
            }
	}
    }
    function handle_check_all(checkbox){
        $('.del_check').attr('checked', checkbox.checked);
        checkbox.checked ?
            $('.fa-trash-alt').parent().removeClass('disabled').addClass('enabled') :
            $('.fa-trash-alt').parent().addClass('disabled');
    }
    $(function(){
	page_lock();
	$.ajax({
            type:'GET',
            url:"../server/server.php?action=child_school_query"
        })
        .done(function(data){
	    render_student(data);
	    page_unlock();
        })
        .fail(function(error){
            console.log(error);
	    report_error('查询分校信息失败！');
	    page_unlock();
        });

    });
</script>
<div class="col-xs-12 op-area">
    <div>
        <div class="col-xs-3 icon-outer" data-toggle="tooltip" data-placement="top" title="添加" onClick="render_serviceip_creator();">
            <i class="fas fa-plus fa-opfs-size"></i>
        </div>
        <div class="col-xs-3 icon-outer disabled" data-toggle="tooltip" data-placement="top" title="删除">
            <i class="fas fa-trash-alt fa-opfs-size"></i>
        </div>
    </div>
</div>
<div>
    <table class="table student">
        <thead>
            <tr>
                <th width="3%"><input class="check_all" type="checkbox" onclick="handle_check_all(this)"/></th> 
                <th width="20%">名字</th>
                <th width="20%">班级</th>
                <th width="20%">年龄</th>
                <th width="20%">性别</th>
                <th width="17%">电话</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="modal fade" id="serviceip-creator" tabindex="-1" role="dialog" aria-labelledby="serviceip-add-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="serviceip-add-label">
                      {{_("Add Service IP")}}
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="ip" class="required-field">{{_("Service IP(s)")}}</label>
                    <input class="form-control" name="ip" id="ip" type="text" placeholder='{{_("IP(s) separated with comma.")}}'>
                </div>
                <div class="form-group">
                    <label for="iface" class="required-field">{{_("NIC")}}</label>
                    <select class="form-control" multiple="multiple" id="iface"></select>
                </div>
                <div class="form-group">
                    <label for="netmask" class="required-field">{{_("Netmask")}}</label>
                    <input class="form-control" id="netmask" type="text" placeholder='{{_("Netmask")}}'>
                </div>
                <div class="form-group">
                    <label for="gateway">{{_("Gateway")}}</label>
                    <input class="form-control" id="gateway" type="text" placeholder='{{_("Gateway")}}'>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="button-cancel form-control" data-dismiss="modal">
                      {{_("Cancel")}}
                </button>
                <button type="button" class="button-submit form-control" onClick="handle_add_serviceip();">
                      {{_("Add")}}
                </button>
            </div>
        </div>
    </div>
</div>
