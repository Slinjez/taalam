let searchParams = new URLSearchParams(window.location.search);

let record_id = '';

if (searchParams.has('record-id')) {
    record_id = searchParams.get('record-id');
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
const global_recordid = record_id;
get_services(record_id);
get_my_kids_list(record_id);

function get_my_kids_list() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-my-active-kids-list",
        data: b,
        dataType: "json",

        beforeSend: function () {
            $("#div-trainerlist").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
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
                    $("#div-trainerlist").html('No children set');
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
                        '<label class="custom-control-label" for="' + val.record_id + '"><i class=\'bx bxs-arrow-to-right\'></i> ' + val.kidsname + '</label>' +
                        '</p>' +
                        '</li>' +
                        '</ul>';

                });
                $('#div-trainerlist').html(s);
            }
        },
        complete: function () {
            //pass
        }
    });

}


function get_services(record_id) {
    var b = {
        'record_id': record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-event-by-id-fe-api",
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
                    $('#tag_line').html(val.tag_line);
                    $('#location').html(val.location);
                    $('#start_date').html(val.start_date);
                    $('#end_date').html(val.end_date);
                    var cost=val.cost;
                    //var commas = cost.toLocaleString ("en-US");
                    var commas = cost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    console.log('amounts',commas);
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

$("#book-form").submit(function (c) {
    console.log('Saving booking');
    var number_of_kids = 0;
    var number_of_chaperone = $("#number-of-chaperone").val();
    var extra_info = $("#extra-info").val();
    selected_trainers = $('.trainer-checkbox:checked').map(function () {
        return $(this).attr("value");
    }).get();
    number_of_kids = selected_trainers.length;
    if (selected_trainers.length == 0) {
        $('#div-trainerlist').addClass('is-invalid-selection-div');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly select at least one child.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#div-trainerlist').focus().offset().bottom - 5
        }, 2000);
        return false;
    } else {
        $('#div-trainerlist').removeClass('is-invalid-selection-div');
    }
    //var record_id
    var b = {
        "recordid": global_recordid,
        "number_of_kids": number_of_kids,
        "number_of_chaperone": number_of_chaperone,
        "extra_info": extra_info,
        "selected_trainers": selected_trainers,
        'token': localStorage.token,
    };

    $.ajax({
        type: "post",
        url: "/book_session",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $(".ajaxbioloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxbioloader").css("visibility", "visible");
            $(".removebioMessages").html("");
            $(".removebioMessages").css("visibility", "hidden");
            $(".clientbio-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');

            
            alert_title = 'working...';
            alert_msg = 'Please wait...';
            show_lobi_alert(type = null, alert_title, alert_msg);
        },
        success: function (e) {
            if (e.status == 'ok') {
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");

                alert_title = 'Response';
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);

            } else {
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
                alert_title = 'Response';
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);
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