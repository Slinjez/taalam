let searchParams = new URLSearchParams(window.location.search);

let record_id = '';

if (searchParams.has('rec-id')) {
    record_id = searchParams.get('rec-id');
} else {
    console.log('trainer id not set');
    window.location.href = "/current-activities";
}
try {
    record_id = parseInt(record_id);
    console.log(record_id);

    if (isNaN(record_id) || record_id < 1) {
        console.log('not a good number');
        window.location.href = "/current-activities";
    }

} catch (Exception) {
    window.location.href = "/current-activities";
}

/***
 * In the streets it's getting hot
 * And the youths dem a get so cold..
 */
const global_recordid = record_id;
get_services(record_id);
get_my_kids(record_id);
get_my_trainers(record_id);


function get_my_trainers(record_id) {
    $('#my-sessions-trainers').DataTable().destroy();
    var requiredfunction = {
        'record_id': record_id,
        'token': localStorage.token,
    };
    $("#my-sessions-trainers").DataTable({
        order: [
            [1, 'asc']
        ],
        // rowGroup: {
        //     dataSrc: 0
        // },
        "ajax": {
            "data": requiredfunction,
            "url": "/get-api-event-trainer-list",
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


function get_my_kids(record_id) {
    $('#my-sessions').DataTable().destroy();
    var requiredfunction = {
        'record_id': record_id,
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
            "url": "/get-my-active-event-kids-list",
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



function get_services(record_id) {
    var b = {
        'record_id': record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-booked-event-by-id-fe-api-ovr",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $('.loading-div').removeClass('hide-me');
            $('.results-div').addClass('hide-me');
            $(".ajaxDataloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxDataloader").css("visibility", "visible");
            $(".removeDataMessages").html("");
            $(".removeDataMessages").css("visibility", "hidden");
            $(".clientData-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function (e) {
            var item_divs = '';
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                var data_export = e.data;
                // $.each(data_export.data, function (key, val) {
                //     var services = '| ';
                //     var trainer_data = val.type_of_trainings;
                //     $.each(trainer_data, function (key, val) {
                //         //console.log(val);
                //         services += val.description + ' | ';

                //         // var i;
                //         // for (i in val) {
                //         //     if (val.hasOwnProperty(i)) {
                //         //         services+=val.description+' | ';
                //         //     }
                //         // }
                //     })
                //     var tag_line = val.tag_line;
                //     services = truncate(services, 25);
                //     tag_line = truncate(tag_line, 80);
                //     var session_title = truncate(val.session_title, 40);
                //     var service_pill = 'Training activities: <span class="badge badge-light">' + services + '</span>';
                //     item_divs += '<div class="classess-block col-lg-4 col-md-6 col-sm-12">' +
                //         '<div class="inner-box elem-paro">' +
                //         '<div class="image">' +
                //         '<a href="/current-activities-view?ref=' + val.record_id + '"><img src="' + val.thumbnail + '" alt="" /></a>' +
                //         '<div class="icon-box">' +
                //         '<span class="icon flaticon-tools-and-utensils-1"></span>' +
                //         '</div>' +
                //         '</div>' +
                //         '<div class="lower-content">' +
                //         '<div class="title">' + service_pill + '</div>' +
                //         '<h4><a href="/current-activities-view?ref=' + val.record_id + '">' + session_title + '</a></h4>' +
                //         '<div class="text">'+tag_line+'</div>' +
                //         '<a class="read-more" href="/current-activities-view?ref=' + val.record_id + '">More Details</a>' +
                //         '</div>' +
                //         '</div>' +
                //         '</div>';
                // });
                //$('#results-div').html(item_divs);
                let data = e.data['data'];
                //.log(data);
                //let index_zero = data[0];
                var age_brackets_string = '| ';
                var training_types_string = '';
                $.each(data, function (key, val) {
                    console.log(key);
                    console.log(val);
                    var age_brackets = val.age_brackets;
                    var type_of_trainings = val.type_of_trainings;

                    $.each(age_brackets, function (key, val) {
                        console.log(val);
                        age_brackets_string += val.description + ' | ';

                        // var i;
                        // for (i in val) {
                        //     if (val.hasOwnProperty(i)) {
                        //         services += val.description + ' | ';
                        //     }
                        // }
                    })

                    $.each(type_of_trainings, function (key, val) {
                        console.log(val);
                        training_types_string += '<ul class="service-list">' +
                            '<li>' +
                            '<a href="#">' + val.description + '</a>' +
                            '</li>' +
                            '</ul>';
                    })

                    let event_dates = val.start_date + ' <span> - </span> ' + val.end_date;
                    $('#desc-body').html(val.description);
                    $('#max-attendee-span').html(val.max_attendee);
                    $('#chaperone-allowed-span').html(val.chaperone_allowed);
                    $('#age-bracket-span').html(age_brackets_string);
                    $('.age-bracket-span').html(age_brackets_string);
                    $('#dates-span').html(event_dates);
                    $('#sdbar-div').html(training_types_string);


                    $('#event-thumbnail').attr('src', val.thumbnail);
                    $('#event-name').html(val.session_title);
                    $('.number_of_sessions').html(val.number_of_sessions);
                    $('.location').html(val.location);
                    $('.start_date').html(val.start_date);
                    $('.end_date').html(val.end_date);



                    // $('#number_of_sessions').html(val.number_of_sessions);
                    // $('#location').html(val.location);
                    // $('#start_date').html(val.start_date);
                    // $('#end_date').html(val.end_date);

                    var cost = val.cost;
                    var commas = cost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    console.log('amounts', commas);
                    $('#cost-span').html(commas);
                })
                // $('#desc-body').html(index_zero.description);
                // $(".paginate").paginga({
                //     // use default options
                //     itemsPerPage: 6,
                // });
            } else {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function () {
            $('.loading-div').addClass('hide-me');
            $('.results-div').removeClass('hide-me');
            $(".ajaxDataloader").html("");
            $(".ajaxDataloader").css("visibility", "hidden");
            $(".removeDataMessages").css("visibility", "visible");
            $(".clientData-btn").prop('disabled', false).html('Log In');
            $(".clientData-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
}



$("#rating-form").submit(function (c) {
    console.log('Saving rating');
    var stars = $("input[name='stars']:checked").val();
    var send_star = stars;
    var extra_info = $("#extra-info").val();
    var trainer_id = $('#trainer-id-rate').val();
    console.log('stars', stars);
    let star_desc = '';
    if (stars < 1) {
        star_desc = stars + ' star';
    } else {
        star_desc = stars + ' stars';
    }
    stars = stars.length;
    if (stars.length == 0) {
        $('.rating-sel').addClass('is-invalid-selection-div');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly select at least one start.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        // $('html, body').animate({
        //     scrollTop: $('#div-trainerlist').focus().offset().bottom - 5
        // }, 2000);
        return false;
    } else {
        $('.rating-sel').removeClass('is-invalid-selection-div');
    }

    if (extra_info.length == 0) {
        $('#extra-info').addClass('is-invalid-selection-div');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly explain why you rated the trainer ' + star_desc + '.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        // $('html, body').animate({
        //     scrollTop: $('#div-trainerlist').focus().offset().bottom - 5
        // }, 2000);
        return false;
    } else {
        $('#extra-info').removeClass('is-invalid-selection-div');
    }
    //var record_id
    var b = {
        "recordid": global_recordid,
        "stars": send_star,
        "extra_info": extra_info,
        "trainer_id": trainer_id,
        'token': localStorage.token,
    };

    $.ajax({
        type: "post",
        url: "/save-rating",
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
        },
        success: function (e) {
            if (e.status == 'ok') {
                alert_title = e.title;
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);
                $("#rating-modal").modal('hide');
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
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

$("#message-form").submit(function (c) {
    console.log('Saving message');

    var extra_info = $("#msg-extra-info").val();
    var trainer_id = $('#trainer-id-rate-msg').val();

    if (extra_info == 0) {
        $('#msg-extra-info').addClass('is-invalid-selection-div');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter you message.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        // $('html, body').animate({
        //     scrollTop: $('#div-trainerlist').focus().offset().bottom - 5
        // }, 2000);
        return false;
    } else {
        $('.msg-extra-info').removeClass('is-invalid-selection-div');
    }

    //var record_id
    var b = {
        "recordid": global_recordid,
        "extra_info": extra_info,
        "trainer_id": trainer_id,
        'token': localStorage.token,
    };
    console.log('client_message', b);
    $.ajax({
        type: "post",
        url: "/save-message",
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

$(document).on("click", ".actionbutton-rate", function () {
    console.log('actionbutton-rate clicked');
    let attr_id = $(this).attr('attr-id');
    $('#trainer-id-rate').val(attr_id);
    $('#trainer-id-rate-msg').val(attr_id);
    
    //let click_act = $(this).attr('attr-act');
    console.log(attr_id);
    //console.log(click_act);

    //update_kid_status(record_id);
    // $(this).parent().remove();
});

$(document).on("click", ".actionbutton-message", function () {
    console.log('actionbutton-rate clicked');
    let attr_id = $(this).attr('attr-id');
    $('#trainer-id-rate').val(attr_id);
    $('#trainer-id-rate-msg').val(attr_id);
    
    //let click_act = $(this).attr('attr-act');
    console.log(attr_id);
    //console.log(click_act);

    //update_kid_status(record_id);
    // $(this).parent().remove();
});
$(':radio').change(function () {
    console.log('New star rating: ' + this.value);
    let ratingValue = this.value;
    var msg = "";
    if (ratingValue > 1) {
        msg = "Thanks! You rated this " + ratingValue + " star.";
    } else {
        msg = "We will improve ourselves. You rated this " + ratingValue + " stars.";
    }
    $('.rating-feedback').html(msg);
});

function show_lobi_alert_tst(type = null, not_title, not_message) {
    console.log('Show lobi alert called')
    if (type == '') {
        type = 'default';
    }
    if (not_title == '') {
        console.log('null title');
        return false;
    }
    if (not_message == '') {
        console.log('null message');
        return false;
    }
    Lobibox.notify('default', {
        title: not_title,
        msg: not_message,
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'center top',
        showClass: 'fadeInDown',
        hideClass: 'fadeOutDown',
        width: 600,
    });

    Lobibox.notify('default', {
        title: 'test',
        msg: 'test.',
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'center top',
        showClass: 'fadeInDown',
        hideClass: 'fadeOutDown',
        width: 600,
    });
}