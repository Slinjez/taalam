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

get_all_service_list();
get_all_trainer_list();
get_all_age_brackets();
get_all_type_of_training();
get_event_status();

function get_event_status() {
    let event_statuses = {
        0: "Ice Box",
        1: "Ongoing",
        2: "Past Event",
        3: "Cancelled",
    }
    let s = '';
    $.each(event_statuses, function(key, val) {
        console.log(val);
        s += '<option value="' + key + '">' + val + "</option>";
    });
    $('#status-select').append(s);
    $('#status-select').select2({
        selectOnClose: !0
    });
}

function get_all_service_list() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-all-services-list",
        data: b,
        dataType: "json",
        success: function(e) {
            if (e.status == 'ok') {
                var s = '<option value="">Select service</option>';
                var ex = '';
                $.each(e.data, function(key, val) {
                    s += '<option value="' + val.record_id + '">' + val.service_name + "</option>";
                });
                $('#service-select').append(s);
                $('#service-select').select2({
                    selectOnClose: !0
                });
            }
        },
        complete: function() {}
    });
}

function get_all_trainer_list() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-all-trainer-list",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $("#div-trainerlist").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
        },
        success: function(e) {
            if (e.status == 'ok') {
                var s = '';
                var p = '';
                var ex = '';
                $.each(e.data, function(key, val) {
                    s += '<ul class="list-group list-group-flush">' +
                        '<li class="list-group-item">' +
                        '<p class="mb-0">' +
                        '<div class="custom-control custom-checkbox">' +
                        '<input type="checkbox" name="" class="custom-control-input trainer-checkbox" value="' + val.record_id + '" id="' + val.record_id + '">' +
                        '<label class="custom-control-label" for="' + val.record_id + '"><i class=\'bx bxs-arrow-to-right\'></i> ' + val.user_name + '</label>' +
                        '</p>' +
                        '</li>' +
                        '</ul>';
                });
                $('#div-trainerlist').html(s);
            }
        },
        complete: function() {}
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
        complete: function() {}
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
        complete: function() {}
    });
}
(Dropzone.options.myAwesomeDropzone = {
    paramName: "eventfiles",
    maxFilesize: 8,
    parallelUploads: 8,
    maxFiles: 1,
    autoProcessQueue: !1,
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    dictFileTooBig: "File is to big ({{filesize}}mb). Max allowed file size is {{maxFilesize}}mb",
    dictInvalidFileType: "Invalid File Type",
    dictCancelUpload: "Cancel",
    dictRemoveFile: "Remove this",
    dictMaxFilesExceeded: "Only {{maxFiles}} files are allowed",
    dictDefaultMessage: "Drop files here to upload. You can also just click here.",
    url: "/update-new-session",
    uploadMultiple: true,
    autoDiscover: false,
    accept: function(e, a) {
        "uda.jpg" == e.name ? a("Nah, you just didn't.") : a();
    },
    init: function() {
        var e = this;
        $("#new-event").click(function(a) {
                if ((a.preventDefault(), a.stopPropagation(), e.getQueuedFiles().length > 0)) e.processQueue();
                else submitMyFormWithData();
                e.processQueue();
            }),
            this.on("sending", function(e, a, s) {
                $(".removeRegMessages").html(""),
                    $(".removeRegMessages").css("visibility", "visible"),
                    $(".removeRegMessages").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
                var session_name = $("#session-name").val();
                var service_select = $("#service-select").select2('val');
                var session_date = $("#session-date").val();
                var session_end_date = $("#session-end-date").val();
                var session_location = $("#session-location").val();
                var max_attendees = $("#max-attendees").val();
                var age_bracket = $("#age-bracket").val();
                var type_of_training = $("#type-of-training").val();
                var num_chaperone_alllowed = $("#num-chaperone-alllowed").val();
                var tag_line = $("#tag-line").val();
                let editor = $("#editor-1").find('.editorAria').html();

                let record_id = record_id;
                selected_trainers = $('.trainer-checkbox:checked').map(function() {
                    return $(this).attr("value");
                }).get();
                var t = $("#new-event-form").serializeArray();
                t.push({
                    name: "record_id",
                    value: record_id
                });
                t.push({
                    name: "num_chaperone_allowed",
                    value: num_chaperone_alllowed
                });
                t.push({
                    name: "type_of_training",
                    value: type_of_training
                });
                t.push({
                    name: "age_bracket",
                    value: age_bracket
                });
                t.push({
                    name: "max_attendees",
                    value: max_attendees
                });
                t.push({
                    name: "selected_trainers",
                    value: selected_trainers
                });
                t.push({
                    name: "editor",
                    value: editor
                });
                t.push({
                    name: "session_name",
                    value: session_name
                });
                t.push({
                    name: "service_select",
                    value: service_select
                });
                t.push({
                    name: "session_date",
                    value: session_date
                });
                t.push({
                    name: "session_end_date",
                    value: session_end_date
                });
                t.push({
                    name: "session_location",
                    value: session_location
                });
                t.push({
                    name: "token",
                    value: localStorage.token
                });
                t.push({
                    name: "tag_line",
                    value: tag_line
                });
                console.log(t);
                console.log(e),
                    console.log(a);
                console.log(s),
                    console.log(t),
                    $.each(t, function(e, a) {
                        s.append(a.name, a.value);
                    }),
                    console.log(s);
            }),
            this.on("success", function(e, a) {
                console.log("Response from server"),
                    console.log(a),
                    $(".removeRegMessages").html(""),
                    $(".removeRegMessages").css("visibility", "hidden");
                if (a.status == "ok") {
                    $(".ajaxDataloader").html("");
                    $(".ajaxDataloader").css("visibility", "hidden");
                    $(".removeBookMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + a.messages + "</div>");
                    $(".removeBookMessages").css("visibility", "visible");
                    $('html, body').animate({
                        scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
                    }, 2000);
                    Lobibox.notify('default', {
                        title: 'Feedback',
                        msg: a.messages,
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
                    $(".removeBookMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + a.messages + "</div>")
                    $(".removeBookMessages").css("visibility", "visible");
                    $('html, body').animate({
                        scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
                    }, 2000);
                    Lobibox.notify('default', {
                        title: 'Feedback',
                        msg: a.messages,
                        pauseDelayOnHover: true,
                        continueDelayOnInactiveTab: false,
                        position: 'center top',
                        showClass: 'fadeInDown',
                        hideClass: 'fadeOutDown',
                        width: 600,
                    });
                }
                Dropzone.forElement('#my-awesome-dropzone').removeAllFiles(true)
            });
        this.on("error", function(e, a) {
            console.log("Response from server"),
                console.log(a),
                Dropzone.forElement('#my-awesome-dropzone').removeAllFiles(true);
            $(".ajaxDataloader").html("");
            $(".ajaxDataloader").css("visibility", "hidden");
            $(".removeBookMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            $(".removeBookMessages").css("visibility", "visible");
            $('html, body').animate({
                scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
            }, 2000);
        });
    },
});

function submitMyFormWithData(c) {
    var session_name = $("#session-name").val();
    var service_select = $("#service-select").select2('val');
    var session_date = $("#session-date").val();
    var session_end_date = $("#session-end-date").val();
    var session_location = $("#session-location").val();
    var tag_line = $("#tag-line").val();
    var max_attendees = $("#max-attendees").val();
    var age_bracket = $("#age-bracket").val();
    var type_of_training = $("#type-of-training").val();
    var num_chaperone_alllowed = $("#num-chaperone-alllowed").val();
    let editor = $("#editor-1").find('.editorAria').html();
    selected_trainers = $('.trainer-checkbox:checked').map(function() {
        return $(this).attr("value");
    }).get();
    if (session_name == '') {
        $('#session-name').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter event name.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#session-name').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#session-name').removeClass('is-invalid');
    }
    if (service_select == '') {
        $('#service-select').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly select services.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#service-select').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#service-select').removeClass('is-invalid');
    }
    if (session_date == '') {
        $('#session-date').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly select event start date and time.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#session-date').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#session-date').removeClass('is-invalid');
    }
    if (session_end_date == '') {
        $('#session-end-date').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly select event end date and time.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#session-end-date').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#session-end-date').removeClass('is-invalid');
    }
    if (session_location == '') {
        $('#session-location').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly set event location.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#session-location').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#session-location').removeClass('is-invalid');
    }
    if (editor == '') {
        $('#editor').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly describe the event.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#editor').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#editor').removeClass('is-invalid');
    }
    if (max_attendees == '') {
        $('#max-attendees').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter max attendees.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#max-attendees').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#max-attendees').removeClass('is-invalid');
    }
    if (age_bracket == '') {
        $('#age-bracket').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly select age brackets.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#age-bracket').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#age-bracket').removeClass('is-invalid');
    }
    if (type_of_training == '') {
        $('#type-of-training').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly select type of training.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#type-of-training').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#type-of-training').removeClass('is-invalid');
    }
    if (num_chaperone_alllowed == '') {
        $('#num-chaperone-alllowed').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter max chaperone allowed.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#num-chaperone-alllowed').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#num-chaperone-alllowed').removeClass('is-invalid');
    }
    if (selected_trainers.length == 0) {
        $('#div-trainerlist').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly select some trainers for the event.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#div-trainerlist').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#div-trainerlist').removeClass('is-invalid');
    }
    if (tag_line == '') {
        $('#tag-line').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter an event tag.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#tag-line').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#tag-line').removeClass('is-invalid');
    }
    var e = $("#new-event-form").serializeArray();
    t.push({
        name: "num_chaperone_allowed",
        value: num_chaperone_alllowed
    });
    t.push({
        name: "type_of_training",
        value: type_of_training
    });
    t.push({
        name: "age_bracket",
        value: age_bracket
    });
    t.push({
        name: "max_attendees",
        value: max_attendees
    });
    e.push({
        name: "selected_trainers",
        value: selected_trainers
    });
    e.push({
        name: "editor",
        value: editor
    });
    e.push({
        name: "session_name",
        value: session_name
    });
    e.push({
        name: "service_select",
        value: service_select
    });
    e.push({
        name: "session_date",
        value: session_date
    });
    e.push({
        name: "session_end_date",
        value: session_end_date
    });
    e.push({
        name: "session_location",
        value: session_location
    });
    e.push({
        name: "token",
        value: localStorage.token
    });
    e.push({
        name: "tag_line",
        value: tag_line
    });
    e.push({
        name: "record_id",
        value: record_id
    });

    $.ajax({
        type: "post",
        url: "/update-new-session",
        data: e,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxDataloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxDataloader").css("visibility", "visible");
            $(".removeBookMessages").html("");
            $(".removeBookMessages").css("visibility", "hidden");
            $(".new-event").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
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
        complete: function() {
            $(".ajaxDataloader").html("");
            $(".ajaxDataloader").css("visibility", "hidden");
            $(".removeBookMessages").css("visibility", "visible");
            $(".new-event").prop('disabled', false).html('Update Session');
            $(".new-event-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
            $('html, body').animate({
                scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
            }, 2000);
        }
    });
    c.preventDefault()
}
/**
 * 
 * 
 * EDIT
 * 
 * 
 */

get_services(record_id);
$('#record-id').val(record_id);

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
                    $('#desc-body').html(val.description);
                    $('#editor').html(val.description);
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