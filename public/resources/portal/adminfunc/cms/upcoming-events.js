get_cms_content();
//get_all_trainer_list();
//get_all_age_brackets();
//get_all_type_of_training();

function get_cms_content() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/admin-vw-cms-about-current-activities",
        data: b,
        dataType: "json",

        success: function (e) {
            if (e.status == 'ok') {

                let naked_data = e.data;

                $('#content-title').val(naked_data.title);

                $('#editor').prevAll(".editor-content").html(naked_data.body);
                $('#cms-thumbnail').attr('src', naked_data.side_image);
                $('#created-on').attr('src', naked_data.on_date);
                $('#cms-id').val(naked_data.record_id);
            }
        },
        complete: function () {

        }
    });

}

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
    url: "/update-cms-upcoming-events",
    uploadMultiple: true,
    autoDiscover: false,
    accept: function (e, a) {
        "uda.jpg" == e.name ? a("Nah, you just didn't.") : a();
    },
    init: function () {
        var e = this;
        $("#cms-update").click(function (a) {
            if ((a.preventDefault(), a.stopPropagation(), e.getQueuedFiles().length > 0)) e.processQueue();
            else submitMyFormWithData();
            e.processQueue();
        }),
            this.on("sending", function (e, a, s) {
                $(".removeRegMessages").html(""),
                    $(".removeRegMessages").css("visibility", "visible"),
                    $(".removeRegMessages").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');

                var content_title = $("#content-title").val();
                let editor = $("#editor").val();
                let record_id = $('#cms-id').val();
                //console.log(editor);

                var t = $("#cms-update-form").serializeArray();

                t.push({
                    name: "record_id",
                    value: record_id
                });

                t.push({
                    name: "token",
                    value: localStorage.token
                });
                t.push({
                    name: "content_title",
                    value: content_title
                });
                t.push({
                    name: "editor",
                    value: editor
                });


                $.each(t, function (e, a) {
                    s.append(a.name, a.value);
                })


            }),
            this.on("success", function (e, a) {

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


                    alert_title = 'Feedback.';
                    alert_msg = a.messages;
                    show_lobi_alert(type = null, alert_title, alert_msg);

                } else {
                    $(".ajaxDataloader").html("");
                    $(".ajaxDataloader").css("visibility", "hidden");
                    $(".removeBookMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + a.messages + "</div>")

                    $(".removeBookMessages").css("visibility", "visible");
                    $('html, body').animate({
                        scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
                    }, 2000);

                    alert_title = 'Feedback.';
                    alert_msg = a.messages;
                    show_lobi_alert(type = null, alert_title, alert_msg);
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

    var content_title = $("#content-title").val();

    let editor = $("#editor").val();
    let record_id = $('#cms-id').val();
    //console.log(editor);

    //console.log(selected_trainers);

    if (content_title == '') {
        $('#content-title').addClass('is-invalid');

        alert_title = 'Required.';
        alert_msg = 'Kindly enter blog title.';
        show_lobi_alert(type = null, alert_title, alert_msg);
        $('html, body').animate({
            scrollTop: $('#blog_title').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#content-title').removeClass('is-invalid');
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


    var e = $("#cms-update-form").serializeArray();

    e.push({
        name: "record_id",
        value: record_id
    });
    e.push({
        name: "token",
        value: localStorage.token
    });
    e.push({
        name: "content_title",
        value: content_title
    });

    e.push({
        name: "editor",
        value: editor
    });

    $.ajax({
        type: "post",
        url: "/update-cms-upcoming-events",
        data: e,
        dataType: "json",
        beforeSend: function () {
            $(".ajaxDataloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxDataloader").css("visibility", "visible");
            $(".removeBookMessages").html("");
            $(".removeBookMessages").css("visibility", "hidden");
            $(".cms-update").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function (e) {
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeBookMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                $('html, body').animate({
                    scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
                }, 2000);


                alert_title = 'Feedback.';
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);
            } else {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeBookMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>");
                $('html, body').animate({
                    scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
                }, 2000);

                alert_title = 'Feedback.';
                alert_msg = e.messages;
                show_lobi_alert(type = null, alert_title, alert_msg);
            }
        },
        complete: function () {
            $(".ajaxDataloader").html("");
            $(".ajaxDataloader").css("visibility", "hidden");
            $(".removeBookMessages").css("visibility", "visible");
            $(".cms-update").prop('disabled', false);
            //$(".cms-update").prop('disabled', false).html('Update');
            $(".cms-update-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
            $('html, body').animate({
                scrollTop: $('.removeBookMessages').focus().offset().bottom - 25
            }, 2000);
        }
    });

}