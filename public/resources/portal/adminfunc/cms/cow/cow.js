get_my_kids();


function get_my_kids(param = null) {
    $('#my-sessions').DataTable().destroy();
    var requiredfunction = {
        'token': localStorage.token,
    };
    $("#my-sessions").DataTable({
        order: [
            [1, 'asc']
        ],
        // rowGroup: {
        //     dataSrc: 0
        // },
        "ajax": {
            "data": requiredfunction,
            "url": "/client-fetch-cow-list",
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
        initComplete: function () {
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
    $body.on("click", "[club-Items-action]", function (e) {
        e.preventDefault();
        var t = $(this).attr("club-Items-action");
        if ("excel" === t && $("#club-Items_wrapper").find(".buttons-excel").click(), "csv" === t && $("#club-Items_wrapper").find(".buttons-csv").click(), "print" === t && $("#club-Items_wrapper").find(".buttons-print").click(), "fullscreen" === t) {
            var a = $(this).closest(".card");
            a.hasClass("card--fullscreen") ? (a.removeClass("card--fullscreen"), $body.removeClass("club-Items-toggled")) : (a.addClass("card--fullscreen"), $body.addClass("club-Items-toggled"))
        }
    });
    //}
}


$(document).on("click", ".actionbutton", function () {
    console.log('actionbutton clicked');
    let record_id = $(this).attr('attr-id');
    let click_act = $(this).attr('attr-act');
    let img_url = $(this).attr('attr-url');
    console.log(record_id);
    console.log(click_act);
    update_gallery_status(record_id, click_act);
    // $(this).parent().remove();
});

function update_gallery_status(record_id, click_act) {


    var b = {
        "record_id": record_id,
        "click_act": click_act,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/client-update-cow-status",
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


$(document).on("click", ".actionbutton-details", function () {
    console.log('actionbutton-details clicked');
    let attr_id = $(this).attr('attr-id');
    let attr_act = $(this).attr('attr-act');
    let img_url = $(this).attr('attr-url');

    $('#modal-preview').attr('src',img_url);
    $('#sidebar-preview').attr('src',img_url);
    
    $('#preview').modal('show');
    get_kid_details(attr_id);
});


function get_kid_details(attr_id){
    var b = {
        'attr_id': attr_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-cow-full-details",
        data: b,
        dataType: "json",

        success: function (e) {
            console.log(e);
            if (e.status == 'ok') {

                //$('#record_id').html(e.record_id);
                $('#user_name').html(e.user_name);
                $('#award_text').html(e.award_text);
                $('#start_date').html(e.start_date);
                $('#end_date').html(e.end_date);
                //$('#img_url').html(e.img_url);
                $('#img_url').attr('src',e.img_url);
                $('#sidebar-preview').attr('src',e.img_url);
                $('#rating_text').html(e.rating_text);
                $('#unit_ui_display').html(e.unit_ui_display);

                //$('#edit-kid').attr('href','/client-vw-edit-registered-kids?rec-id='+e.record_id);
                $('#edit-kid').attr('href','#');
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
