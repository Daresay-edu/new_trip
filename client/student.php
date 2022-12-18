<script>
    function render_student(data){
	$('.main-functional-area').html(
            '<div class="col-xs-12 op-area">'+
                '<div>'+
                    '<div class="col-xs-3 icon-outer" data-toggle="tooltip" data-placement="top" title="添加" onClick="render_student_creator();">'+
                        '<i class="fas fa-plus fa-opfs-size"></i>'+
                    '</div>'+
                    '<div class="col-xs-3 icon-outer disabled" data-toggle="tooltip" data-placement="top" title="删除">'+
                        '<i class="fas fa-trash-alt fa-opfs-size"></i>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div>'+
                '<table class="table student">'+
                    '<thead>'+
                        '<tr>'+
                            '<th width="3%"><input class="check_all" type="checkbox" onclick="handle_check_all(this)"/></th>'+ 
                            '<th width="10%">名字</th>'+
                            '<th width="10%">英文名字</th>'+
                            '<th width="20%">班级</th>'+
                            '<th width="20%">年龄</th>'+
                            '<th width="20%">性别</th>'+
                            '<th width="17%">电话</th>'+
                        '</tr>'+
                    '</thead>'+
                    '<tbody>'+
                    '</tbody>'+
                '</table>'+
            '</div>'
	);
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
                    $('<td></td>').text(data[i]['engname']),
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
    function render_student_creator() {
	    $('.main-functional-area').html(
		    '<div class="form-group">'+
		        '<label class="required-field">姓名</label>'+
                        '<input class="form-control" type="text" id="name">'+
		    '</div>'+
		    '<div class="form-group">'+
		        '<label class="required-field">年龄</label>'+
                        '<select id="age" class="form-control">'+
			    '<option value="1">1</option>'+
			    '<option value="2">2</option>'+
			    '<option value="3">3</option>'+
			    '<option value="4">4</option>'+
			    '<option value="5">5</option>'+
			    '<option value="6">6</option>'+
			    '<option value="7">7</option>'+
			    '<option value="8">8</option>'+
			    '<option value="9">9</option>'+
			    '<option value="10">10</option>'+
			    '<option value="11">11</option>'+
			    '<option value="12">12</option>'+
			    '<option value="13">13</option>'+
			    '<option value="14">14</option>'+
			    '<option value="15">15</option>'+
			    '<option value="16">16</option>'+
			    '<option value="17">17</option>'+
			    '<option value="18">18</option>'+
                        '</select>'+
		    '</div>'+
		    '<div class="form-group">'+
		        '<label class="required-field">性别</label>'+
                        '<select id="gender" class="form-control">'+
			    '<option value="male">男孩</option>'+
			    '<option value="female">女孩</option>'+
                        '</select>'+
		    '</div>'+
		    '<div class="form-group">'+
		        '<label class="required-field">年级</label>'+
                        '<select id="grade" class="form-control">'+
			    '<option value="1">婴儿</option>'+
			    '<option value="2">小班</option>'+
			    '<option value="3">中班</option>'+
			    '<option value="4">大班</option>'+
			    '<option value="5">一年级</option>'+
			    '<option value="6">二年级</option>'+
			    '<option value="7">三年级</option>'+
			    '<option value="8">四年级</option>'+
			    '<option value="9">五年级</option>'+
			    '<option value="10">六年级</option>'+
			    '<option value="11">初一</option>'+
			    '<option value="12">初二</option>'+
			    '<option value="13">初三</option>'+
			    '<option value="14">高一</option>'+
			    '<option value="15">高二</option>'+
			    '<option value="16">高三</option>'+
                        '</select>'+
		    '</div>'
	    );
	    multiselect_init($('#age,#gender,#grade'), {'filter':false});
	    $('#age').multiselect('select', '1').multiselect('refresh');
	    $('#gender').multiselect('select', 'male').multiselect('refresh');
	    $('#grade').multiselect('select', '5').multiselect('refresh');
    
    }
    $(function(){
	page_lock();
	$.ajax({
            type:'GET',
            url:"../server/server.php?action=stu_query"
        })
        .done(function(data){
	    render_student(data);
	    page_unlock();
        })
        .fail(function(error){
	    report_error('查询学生信息失败');
	    page_unlock();
        });

    });
</script>
<div class="main-functional-area">
</div>
