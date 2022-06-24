get_my_kids();
get_condition_list();

function get_condition_list() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-conditions-list",
        data: b,
        dataType: "json",

        beforeSend: function () {
            $("#div-conditionlist").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
        },
        success: function (e) {
            if (e.status == 'ok') {

                var s = '';
                var p = '';
                var ex = '';
                let resp_size = e.data;
                console.log(resp_size.length);
                if (resp_size.length < 1) {
                    $('.hint-create-kids').removeClass('hide-me');
                    $('#clientbio').prop('disabled', true);
                    $("#div-conditionlist").html('No children set');
                } else {
                    $('.hint-create-kids').addClass('hide-me');
                    $('#clientbio').prop('disabled', false)
                }

                $.each(e.data, function (key, val) {

                    s += '<ul class="list-group list-group-flush">' +
                        '<li class="list-group-item">' +
                        '<p class="mb-0">' +
                        '<div class="custom-control custom-checkbox">' +
                        '<input type="checkbox" name="" class="custom-control-input trainer-checkbox" value="' + val.record_id + '" id="' + val.record_id + '">' +
                        '<label class="custom-control-label" for="' + val.record_id + '">' + val.condition_name + '</label>' +
                        '</p>' +
                        '</li>' +
                        '</ul>';

                });
                $('#div-conditionlist').html(s);
            }
        },
        complete: function () {
            //pass
        }
    });

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
            // rowGroup: {
            //     dataSrc: 0
            // },
            "ajax": {
                "data": requiredfunction,
                "url": "/client-fetch-my-chaperone",
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


$("#bio-form").submit(function(c) {
    var client_full_name = $("#client-full-name").val();
    var relationship = $("#relationship").val();
    var phonenumber = $("#phonenumber").val();
    var email = $("#email").val();
    var location = $("#location").val();
   
    

    var b = {
        "client_full_name": client_full_name,
        "relationship": relationship,
        "phonenumber": phonenumber,
        "email": email,
        "location": location,
        'token': localStorage.token,
    };

    $.ajax({
        type: "post",
        url: "/create-chaperone",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxbioloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxbioloader").css("visibility", "visible");
            $(".removebioMessages").html("");
            $(".removebioMessages").css("visibility", "hidden");
            $(".clientbio-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            if (e.status == 'ok') {
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");

                get_my_kids();
            } else {
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxbioloader").html("");
            $(".ajaxbioloader").css("visibility", "hidden");
            $(".removebioMessages").css("visibility", "visible");
            $(".clientbio-btn").prop('disabled', false).html('Update Profile');
            $(".clientbio-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})

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
        url: "/client-update-chaperone-status",
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
    get_kid_details(attr_id);
});

function get_kid_details(attr_id){
    var b = {
        'attr_id': attr_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-chaperone-full-details",
        data: b,
        dataType: "json",

        success: function (e) {
            if (e.status == 'ok') {

                $('#preview-name').html(e.name);
                $('#disp-relationship').html(e.relationship);
                $('#display_phonenumber').html(e.phonenumber);
                $('#disp-email').html(e.email);
                $('#disp-location').html(e.location);
                $('#disp-display_date').html(e.display_date);

                $('#edit-kid').attr('href','/client-vw-edit-registered-chaperone?rec-id='+e.record_id);
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