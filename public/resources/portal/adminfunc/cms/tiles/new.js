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
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-all-tiles-list",
        data: b,
        dataType: "json",

        success: function (e) {
            if (e.status == 'ok') {

                var s = '<option value="">Select service</option>';
                var ex = '';

                $.each(e.data, function (key, val) {
                    s += '<option value="' + val.record_id + '">' + val.service_name + "</option>";
                });
                $('#service-select').append(s);
                $('#service-select').select2({
                    selectOnClose: !0
                });
            }
        },
        complete: function () {

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

        beforeSend: function () {
            $("#div-trainerlist").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
        },
        success: function (e) {
            if (e.status == 'ok') {

                var s = '';
                var p = '';
                var ex = '';

                $.each(e.data, function (key, val) {

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
        complete: function () {
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

        success: function (e) {
            if (e.status == 'ok') {

                var s = '<option value="">Select age group</option>';
                var ex = '';

                $.each(e.data, function (key, val) {
                    s += '<option value="' + val.record_id + '">' + val.description + "</option>";
                });
                $('#age-bracket').append(s);
                $('#age-bracket').select2({
                    selectOnClose: !0
                });
            }
        },
        complete: function () {
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

        success: function (e) {
            if (e.status == 'ok') {

                var s = '<option value="">Select trainings</option>';
                var ex = '';

                $.each(e.data, function (key, val) {
                    s += '<option value="' + val.record_id + '">' + val.description + "</option>";
                });
                $('#type-of-training').append(s);
                $('#type-of-training').select2({
                    selectOnClose: !0
                });
            }
        },
        complete: function () {
            //pass
        }
    });

}




(Dropzone.options.myAwesomeDropzone = {
    paramName: "eventfiles",
    maxFilesize: 8,
    parallelUploads: 8,
    maxFiles: 10,
    autoProcessQueue: !1,
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    dictFileTooBig: "File is to big ({{filesize}}mb). Max allowed file size is {{maxFilesize}}mb",
    dictInvalidFileType: "Invalid File Type",
    dictCancelUpload: "Cancel",
    dictRemoveFile: "Remove this",
    dictMaxFilesExceeded: "Only {{maxFiles}} files are allowed",
    dictDefaultMessage: "Drop files here to upload. You can also just click here.",
    url: "/create-new-eventtile",
    uploadMultiple: true,
    autoDiscover: false,
    accept: function (e, a) {
        "uda.jpg" == e.name ? a("Nah, you just didn't.") : a();
    },
    init: function () {
        var e = this;
        $("#new-event").click(function (a) {
            if ((a.preventDefault(), a.stopPropagation(), e.getQueuedFiles().length > 0)) e.processQueue();
            else submitMyFormWithData();
            e.processQueue();
            $('#preview').modal('hide');
        }),
            this.on("sending", function (e, a, s) {
                $(".removeRegMessages").html(""),
                    $(".removeRegMessages").css("visibility", "visible"),
                    $(".removeRegMessages").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');

                var blog_title = $("#blog-title").val();
                var editor = $("#editor").val();
                //console.log(editor);

                var t = $("#new-event-form").serializeArray();

              
                t.push({
                    name: "token",
                    value: localStorage.token
                });
                t.push({
                    name: "editor",
                    value: editor
                });
                t.push({
                    name: "blog_title",
                    value: blog_title
                });


                console.log(t);
                console.log(e),
                    console.log(a);
                console.log(s),
                    console.log(t),
                    $.each(t, function (e, a) {
                        s.append(a.name, a.value);
                    }),
                    console.log(s);
            }),
            this.on("success", function (e, a) {
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
        this.on("error", function (e, a) {
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
    $('#preview').modal('hide');

    var blog_title = $("#blog-title").val();
    var editor = $("#editor").val();
    //console.log(editor);

    //console.log(selected_trainers);

    if (blog_title == '') {
        $('#blog-title').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter image description.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#blog-title').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#blog-title').removeClass('is-invalid');
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
        name: "editor",
        value: editor
    });
    

    $.ajax({
        type: "post",
        url: "/create-new-eventtile",
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

}




$("#new-event-popup").click(function (cccc) {
    console.log('Preview clicked');
    var blog_title = $("#blog-title").val();
    var teaser = $("#teaser").val();
    let editor = $("#editor").val();

    console.log(blog_title);
    console.log(teaser);
    console.log(editor);

    $("#preview-title").html(blog_title);
    $("#preview-teaser").html(teaser);
    $("#blog-body").html(editor);
    $('#preview').modal('show');
    cccc.preventDefault()
});