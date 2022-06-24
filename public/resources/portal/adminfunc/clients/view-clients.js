
get_client_list();
get_my_kids();
function get_client_list() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-client-lists",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $('#clients-body').html('<tr>'
            +'<td><i class="bx bx-loader bx-spin"></i></td>'
            +'<td><i class="bx bx-loader bx-spin"></i></td>'
            +'<td><i class="bx bx-loader bx-spin"></i></td>'
            +'<td><i class="bx bx-loader bx-spin"></i></td>'
            +'<td><i class="bx bx-loader bx-spin"></i></td>'
            +'</tr>'
            );
        },
        success: function (e) {
            //console.log('get services done');
            //console.log(e.data);
            var item_divs = '';
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");

                var data_export = e.data;
                // console.log('Data export:',data_export);
                // for (var i = 0; i < data_export.length; i++) {                    
                //     console.log('Data export row:',data_export[i]);
                // }

                $.each(data_export.data, function (key, val) {              
                    //console.log('Data export row:',val);
                    //var session_data = val.trainer_activities_array;             
                    //console.log('Session_data:',session_data);

                    item_divs += '<tr>'
                        +'<td>'+val.user_name+'</td>'
                        +'<td>'+val.email_address+'</td>'
                        +'<td>'+val.phone+'</td>'
                        +'<td>'+val.date_of_joining+'</td>'
                        +'<td>'+val.unit_ui_display+'</td>'
                    +'</tr>';

                });

                $('#clients-body').html(item_divs);

                
                //$("#loading-div").css({'display':'none'});

            } else {
                $('#clients-body').html(e.messages);
            }
        },
        complete: function () {
            //pass
        }
    });

}


$(document).on("click", ".actionbutton-details", function () {
    console.log('actionbutton-details clicked');
    let attr_id = $(this).attr('attr-id');
    let attr_act = $(this).attr('attr-act');
    get_kid_details(attr_id);
});

function get_kid_details(attr_id){
    var b = {
        'attr_id': attr_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-kid-full-details",
        data: b,
        dataType: "json",

        success: function (e) {
            if (e.status == 'ok') {

                $('#preview-name').html(e.kidsname);
                $('#age').html(e.age);
                $('#display_date').html(e.display_date);
                $('#allergies').html(e.allergies);
                $('#medical_conditions').html(e.medical_conditions);
                $('#special_needs').html(e.special_needs);
                $('#behavioral_conditions').html(e.behavioral_conditions);

                $('#edit-kid').attr('href','/client-vw-edit-registered-kids?rec-id='+e.record_id);
                var s = '';
                var ex = '';

                $.each(e.special_need_list, function (key, val) {
                    s += val.condition_name + ", ";
                });
                
                $('#special_need_list').html(s);
                
                $('#preview').modal('show');
            }
        },
        complete: function () {

        }
    });

    // console.log('Preview clicked');
    // var blog_title = $("#blog-title").val();
    // var teaser = $("#teaser").val();
    // let editor = $("#editor").val();

    // console.log(blog_title);
    // console.log(teaser);
    // console.log(editor);

    // $("#preview-title").html(blog_title);
    // $("#preview-teaser").html(teaser);
    // $("#blog-body").html(editor);
    // cccc.preventDefault()
}



function get_my_kids(param = null) {
    $('#my-sessions').DataTable().destroy();
    var requiredfunction = {
        'token': localStorage.token,
    };
    $("#my-sessions").DataTable({
            order: [
                [1, 'asc']
            ],
            rowGroup: {
                dataSrc: 0
            },
            "ajax": {
                "data": requiredfunction,
                "url": "/client-fetch-my-kids-adm",
            },
            autoWidth: !1,
            responsive: 1,
            lengthMenu: [
                [8, 16, 88, -1],
                ["8 Rows", "16 Rows", "88 Rows", "All Items"]
            ],
            language: {
                searchPlaceholder: "Search for Items..."
            },
            sDom: '<"dataTables__top"flB<"dataTables_actions">>rt<"dataTables__bottom"ip><"clear">',
            // buttons: [
            //     {
            //         //extend: 'csv',
            //         exportOptions: {
            //             modifier: {
            //                 search: 'none'
            //             }
            //         }
            //     }
            // ],
            initComplete: function() {
                $(".dataTables_actions").html('<i class="zwicon-more-h" data-toggle="dropdown" />' +
                    '<div class="dropdown-menu dropdown-menu-right">' +
                    '<a club-Items-action="print" class="dropdown-item">Print</a>' +
                    '<a club-Items-action="fullscreen" class="dropdown-item">Fullscreen</a>' +
                    '<div class="dropdown-divider" />' +
                    '<div class="dropdown-header border-bottom-0 pt-0"><small>Download as</small></div>' +
                    '<a club-Items-action="csv" class="dropdown-item">CSV (.csv)</a></div>')
            }
        }),
        $body = $("body");
    $body.on("click", "[club-Items-action]", function(e) {
        e.preventDefault();
        var t = $(this).attr("club-Items-action");
        if ("excel" === t && $("#club-Items_wrapper").find(".buttons-excel").click(), "csv" === t && $("#club-Items_wrapper").find(".buttons-csv").click(), "print" === t && $("#club-Items_wrapper").find(".buttons-print").click(), "fullscreen" === t) {
            var a = $(this).closest(".card");
            a.hasClass("card--fullscreen") ? (a.removeClass("card--fullscreen"), $body.removeClass("club-Items-toggled")) : (a.addClass("card--fullscreen"), $body.addClass("club-Items-toggled"))
        }
    });
    //}
}


$(document).on("click", ".actionbutton", function() {
    console.log('actionbutton clicked');
    let record_id = $(this).attr('attr-id');
    let click_act = $(this).attr('attr-act');
    console.log(record_id);
    console.log(click_act);
    update_kid_status(record_id, click_act);
    // $(this).parent().remove();
});


function update_kid_status(record_id, click_act) {


    var b = {
        "record_id": record_id,
        "click_act": click_act,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/client-update-kid-status",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxLoginloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxLoginloader").css("visibility", "visible");
            $(".removeLoginMessages").html("");
            $(".removeLoginMessages").css("visibility", "hidden");
            $(".clientlogin-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');

            Lobibox.notify('default', {
                title: 'Updating',
                msg: 'Please wait...',
                pauseDelayOnHover: true,
                continueDelayOnInactiveTab: false,
                position: 'center top',
                showClass: 'fadeInDown',
                hideClass: 'fadeOutDown',
                width: 600,
            });
        },
        success: function(e) {
            if (e.status == 'ok') {

                Lobibox.notify('default', {
                    title: 'Updated',
                    msg: 'Refreshing...',
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                get_my_kids();
            } else {
                Lobibox.notify('default', {
                    title: 'Update error',
                    msg: 'Error occurred, please try again later...',
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                $(".ajaxLoginloader").html("");
                $(".ajaxLoginloader").css("visibility", "hidden");
                $(".removeLoginMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxLoginloader").html("");
            $(".ajaxLoginloader").css("visibility", "hidden");
            $(".removeLoginMessages").css("visibility", "visible");
            $(".clientlogin-btn").prop('disabled', false).html('Log In');
            $(".clientlogin-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    //c.preventDefault()
}
