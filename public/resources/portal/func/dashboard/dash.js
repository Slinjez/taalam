get_service_count();
get_session_count();
get_trainer_count();
get_top_trainers_list();
get_my_short_training_schedule();
get_my_sessions();

function get_top_trainers_list() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-top-trainers",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $('.loading-div').removeClass('hide-me');
            $('.results-div').addClass('hide-me');
            $(".ajaxDataloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxDataloader").css("visibility", "visible");
            $(".removeDataMessages").html("");
            $(".removeDataMessages").css("visibility", "hidden");
            $(".clientData-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            //console.log('get services done');
            //console.log(e.data);
            var item_divs = '';
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");

                var data_export = e.data;
                $.each(data_export.data, function(key, val) {
                    var trainer_data = val.trainer_activities_array;

                    item_divs += '<div class="mt-3 media align-items-center">' +
                        '<img src="' + val.profile_picture + '" width="45" height="45" class="rounded-circle" alt="">' +
                        '<div class="ml-3 media-body">' +
                        '<p class="mb-0 text-white font-weight-bold">' + val.user_name + '</p>' +
                        '<p class="mb-0">Trainer</p>' +
                        '</div> <a href="/client-vw-book-trainer-session?record-id=' + val.record_id + '" class="btn btn-sm btn-light radius-10">Book ' + val.pronoun + '</a>' +
                        '</div>' +
                        '<hr>';

                });

                $('#top-trainers-div').html(item_divs);


                //$("#loading-div").css({'display':'none'});

            } else {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").css("visibility", "hidden");
            }
        },
        complete: function() {
            $('.loading-div').addClass('hide-me');
            $('.results-div').removeClass('hide-me');
            $(".ajaxDataloader").html("");
            $(".ajaxDataloader").css("visibility", "hidden");
            $(".removeDataMessages").css("visibility", "hidden");
            $(".clientData-btn").prop('disabled', false).html('Log In');
            $(".loading-div-trainers").addClass('hide-me');
            $("#loading-div").css({
                'display': 'none'
            });
            $(".clientData-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });

}

function get_my_short_training_schedule() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-my-short-schedule",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $('#sessions-body').html('<tr>' +
                '<td><i class="bx bx-loader bx-spin"></i></td>' +
                '<td><i class="bx bx-loader bx-spin"></i></td>' +
                '<td><i class="bx bx-loader bx-spin"></i></td>' +
                '<td><i class="bx bx-loader bx-spin"></i></td>' +
                '</tr>'
            );
        },
        success: function(e) {
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

                $.each(data_export.data, function(key, val) {
                    //console.log('Data export row:',val);
                    //var session_data = val.trainer_activities_array;             
                    //console.log('Session_data:',session_data);

                    item_divs += '<tr>' +
                        '<td>' + val.display_date + '</td>' +
                        '<td>' + val.service_name + '</td>' +
                        '<td>' + val.trainer_name + '</td>' +
                        '<td>' + val.unit_ui_display + '</td>' +
                        '</tr>';

                });

                $('#sessions-body').html(item_divs);


                //$("#loading-div").css({'display':'none'});

            } else {
                $('#sessions-body').html(e.messages);
            }
        },
        complete: function() {
            //pass
        }
    });

}

function get_service_count() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-service-count",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $('#service-count').html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            console.log(e.service_count);
            if (e.status == 'ok') {
                $('#service-count').html(e.service_count);

            } else {
                $('#service-count').html(e.service_count);
            }
        },
        complete: function() {
            //pass
        }
    });

}

function get_session_count() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-session-count",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $('#session-count').html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            if (e.status == 'ok') {
                $('#session-count').html(e.session_count);

            } else {
                $('#session-count').html(e.session_count);
            }
        },
        complete: function() {
            //pass
        }
    });

}

function get_trainer_count() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-trainer-count",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $('#trainer-count').html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            if (e.status == 'ok') {
                $('#trainer-count').html(e.trainer_count);

            } else {
                $('#trainer-count').html(e.trainer_count);
            }
        },
        complete: function() {
            //pass
        }
    });

}

function get_my_sessions(param = null) {
    $('#my-sessions').DataTable().destroy();
    var requiredfunction = {
        'token': localStorage.token,
    };
    $("#my-sessions").DataTable({
            rowGroup: {
                dataSrc: 1
            },
            "ajax": {
                "data": requiredfunction,
                "url": "/get-client-sessions",
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
            // initComplete: function () {
            //     $(".dataTables_actions").html('<i class="zwicon-more-h" data-toggle="dropdown" />' +
            //         '<div class="dropdown-menu dropdown-menu-right">'
            //         + '<a club-Items-action="print" class="dropdown-item">Print</a>'
            //         + '<a club-Items-action="fullscreen" class="dropdown-item">Fullscreen</a>'
            //         + '<div class="dropdown-divider" />'
            //         + '<div class="dropdown-header border-bottom-0 pt-0"><small>Download as</small></div>'
            //         + '<a club-Items-action="csv" class="dropdown-item">CSV (.csv)</a></div>')
            // }
        }),
        $body = $("body");
    $body.on("click", "[club-Items-action]", function(e) {
        e.preventDefault();
        var t = $(this).attr("club-Items-action");
        // if ("excel" === t && $("#club-Items_wrapper").find(".buttons-excel").click(), "csv" === t && $("#club-Items_wrapper").find(".buttons-csv").click(), "print" === t && $("#club-Items_wrapper").find(".buttons-print").click(), "fullscreen" === t) {
        //     var a = $(this).closest(".card");
        //     a.hasClass("card--fullscreen") ? (a.removeClass("card--fullscreen"), $body.removeClass("club-Items-toggled")) : (a.addClass("card--fullscreen"), $body.addClass("club-Items-toggled"))
        // }
    });


    //}
}