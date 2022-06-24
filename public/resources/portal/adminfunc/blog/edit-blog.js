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

get_all_service_list();
get_all_trainer_list();
get_all_age_brackets();
get_all_type_of_training();

$('#editor').wysiwyg({
    //https://github.com/wdmg/bootstrap-wysiwyg
    // debug: false,
    // mode: 'source'
    // ['components', ['table']],
    toolbar: [
        ['mode'],
        ['operations', ['undo', 'rendo', 'cut', 'copy', 'paste']],
        ['styles'],
        ['fonts', ['size']],
        ['text', ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'font-color', 'bg-color']],
        ['align', ['left', 'center', 'right', 'justify']],
        ['lists', ['unordered', 'ordered', 'indent', 'outdent']],
        ['intervals', ['line-height', 'letter-spacing']],
        ['insert', ['link', 'image', 'video']],
        ['special', ['print', 'unformat', 'visual', 'clean']],
    ],
    // fontSizes: ['8px', ...
    //     '48px'
    // ],
    fontSizeDefault: '12px',
    // fontFamilies: ['Open Sans', 'Arial',
    //     'Times New Roman', 'Verdana'
    // ],
    fontFamilyDefault: 'Open Sans',
    // emojiDefault: [...],
    // symbolsDefault: [...],
    // colorPalette: [...],
    mode: 'editor',
    highlight: true,
    debug: false
});

function get_all_service_list() {
    var b = {
        "record_id": record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/admin-vw-byid-blog",
        data: b,
        dataType: "json",

        success: function(e) {
            if (e.status == 'ok') {
                var s = '<option value="">Select service</option>';
                console.log(e);
                let var_data = e.data;

                $('#blog-title').val(var_data.title);
                $('#teaser').val(var_data.teaser);
                //$('#editor').val(var_data.blog);
                $('#status-disp').html(var_data.unit_ui_display);

                $('#editor').prevAll(".editor-content").html(var_data.blog);
                //$('#editor').val(var_data.over_view);
                //$('#editor-2').val(var_data.responsibilities);
                //$('#editor-3').val(var_data.desirability);
                //$('#editor-4').val(var_data.qualifications);
                // $('#editor-5').val(var_data.commitment);
                // $('#editor-5').val(var_data.commitment);
            }
        },

        complete: function() {

        }
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
        complete: function() {
            //pass
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
    url: "/edit-new-blog",
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

                var blog_title = $("#blog-title").val();
                var teaser = $("#teaser").val();
                let editor = $("#editor").val();
                let status_select = $("#status-select").select2('val');
                //console.log(editor);

                var t = $("#new-event-form").serializeArray();


                //                "record_id": record_id,

                t.push({
                    name: "status_select",
                    value: status_select
                });
                t.push({
                    name: "record_id",
                    value: record_id
                });

                t.push({
                    name: "token",
                    value: localStorage.token
                });
                t.push({
                    name: "blog_title",
                    value: blog_title
                });
                t.push({
                    name: "teaser",
                    value: teaser
                });
                t.push({
                    name: "editor",
                    value: editor
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
                    get_all_service_list();
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

function submitMyFormWithData(cccc) {
    //cccc.preventDefault()

    var blog_title = $("#blog-title").val();
    var teaser = $("#teaser").val();

    //var editorId = $(this).attr('get-data');
    let editor = $("#editor").val();
    let status_select = $("#status-select").select2('val');
    //console.log(editor);

    //console.log(selected_trainers);

    if (blog_title == '') {
        $('#blog_title').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter blog title.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#blog_title').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#blog_title').removeClass('is-invalid');
    }

    if (teaser == '') {
        $('#teaser').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter blog teaser.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#teaser').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#teaser').removeClass('is-invalid');
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


    var e = $("#new-event-form").serializeArray();

    e.push({
        name: "token",
        value: localStorage.token
    });
    e.push({
        name: "blog_title",
        value: blog_title
    });
    e.push({
        name: "teaser",
        value: teaser
    });
    e.push({
        name: "editor",
        value: editor
    });
    //"record_id": record_id,
    e.push({
        name: "record_id",
        value: record_id
    });
    //status_select
    e.push({
        name: "status_select",
        value: status_select
    });

    $.ajax({
        type: "post",
        url: "/edit-new-blog",
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
                get_all_service_list();
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
            $(".new-event").prop('disabled', false).html('Save Event');
            $(".new-event-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
            $('html, body').animate({
                scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
            }, 2000);
        }
    });

}