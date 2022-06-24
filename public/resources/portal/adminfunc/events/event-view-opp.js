let searchParams = new URLSearchParams(window.location.search);
let record_id = '';
if (searchParams.has('rec-id')) {
    record_id = searchParams.get('rec-id');
} else {
    console.log('trainer id not set');
}
try {
    record_id = parseInt(record_id);
    console.log(record_id);
    if (isNaN(record_id) || record_id < 1) {
        console.log('not a good number');
    }
} catch (Exception) {}

$('#status-select').select2({
    selectOnClose: !0
});

//get_all_service_list();
// get_all_trainer_list();
// get_all_age_brackets();
// get_all_type_of_training();

get_aid_members(record_id);
get_all_event_summary(record_id);

function get_aid_members(record_id) {
    $('#my-sessions').DataTable().destroy();
    var requiredfunction = {
        'record_id': record_id,
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
                "url": "/admin-vw-all-new-sessions-by-id",
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


function get_all_service_listX() {
    var b = {
        "record_id": record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/admin-vw-all-new-sessions-by-id",
        data: b,
        dataType: "json",

        success: function(e) {
            if (e.status == 'ok') {
                var s = '<option value="">Select service</option>';
                console.log(e);
                let var_data = e.data;

                console.log(var_data.session_title);

                $('#record_id').val(var_data.record_id);
                $('#session_title').html(var_data.session_title);
                console.log(var_data.title);
                console.log(var_data.full_names);
                //$('#editor').val(var_data.blog);
                $('#created_on').html(var_data.created_on);
                $('#mobile').html(var_data.mobile);
                $('#email').html(var_data.email);
                $('#address').html(var_data.address);
                $('#under_taking_study').html(var_data.under_taking_study);


                $('#completed_studies').html(var_data.completed_studies);
                $('#volunteer_exp').html(var_data.volunteer_exp);
                $('#prefered_age_group').html(var_data.prefered_age_group);
                $('#worked_wit_able_diff').html(var_data.worked_wit_able_diff);
                $('#coaching_phil').html(var_data.coaching_phil);

                let school_data = var_data.results_school;
                let results_certification = var_data.results_certification;
                let results_employment = var_data.results_employment;
                let results_sport = var_data.results_sport;
                let results_availability = var_data.results_availability;

                let schooltable = '<table style="width:100%">\n' +
                    '  <tr>\n' +
                    '    <th>Institution</th>\n' +
                    '    <th>Program</th>\n' +
                    '    <th>Year of Completion</th>\n' +
                    '  </tr>\n';
                $.each(school_data, function(key, val) {
                    console.log('val', val);
                    schooltable += '  <tr>\n' +
                        '    <td>' + val.institution + '</td>\n' +
                        '<td>' + val.institution + '</td>\n' +
                        '<td>' + val.year_of_completion + '</td>\n' +
                        '</tr>\n';
                })
                schooltable += '</table>';
                $('#institutions').html(schooltable);
                ////sdggg
                let results_certification_span = '<table style="width:100%">\n' +
                    '  <tr>\n' +
                    '    <th>Certificate</th>\n' +
                    '    <th>Level</th>\n' +
                    '  </tr>\n';
                $.each(results_certification, function(key, val) {
                    console.log('val', val);
                    results_certification_span += '  <tr>\n' +
                        '    <td>' + val.certification + '</td>\n' +
                        '<td>' + val.level + '</td>\n' +
                        '</tr>\n';
                })
                results_certification_span += '</table>';
                $('#certs').html(results_certification_span);
                ///asfas
                let results_emp_span = '<table style="width:100%">\n' +
                    '  <tr>\n' +
                    '    <th>Employer</th>\n' +
                    '    <th>Position/Responsibilities</th>\n' +
                    '    <th>Reason for leaving</th>\n' +
                    '  </tr>\n';
                $.each(results_employment, function(key, val) {
                    console.log('val', val);
                    results_emp_span += '  <tr>\n' +
                        '<td>' + val.certification + '</td>\n' +
                        '<td>' + val.level + '</td>\n' +
                        '<td>' + val.level + '</td>\n' +
                        '</tr>\n';
                })
                results_emp_span += '</table>';
                $('#emplymnt').html(results_emp_span);
                ///asfas
                let results_sport_span = '<table style="width:100%">\n' +
                    '  <tr>\n' +
                    '    <th>Activity</th>\n' +
                    '    <th>Age Group</th>\n' +
                    '  </tr>\n';
                $.each(results_sport, function(key, val) {
                    console.log('val', val);
                    results_sport_span += '  <tr>\n' +
                        '<td>' + val.activity + '</td>\n' +
                        '<td>' + val.age_group + '</td>\n' +
                        '</tr>\n';
                })
                results_sport_span += '</table>';
                $('#aviltext').html(results_sport_span);
                ///asfas
                let results_availability_span = '<table style="width:100%">\n' +
                    '  <tr>\n' +
                    '    <th>At time</th>\n' +
                    '  </tr>\n';
                $.each(results_availability_span, function(key, val) {
                    console.log('val', val);
                    var availability_dest = '';
                    if (val.id_avl == 1) {
                        availability_dest = 'Full time';
                    }
                    if (val.id_avl == 2) {
                        availability_dest = 'During school hours 8am-3pm';
                    }
                    if (val.id_avl == 3) {
                        availability_dest = 'After school hours 3pm-5pm';
                    }
                    if (val.id_avl == 4) {
                        availability_dest = 'Evening 5pm-9pm';
                    }
                    if (val.id_avl == 5) {
                        availability_dest = 'Weekends';
                    }
                    results_sport_span += '  <tr>\n' +
                        '<td>' + availability_dest + '</td>\n' +
                        '</tr>\n';
                })
                results_availability_span += '</table>';
                $('#availspan').html(results_sport_span);
                ///asfas

            }
        },

        complete: function() {

        }
    });

}

function get_all_event_summary(record_id) {
    var b = {
        'record_id': record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-event-by-id-fe-api",
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
            var item_divs = '';
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                var data_export = e.data;
                let data = e.data['data'];
                var age_brackets_string = '| ';
                var training_types_string = '';
                $.each(data, function(key, val) {
                    console.log(key);
                    console.log(val);
                    var age_brackets = val.age_brackets;
                    var type_of_trainings = val.type_of_trainings;
                    $.each(age_brackets, function(key, val) {
                        console.log(val);
                        age_brackets_string += val.description + ' | ';

                        $.each(e.data, function(key, val) {
                            //console.log(val.age_bracket);
                            //s += '<option value="' + val.record_id + '">' + val.description + "</option>";
                            //$('#age-bracket').select2('val', val.record_id);
                            // $('#age-bracket').find(val.age_bracket).attr('selected', true);
                            // $('#age-bracket').select2();
                        });
                        // $('#age-bracket').append(s);
                        $('#age-bracket').select2({
                            selectOnClose: !0
                        });
                    })
                    $.each(type_of_trainings, function(key, val) {
                        console.log(val);
                        training_types_string += '<ul class="service-list">' +
                            '<li>' +
                            '<a href="#">' + val.description + '</a>' +
                            '</li>' +
                            '</ul>';
                        let tr_val = parseInt(val.training_type);

                        let training_val_arr = [tr_val];
                        console.log(training_val_arr);
                        $('#type-of-training').find(training_val_arr).attr('selected', true);
                        $('#type-of-training').select2();
                    })

                    let event_dates = val.start_date + ' <span> - </span> ' + val.end_date;
                    $('#session_title').html(val.session_title);
                    $('#start_date').html(val.start_date);
                    $('#max-attendee-span').html(val.max_attendee);
                    $('#chaperone-allowed-span').html(val.chaperone_allowed);
                    $('#age-bracket-span').html(age_brackets_string);
                    $('.age-bracket-span').html(age_brackets_string);
                    $('#dates-span').html(event_dates);
                    $('#sdbar-div').html(training_types_string);
                    $('#event-thumbnail').attr('src', val.thumbnail);
                    $('#event-name').html(val.session_title);
                    $('#tag_line').html(val.tag_line);
                    $('#location').html(val.location);
                    $('#start_date').html(val.start_date);
                    $('#end_date').html(val.end_date);

                    $("#session-name").val(val.session_title);
                    $("#session-date").val(val.start_date);
                    $("#session-end-date").val(val.end_date);
                    $("#session-location").val(val.location);
                    $("#tag-line").val(val.tag_line);


                    $("#num_sessions").val(val.number_of_sessions);
                    $("#cost").val(val.cost);
                    // $("#max-attendees").val(val.max_attendee);
                    // $("#num-chaperone-alllowed").val(val.chaperone_allowed);
                })
            } else {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
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

function get_all_age_brackets() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-all-age-group-list",
        data: b,
        dataType: "json",

        success: function(e) {
            if (e.status == 'ok') {

                var s = '<option value="">Select age group</option>';
                var ex = '';

                $.each(e.data, function(key, val) {
                    s += '<option value="' + val.record_id + '">' + val.description + "</option>";
                });
                $('#age-bracket').append(s);
                $('#age-bracket').select2({
                    selectOnClose: !0
                });
            }
        },
        complete: function() {
            //pass
        }
    });

}

function get_all_type_of_training() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-all-age-training-list",
        data: b,
        dataType: "json",

        success: function(e) {
            if (e.status == 'ok') {

                var s = '<option value="">Select trainings</option>';
                var ex = '';

                $.each(e.data, function(key, val) {
                    s += '<option value="' + val.record_id + '">' + val.description + "</option>";
                });
                $('#type-of-training').append(s);
                $('#type-of-training').select2({
                    selectOnClose: !0
                });
            }
        },
        complete: function() {
            //pass
        }
    });

}