let searchParams = new URLSearchParams(window.location.search);

let record_id = '';

if (searchParams.has('ref')) {
    record_id = searchParams.get('ref');
} else {
    console.log('trainer id not set');
    window.location.href = "/blog";
}
try {
    record_id = parseInt(record_id);
    console.log(record_id);

    if (isNaN(record_id) || record_id < 1) {
        console.log('not a good number');
        window.location.href = "/blog";
    }

} catch (Exception) {
    window.location.href = "/blog";
}

get_services(record_id);

function get_services(record_id) {
    var b = {
        'record_id': record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-opportunity-by-id-fe-api",
        data: b,
        dataType: "json",
        beforeSend: function () {


            $('#over_view').html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $('#responsibilities').html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $('#desirability').html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $('#qualifications').html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $('#commitment').html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');


            $('#post-date').html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $('#title').html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $('#contract_length').html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $('#location').html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');

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
                //         '<a href="/blog-view?ref=' + val.record_id + '"><img src="' + val.thumbnail + '" alt="" /></a>' +
                //         '<div class="icon-box">' +
                //         '<span class="icon flaticon-tools-and-utensils-1"></span>' +
                //         '</div>' +
                //         '</div>' +
                //         '<div class="lower-content">' +
                //         '<div class="title">' + service_pill + '</div>' +
                //         '<h4><a href="/blog-view?ref=' + val.record_id + '">' + session_title + '</a></h4>' +
                //         '<div class="text">'+tag_line+'</div>' +
                //         '<a class="read-more" href="/blog-view?ref=' + val.record_id + '">More Details</a>' +
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

                    let record_id = val.record_id;
                    let title = val.title;
                    let location_job = val.location_job;
                    let contract_length = val.contract_length;
                    let start_date = val.start_date;


                    let over_view = val.over_view;
                    let responsibilities = val.responsibilities;
                    let desirability = val.desirability;
                    let commitment = val.commitment;
                    let qualifications = val.qualifications;

                    $('#post-date').html(start_date);
                    $('#title-jb').html(title);
                    $('.title-txt').html(title);
                    $('#contract_length').html(contract_length);
                    $('#location').html(location_job);


                    $('#apply-link').attr('href', '/apply-opp?ref=' + record_id);

                    $('#over_view').html(over_view);
                    $('#responsibilities').html(responsibilities);
                    $('#desirability').html(desirability);
                    $('#qualifications').html(qualifications);
                    $('#commitment').html(commitment);

                    //$('#dates-span').html(event_dates);

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

/**
 * Institution
 */
let max_institution = 4;
let current_institution_count = 1;

$("#addRow").click(function () {
    current_institution_count++;
    if (current_institution_count > max_institution) {
        Lobibox.notify('default', {
            title: 'Not allowed.',
            msg: 'You have reached max allowed institutions.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        return false;
    }
    console.log('add row clicked');
    // var html = '';
    // html += '<div id="inputFormRow">';
    // html += '<div class="input-group mb-3">';
    // html += '<input type="text" name="title[]" class="form-control m-input" placeholder="Enter title" autocomplete="off">';
    // html += '<div class="input-group-append">';
    // html += '<button id="removeRow" type="button" class="btn btn-danger">Remove</button>';
    // html += '</div>';
    // html += '</div>';
    let html = '';
    html += '<div class="input-group inputFormRow mb-3 ">\n' +
        '																	<div class="col-12 row">\n' +
        '																		<div class="col-4">\n' +
        '																			<input type="text" name="institution_name[]" class="form-control m-input" placeholder="Institution" autocomplete="off">\n' +
        '																		</div>\n' +
        '																		<div class="col-4">\n' +
        '																			<input type="text" name="program[]" class="form-control m-input" placeholder="Program" autocomplete="off">\n' +
        '																		</div>\n' +
        '																		<div class="col-4">\n' +
        '																			<input type="text" name="yom_completion[]" class="form-control m-input" placeholder="Year of completion" autocomplete="off">\n' +
        '\n' +
        '																		</div>\n' +
        '																	</div>\n' +
        '																	<div class="col-12 row">\n' +
        '																		<div class="input-group-append col-12">	\n' +
        '                                                                            <button type="button" class="btn btn-block btn-danger removeinstutionRow">Remove Institution</button>\n' +
        '																		</div>\n' +
        '																	</div>\n' +
        '																</div>';

    $('#newRow').append(html);
});

// remove row
$(document).on('click', '.removeinstutionRow', function () {
    current_institution_count--;
    console.log('remove row clicked');
    $(this).closest('.inputFormRow').remove();
});

/**
 * Institution end 
 */


/**
 * Firstaid
 */
let max_certifications = 4;
let current_certifications = 1;

$("#addRowCertification").click(function () {
    current_certifications++;
    if (current_certifications > max_certifications) {
        Lobibox.notify('default', {
            title: 'Not allowed.',
            msg: 'You have reached max allowed certifications.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        return false;
    }

    let html = '';
    html += '<div id="inputFormRow" class="frm-row-div">\n' +
        '																<div class="input-group mb-3">\n' +
        '																	<div class="col-12 row">\n' +
        '																		<div class="col-4">\n' +
        '																			<input type="text" name="certification[]" class="form-control m-input" placeholder="Institution" autocomplete="off">\n' +
        '																		</div>\n' +
        '																		<div class="col-4">\n' +
        '																			<input type="text" name="certification_level[]" class="form-control m-input" placeholder="Program" autocomplete="off">\n' +
        '																		</div>\n' +
        '																		<div class="col-4">\n' +
        '																			<div class="input-group-append col-12">\n' +
        '																				<button id="removeRow-cert" type="button" class="btn btn-block btn-danger removeRow-cert">Remove Certification</button>\n' +
        '																			</div>\n' +
        '																		</div>\n' +
        '																	</div>\n' +
        '																</div>\n' +
        '															</div>';

    $('#newRowCertifications').append(html);
});

// remove row
$(document).on('click', '.removeRow-cert', function () {
    current_certifications--;
    console.log('remove row clicked');
    $(this).closest('.inputFormRow').remove();
});

/**
 * Firstaid end 
 */


/**
 * EMPLOYER
 */
let max_employer = 4;
let current_employers = 1;

$("#addRow-employer").click(function () {
    current_employers++;
    if (current_employers > max_employer) {
        Lobibox.notify('default', {
            title: 'Not allowed.',
            msg: 'You have reached max allowed employers.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        return false;
    }

    let html = '';
    html += '<div id="inputFormRow" class="frm-row-div inputFormRow">\n' +
        '																<div class="input-group mb-3">\n' +
        '																	<div class="col-12 row">\n' +
        '																		<div class="col-4">\n' +
        '																			<input type="text" name="employer_name[]" class="form-control m-input" placeholder="employer" autocomplete="off">\n' +
        '																		</div>\n' +
        '																		<div class="col-4">\n' +
        '                                                                            <textarea id="w3review" name="employment_position[]" rows="4" cols="50">Position and responsibilities</textarea>\n' +
        '																		</div>\n' +
        '																		<div class="col-4">\n' +
        '																			<textarea id="w3review" name="reason_for_leaving_employment[]" rows="4" cols="50">Reasons for leaving</textarea>\n' +
        '																		</div>\n' +
        '																	</div>\n' +
        '																	<div class="col-12 row">\n' +
        '																		<div class="input-group-append col-12">\n' +
        '																			<button id="removeRow-employer' +
        current_employers + '" type="button" class="btn btn-block btn-danger removeRow-employer">Remove Employer</button>\n' +
        '																		</div>\n' +
        '																	</div>\n' +
        '																</div>\n' +
        '															</div>';

    $('#newRow-employers').append(html);
});

// remove row
$(document).on('click', '.removeRow-employer', function () {
    current_employers++;
    $(this).closest('.inputFormRow').remove();
});

/**
 * EMPLOYER end 
 */




/**
 * Activity
 */
let max_activity = 4;
let current_activity = 1;

$("#addRow-activity").click(function () {
    current_activity++;
    if (current_activity > max_activity) {
        Lobibox.notify('default', {
            title: 'Not allowed.',
            msg: 'You have reached max allowed max_activities.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        return false;
    }

    let html = '';
    html += '<div id="inputFormRow" class="frm-row-div inputFormRow">\n' +
        '																<div class="input-group mb-3">\n' +
        '																	<div class="col-12 row">\n' +
        '																		<div class="col-4">	\n' +
        '																			<input type="text" name="activity_coached[]" class="form-control m-input" placeholder="Activity" autocomplete="off">\n' +
        '																		</div>\n' +
        '																		<div class="col-4">\n' +
        '																			<input type="text" name="age_group_coached[]" class="form-control m-input" placeholder="Age Group" autocomplete="off">\n' +
        '																		</div>\n' +
        '																		<div class="col-4">\n' +
        '                                                                        <div class="input-group-append col-12">\n' +
        '																			<button id="removeRow-employer" type="button" class="btn btn-block btn-danger removeRow-employer">Remove Activity</button>\n' +
        '																		</div>\n' +
        '																		</div>\n' +
        '																	</div>\n' +
        '																</div>\n' +
        '															</div>';

    $('#newRow-activity').append(html);
});

// remove row
$(document).on('click', '.removeRow-employer', function () {
    current_activity--;
    $(this).closest('.inputFormRow').remove();
});

/**
 * Activity end 
 */


/**
 * diff able
 */
let max_diff_able = 4;
let current_diff_able = 1;

$("#addRow-diff-able").click(function () {
    current_diff_able++;
    if (current_diff_able > max_diff_able) {
        Lobibox.notify('default', {
            title: 'Not allowed.',
            msg: 'You have reached max allowed activities.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        return false;
    }

    let html = '';
    html += '<div id="inputFormRow" class="frm-row-div inputFormRow">\n' +
        '																<div class="input-group mb-3">\n' +
        '																	<div class="col-12 row">\n' +
        '																		<div class="col-4">	\n' +
        '																			<input type="text" name="condition_coached_diff_able[]" class="form-control m-input" placeholder="Condition" autocomplete="off">\n' +
        '																		</div>\n' +
        '																		<div class="col-4">\n' +
        '																			<input type="text" name="activity_coached_diff_able[]" class="form-control m-input" placeholder="Activity" autocomplete="off">\n' +
        '																		</div>\n' +
        '																		<div class="col-4">\n' +
        '                                                                        <div class="input-group-append col-12">\n' +
        '																			<button id="removeRow-diff-able" type="button" class="btn btn-block btn-danger removeRow-diff-able">Remove Activity</button>\n' +
        '																		</div>\n' +
        '																		</div>\n' +
        '																	</div>\n' +
        '																</div>\n' +
        '															</div>';

    $('#newRow-diff-able').append(html);
});

// remove row
$(document).on('click', '.removeRow-diff-able', function () {
    current_diff_able--;
    $(this).closest('.inputFormRow').remove();
});

/**
 * diff able end 
 */



function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(email)) {
        return false;
    } else {
        return true;
    }
}

$("#opp-app-form").submit(function (ccccc) {
    ccccc.preventDefault()

    var title = $("#title_iput").val();
    var names = $("#names").val();
    let address = $("#address").val();
    let phone = $("#phone").val();
    let email = $("#email").val();


    let instu_name = $("#instu_name").val();
    let instu_prog = $("#instu_prog").val();
    let instu_completion = $("#instu_completion").val();


    let certification_id = $("#certification_id").val();
    let certification_level = $("#certification_level").val();


    let coach_phil = $("#coach_phil").val();

    var count_checked = $("[name='availability[]']:checked").length;

    var count_declaration = $("[name='declaration']:checked").length;

    console.log('Title:', title);
    if (title == '') {
        $('#title_iput').addClass('is-invalid');
        scrl_to_elem('#title_iput');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter title.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });

        return false;
    } else {
        $('#title_iput').removeClass('is-invalid');
    }

    if (names == '') {
        $('#names').addClass('is-invalid');
        scrl_to_elem('#names');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter your names.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });

        return false;
    } else {
        $('#names').removeClass('is-invalid');
    }

    if (address == '') {
        $('#address').addClass('is-invalid');
        scrl_to_elem('#address');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter your address.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });

        return false;
    } else {
        $('#address').removeClass('is-invalid');
    }

    if (phone == '' || phone.length < 8) {
        $('#phone').addClass('is-invalid');
        scrl_to_elem('#phone');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter your phone.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });

        return false;
    } else {
        $('#phone').removeClass('is-invalid');
    }

    if (email == '' || IsEmail(email) != true) {
        $('#email').addClass('is-invalid');
        scrl_to_elem('#email');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter valid email.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });

        return false;
    } else {
        $('#email').removeClass('is-invalid');
    }

    if (instu_name == '' || instu_prog == '') {
        $('#instu_name').addClass('is-invalid');
        scrl_to_elem('#instu_name');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter at least one institution and program.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });

        return false;
    } else {
        $('#instu_name').removeClass('is-invalid');
    }

    if (certification_id == '' || certification_level == '') {
        $('#certification_id').addClass('is-invalid');
        scrl_to_elem('#certification_id');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter at least one certification.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });

        return false;
    } else {
        $('#certification_id').removeClass('is-invalid');
    }

    if (coach_phil == '' || coach_phil == '') {
        $('#coach_phil').addClass('is-invalid');
        scrl_to_elem('#coach_phil');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter your coaching philosophy.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });

    } else {
        $('#coach_phil').removeClass('is-invalid');
    }

    if (count_checked < 1) {
        $('#fulltype').addClass('is-invalid');
        scrl_to_elem('#fulltype');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly select your availability.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });

        return false;
    } else {
        $('#fulltype').removeClass('is-invalid');
    }

    if (count_declaration < 1) {
        $('#declaration').addClass('is-invalid');
        scrl_to_elem('#declaration');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly accept the declaration.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });


        return false;
    } else {
        $('#declaration').removeClass('is-invalid');
    }

    var e = $("#opp-app-form").serializeArray();

    console.log('submission:', e)

    $.ajax({
        type: "post",
        url: "/opp_apply",
        data: e,
        dataType: "json",
        beforeSend: function () {
            $(".ajaxDataloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxDataloader").css("visibility", "visible");
            $(".removeBookMessages").html("");
            $(".removeBookMessages").css("visibility", "hidden");
            $(".new-event").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function (e) {
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeBookMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                $('html, body').animate({
                    scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
                }, 2000);
                Lobibox.notify('default', {
                    title: 'Feedback',
                    msg: e.messages,
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
            } else {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeBookMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>");
                $('html, body').animate({
                    scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
                }, 2000);
                Lobibox.notify('default', {
                    title: 'Feedback',
                    msg: e.messages,
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
            }
        },
        complete: function () {
            $(".ajaxDataloader").html("");
            $(".ajaxDataloader").css("visibility", "hidden");
            $(".removeBookMessages").css("visibility", "visible");
            $(".new-event").prop('disabled', false).html('Save Event');
            $(".new-event-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
            $('html, body').animate({
                scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
            }, 2000);
        }
    });

})

function scrl_to_elem(elem_id) {
    var element = $(elem_id);
    var elementHeight = element.height();
    var windowHeight = $(window).height();
    var offset = Math.min(elementHeight, windowHeight) + element.offset().top;
    $('html, body').animate({ scrollTop: offset }, 500);
}