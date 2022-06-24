get_my_sessions();
function get_my_sessions(param = null) {
    $('#my-sessions').DataTable().destroy();
    var requiredfunction = {
        'token': localStorage.token,
    };
    $("#my-sessions").DataTable({
            order: [
                [1, 'asc']
            ],
            "ajax": {
                "data": requiredfunction,
                "url": "/admin-vw-all-hes",
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
}

$("#message-form").submit(function (c) {
    console.log('Saving message');

    var question = $("#hes-question").val();
    var answer = $('#msg-extra-info').val();

    if (question == 0) {
        $('#hes-question').addClass('is-invalid-selection-div');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter the question.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#hes-question').focus().offset().bottom - 5
        }, 2000);
        return false;
    } else {
        $('#hes-question').removeClass('is-invalid-selection-div');
    }

    if (answer == 0) {
        $('#msg-extra-info').addClass('is-invalid-selection-div');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter the answer.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#msg-extra-info').focus().offset().bottom - 5
        }, 2000);
        return false;
    } else {
        $('#msg-extra-info').removeClass('is-invalid-selection-div');
    }
    //var record_id
    var b = {
        "question": question,
        "answer": answer,
        'token': localStorage.token,
    };
    console.log('client_message', b);
    $.ajax({
        type: "post",
        url: "/save-hes",
        data: b,
        dataType: "json",
        beforeSend: function () {
            alert_title = 'working...';
            alert_msg = 'Please wait...';
            show_lobi_alert(type = null, alert_title, alert_msg);

            $(".ajaxbioloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxbioloader").css("visibility", "visible");
            $(".removebioMessages").html("");
            $(".removebioMessages").css("visibility", "hidden");
            $(".clientbio-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
            $("#message-modal").modal('hide');
        },
        success: function (e) {
            if (e.status == 'ok') {
                alert_title = e.title;
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                get_my_sessions();
            } else {
                alert_title = e.title;
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function () {
            $(".ajaxbioloader").html("");
            $(".ajaxbioloader").css("visibility", "hidden");
            $(".removebioMessages").css("visibility", "visible");
            $(".clientbio-btn").prop('disabled', false).html('Update Profile');
            $(".clientbio-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})


$(document).on("click", ".actionbutton-edit", function () {
    console.log('actionbutton-edit clicked');
    let attr_id = $(this).attr('attr-id');
    let attr_act = $(this).attr('attr-act');
    fetch_hes_details(attr_id);
});



$(document).on("click", ".actionbutton", function () {
    console.log('actionbutton clicked');
    let attr_id = $(this).attr('attr-id');
    let attr_act = $(this).attr('attr-act');
    update_hes_details(attr_id,attr_act);
});


function update_hes_details(attr_id,attr_act) {
    

    var b = {
        "hes_id": attr_id,
        "attr_act": attr_act,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/deactivate-hes",
        data: b,
        dataType: "json",
        beforeSend: function () {
            alert_title = 'working...';
            alert_msg = 'Please wait...';
            show_lobi_alert(type = null, alert_title, alert_msg);

            $(".ajaxbioloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxbioloader").css("visibility", "visible");
            $(".removebioMessages").html("");
            $(".removebioMessages").css("visibility", "hidden");
            $(".clientbio-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
            $("#message-modal").modal('hide');
        },
        success: function (e) {
            if (e.status == 'ok') {
                //alert_title = e.title;
                //alert_msg = e.messages;
                //show_lobi_alert(type = null, alert_title, alert_msg);
    
                //console.log('hes data1',e);
                let hes_data = e.data;
                let hes_data_nest1 = hes_data;

                $.each(hes_data_nest1, function(key, val) {
                    console.log(val[0]);
                    
                $('#hes-id').val(val[0].record_id);
                $('#hes-question-ed').val(val[0].question);
                $('#msg-extra-info-ed').val(val[0].answer);
                $('#new-hes-modal-ed').modal('show');
                    
                })

                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                get_my_sessions();
            } else {
                alert_title = e.title;
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function () {
            $(".ajaxbioloader").html("");
            $(".ajaxbioloader").css("visibility", "hidden");
            $(".removebioMessages").css("visibility", "visible");
            $(".clientbio-btn").prop('disabled', false).html('Update Profile');
            $(".clientbio-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    
}

function fetch_hes_details(attr_id) {
    

    var b = {
        "record_id": attr_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get_hes_by_id",
        data: b,
        dataType: "json",
        beforeSend: function () {
            alert_title = 'working...';
            alert_msg = 'Please wait...';
            show_lobi_alert(type = null, alert_title, alert_msg);

            $(".ajaxbioloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxbioloader").css("visibility", "visible");
            $(".removebioMessages").html("");
            $(".removebioMessages").css("visibility", "hidden");
            $(".clientbio-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
            $("#message-modal").modal('hide');
        },
        success: function (e) {
            if (e.status == 'ok') {
                //alert_title = e.title;
                //alert_msg = e.messages;
                //show_lobi_alert(type = null, alert_title, alert_msg);
    
                //console.log('hes data1',e);
                let hes_data = e.data;
                let hes_data_nest1 = hes_data;

                $.each(hes_data_nest1, function(key, val) {
                    console.log(val[0]);
                    
                $('#hes-id').val(val[0].record_id);
                $('#hes-question-ed').val(val[0].question);
                $('#msg-extra-info-ed').val(val[0].answer);
                $('#new-hes-modal-ed').modal('show');
                    
                })

                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                //get_my_sessions();
            } else {
                alert_title = e.title;
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function () {
            $(".ajaxbioloader").html("");
            $(".ajaxbioloader").css("visibility", "hidden");
            $(".removebioMessages").css("visibility", "visible");
            $(".clientbio-btn").prop('disabled', false).html('Update Profile');
            $(".clientbio-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    
}

$("#message-form-ed").submit(function (c) {
    console.log('Saving message');

    var question = $("#hes-question-ed").val();
    var answer = $('#msg-extra-info-ed').val();
    var hes_id = $('#hes-id').val();

    if (question == 0) {
        $('#hes-question').addClass('is-invalid-selection-div');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter the question.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#hes-question').focus().offset().bottom - 5
        }, 2000);
        return false;
    } else {
        $('#hes-question').removeClass('is-invalid-selection-div');
    }

    if (answer == 0) {
        $('#msg-extra-info').addClass('is-invalid-selection-div');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter the answer.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#msg-extra-info').focus().offset().bottom - 5
        }, 2000);
        return false;
    } else {
        $('#msg-extra-info').removeClass('is-invalid-selection-div');
    }
    //var record_id
    var b = {
        "hes_id": hes_id,
        "question": question,
        "answer": answer,
        'token': localStorage.token,
    };
    console.log('client_message', b);
    $.ajax({
        type: "post",
        url: "/update-hes",
        data: b,
        dataType: "json",
        beforeSend: function () {
            alert_title = 'working...';
            alert_msg = 'Please wait...';
            show_lobi_alert(type = null, alert_title, alert_msg);

            $(".ajaxbioloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxbioloader").css("visibility", "visible");
            $(".removebioMessages").html("");
            $(".removebioMessages").css("visibility", "hidden");
            $(".clientbio-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
            $("#message-modal").modal('hide');
        },
        success: function (e) {
            if (e.status == 'ok') {
                alert_title = e.title;
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                get_my_sessions();
                $('#new-hes-modal-ed').modal('hide');
            } else {
                alert_title = e.title;
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function () {
            $(".ajaxbioloader").html("");
            $(".ajaxbioloader").css("visibility", "hidden");
            $(".removebioMessages").css("visibility", "visible");
            $(".clientbio-btn").prop('disabled', false).html('Update Profile');
            $(".clientbio-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})
