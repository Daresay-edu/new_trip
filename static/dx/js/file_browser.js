var paths = [];
function render_file_browser(fsname, data)
{
    var files = data.dentries;

    var dom = '<div class="modal" id="path-selector" tabindex="-1" role="dialog" aria-labelledby="selector-label" aria-hidden="true">'+
        '<div class="modal-dialog modal-lg">'+
            '<div class="modal-content">'+
                '<div class="modal-header">'+
                    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">'+
                        '&times;'+
                    '</button>'+
                    '<div id="pathbar">'+
                        '<ul class="breadcrumb">'+
                        '</ul>'+
                    '</div>'+
                '</div>'+
                '<div class="modal-body">'+
                    '<table class="table" id="filelist">'+
                        '<thead>'+
                            '<tr>'+
                                '<th width="20%">{{_("Name")}}</th>'+
                                '<th width="10%">{{_("Owner")}}</th>'+
                                '<th width="10%">{{_("Group")}}</th>'+
                                '<th width="20%">{{_("Permission")}}</th>'+
                                '<th width="10%">{{_("Operation")}}</th>'+
                            '</tr>'+
                        '</thead>'+
                        '<tbody>'+
                        '</tbody>'+
                    '</table>'+
                '</div>'+
                '<div class="modal-footer">'+
                    '<input type="button" class="button-cancel form-control" id="up" onClick="handle_up()" value=\'{{_("Up")}}\'>'+
                    '<input type="button" class="button-cancel form-control" id="new_dir" data-toggle="modal" data-target="#creator" value=\'{{_("New Directory")}}\'>'+
                    '<input type="button" class="button-submit form-control" id="done" onClick="handle_done()"  value=\'{{_("Done")}}\'>'+
                '</div>'+
            '</div>'+
        '</div>'+
    '</div>';
    $('#file_browser').html(dom);


    pathbar = $("#pathbar ul")
    pathbar.empty();
    pathbar.append($("<span></span>").text('{{_("Choosed Path")}}' + ": "))
    pathbar.append($("<li></li>").append($("<a></a>").text(fsname).data('level', 0).click(handle_change_level)))
    for (var i = 0; i < paths.length; i++) {
        if (i < paths.length - 1)
            pathbar.append($("<li></li>").append($("<a></a>").text(paths[i]).data('level', i + 1).click(handle_change_level)))
        else
            pathbar.append($("<li></li>").append(paths[i])).addClass('active')
    }

    table = $("#filelist tbody");
    table.empty();
    if (files.length == 0) {
        table.append($(
            '<tr>' +
                '<td colspan="8">' +
                    '<div class="table-tips">{{_("There is no available file or directory.")}}</div>' +
                '</td>' +
            '</tr>'
        ));
    } else {
        for (var i = 0; i < files.length; i++) {
            if (files[i].type != 'directory') {
                continue;
            }
            row = $("<tr></tr>").data('file', files[i]);
            row.append($("<td></td>")
               .addClass('entryname')
               .append($("<a></a>").text(files[i].name)
                                   .prop('href', "#")
                                   .click(handle_enter_entry))
            );
            row.append($("<td></td>").text(files[i]['owner-name']));
            row.append($("<td></td>").text(files[i]['owner-group']));
            row.append($("<td></td>").text(files[i]['access']));
            row.append($("<td></td>").append($("<a></a>").text('{{_("Delete")}}')
                                   .addClass('button-table')
                                   .click(handle_delete_entry)));
            table.append(row);
        }
    }

    $('#note').empty();
    if (data.eof == false)
        $('#note').text('* ' + '{{_("The directory is very large, not all entries are displayed here.")}}');
    $("#path-selector").modal("show");
}

function reload_filelist(fsname) {
    console.log(fsname);
    var filelist;
    get_all_files(fsname, paths, function(data){
        filelist = data;
    })
    return filelist;
}

function handle_change_level()
{
    paths = paths.slice(0, $(this).data('level'));

    reload_filelist();
}

function handle_up()
{
    if (paths.length > 0) {
        paths.pop();
        reload_filelist();
    }
}

function handle_done()
{
    var path ="/";
    for (var i = 0; i < paths.length; i++) {
        if (path == "/")
            path += paths[i];
        else
            path += "/" + paths[i];
    }
    $("#path").val(path);
    $("#path-selector").modal("hide");
}

function handle_enter_entry()
{
    paths.push($(this).closest('tr').data('file').name);

    reload_filelist();
    render_file_browser();
}
function handle_create_entry()
{
    dirname = $('#creator #dirname').val();
    if (dirname.trim() == '') {
        toastr.error('{{_("Please input the name of new directory")}}');
        $('#creator #dirname').focus();
        return;
    }

    var url = '/api/v1/fs/' + $('#fsname').val() + '/namespace';
    if (paths.length == 0)
        url += '/';
    for (var i = 0; i < paths.length; i++)
        url += "/" + encodeURIComponent(paths[i]);

    page_lock();
    $.ajax({
        url: url,
        type: 'post',
        data:JSON.stringify({
            type: 'directory',
            name: dirname,
        }),
        success: function(data) {
            $('#creator').modal('hide');
            $('#creator #dirname').val('');
            page_unlock();
            // enter into this dir
            paths.push(dirname);
            reload_filelist();
        },
        error: function(error) {
            notify_api_error(error);
            page_unlock();
        },
    });
}

function handle_delete_entry()
{
    if (!prompt_yes('{{_("Type yes to delete this file!")}}'))
        return false;

    var file = $(this).closest('tr').data('file');

    var url = '/api/v1/fs/' + $('#fsname').val() + '/namespace';
    for (var i = 0; i < paths.length; i++)
        url += "/" + encodeURIComponent(paths[i]);
    url += "/" + encodeURIComponent(file.name);

    page_lock();
    $.ajax({
        url: url,
        type: 'delete',
        async: false,
        success: function(data) {
            reload_filelist();
            page_unlock();
        },
        error: function(error) {
            notify_api_error(error);
            page_unlock();
        },
    });
}
function file_browser_display(fsname, cur_path) {
    filelist = reload_filelist(fsname);
    console.log(filelist);
    render_file_browser(fsname, filelist);
}
