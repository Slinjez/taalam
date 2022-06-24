$(document).ready(function () {
    $('#smartwizard').smartWizard({
        selected: 0,
        theme: 'arrows',
        justified: true,
        darkMode: false,
        autoAdjustHeight: true,
        cycleSteps: false,
        backButtonSupport: true,
        enableURLhash: false,
        transition: {
            animation: 'none',
            speed: '400',
            easing: ''
        },
        toolbarSettings: {
            toolbarPosition: 'bottom',
            toolbarButtonPosition: 'right',
            showNextButton: true,
            showPreviousButton: true,
            toolbarExtraButtons: []
        },
        anchorSettings: {
            anchorClickable: true,
            enableAllAnchors: false,
            markDoneStep: true,
            markAllPreviousStepsAsDone: true,
            removeDoneStepOnNavigateBack: false,
            enableAnchorOnDoneStep: true
        },
        keyboardSettings: {
            keyNavigation: false,
            keyLeft: [37],
            keyRight: [39]
        },
        lang: {
            next: 'Next',
            previous: 'Previous'
        },
        disabledSteps: [],
        errorSteps: [],
        hiddenSteps: [],
        onLeaveStep: function (obj, context) {
            var frst = "#step-" + context.fromStep;
            console.log('frst', frst);
            var container = this.elmStepContainer.find(frst);
            if (invalid_fields.length > 0) {
                return false;
            } else {
                return true;
            }
        },
    });
    $("#smartwizard").on("leaveStep", function (obj, context, stepNumber, stepDirection, stepPosition) {
        var pass = '';

        if($('button.sw-btn-next').hasClass('disabled')){
            console.log('Yes next is hidden');
            $('.sw-btn-prev').hide();
            $('.sw-btn-prev').addClass('hide-me');
        }

        console.log('step position', stepPosition);
        if (stepPosition === 'final') {
            $("#smartWizard").smartWizard({
                toolbarSettings: {
                    showPreviousButton: false // To hide Previous Button
                }
            });
        }

        if($('button.sw-btn-next').hasClass('disabled')){
            $('.sw-btn-prev').hide();
            $('.sw-btn-prev').addClass('hide-me');
        }else{
            $('.sw-btn-prev').show();	
            $('.sw-btn-prev').removeClass('hide-me');			
        }

        if (stepDirection < 1) {
            return true;
        } else {
            var is_valid = validateSteps(stepNumber);
            if (!is_valid) {
                return false;
            } else {
                return true;
            }
        }
    });

    $("#smartwizard").on("onShowStep", function () {
        if($('button.sw-btn-next').hasClass('disabled')){
            console.log('Yes next is hidden');
            $('.sw-btn-prev').hide();
            $('.sw-btn-prev').addClass('hide-me');
        }

    })

    function validateSteps(stepnumber) {
        console.log('Step number:', stepnumber);
        var isStepValid = true;
        var hasError = false;
        if (stepnumber == 0) {
            var InputName = $('#InputName').val();
            var InputEmail1 = $('#InputEmail1').val();
            var mobile_number = $('#mobile-number').val();
            var InputAddress = $('#InputAddress').val();
            var Regpassword = $('#loginpassword').val();
            is_error = false;
            if (InputName == '' || InputName.length < 3) {
                $('#InputName').addClass('is-invalid');
                Lobibox.notify('default', {
                    title: 'Required',
                    msg: 'Kindly enter your name.',
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                return false;
            } else {
                $('#InputName').removeClass('is-invalid');
            }
            if (!validateEmail(InputEmail1)) {
                $('#InputEmail1').addClass('is-invalid');
                Lobibox.notify('default', {
                    title: 'Required',
                    msg: 'Kindly enter your valid email address.',
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                return false;
            } else {
                $('#InputEmail1').removeClass('is-invalid');
            }
            if (mobile_number == '' || mobile_number.length < 8 || isNaN(mobile_number)) {
                $('#mobile-number').addClass('is-invalid');
                Lobibox.notify('default', {
                    title: 'Required',
                    msg: 'Kindly enter valid mobile number.',
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                return false;
            } else {
                $('#mobile-number').removeClass('is-invalid');
            }
            if (InputAddress == '' || InputAddress.length < 3) {
                $('#InputAddress').addClass('is-invalid');
                Lobibox.notify('default', {
                    title: 'Required',
                    msg: 'Kindly enter location.',
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                return false;
            } else {
                $('#InputAddress').removeClass('is-invalid');
            }
            if (Regpassword == '' || Regpassword.length < 5) {
                $('#loginpassword').addClass('is-invalid');
                Lobibox.notify('default', {
                    title: 'Required',
                    msg: 'Kindly enter a stronger password.',
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                return false;
            } else {
                $('#loginpassword').removeClass('is-invalid');
            }
            Lobibox.notify.closeAll()
            return true;
        }
        if (stepnumber == 1) {
            var education = $('#educationqualification').val();
            var competencies = $('#competencies').val();
            var bio = $('#client-bio-field').val();
            if (education == '') {
                $('#educationqualification').addClass('is-invalid');
                Lobibox.notify('default', {
                    title: 'Required',
                    msg: 'Kindly select your academic qualification level.',
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                return false;
            } else {
                $('#educationqualification').removeClass('is-invalid');
            }
            if (competencies == '') {
                $('#competencies').addClass('is-invalid');
                Lobibox.notify('default', {
                    title: 'Required',
                    msg: 'Kindly select your competences.',
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                return false;
            } else {
                $('#competencies').removeClass('is-invalid');
            }
            if (bio == '') {
                $('#client-bio-field').addClass('is-invalid');
                Lobibox.notify('default', {
                    title: 'Required',
                    msg: 'Kindly describe yourself.',
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                return false;
            } else {
                $('#client-bio-field').removeClass('is-invalid');
            }
            return true;
        }
        return true;
    }
});
var input = document.querySelector('input[name=competencies]'),
    tagify = new Tagify(input);

(Dropzone.options.myAwesomeDropzone = {
    paramName: "trainerfiles",
    maxFilesize: 8,
    parallelUploads: 8,
    maxFiles: 5,
    autoProcessQueue: !1,
    acceptedFiles: "image/*,application/pdf,application/zip",
    addRemoveLinks: true,
    dictFileTooBig: "File is to big ({{filesize}}mb). Max allowed file size is {{maxFilesize}}mb",
    dictInvalidFileType: "Invalid File Type",
    dictCancelUpload: "Cancel",
    dictRemoveFile: "Remove this",
    dictMaxFilesExceeded: "Only {{maxFiles}} files are allowed",
    dictDefaultMessage: "Drop files here to upload. You can also just click here.",
    url: "/register-trainer",
    uploadMultiple:true,
    autoDiscover: false,
    accept: function (e, a) {
        "uda.jpg" == e.name ? a("Nah, you just didn't.") : a();
    },
    init: function () {
        var e = this;
        $("#trainerRegister").click(function (a) {
                if ((a.preventDefault(), a.stopPropagation(), e.getQueuedFiles().length > 0)) e.processQueue();
                else submitMyFormWithData();
                e.processQueue();
            }),
            this.on("sending", function (e, a, s) {
                $(".removeRegMessages").html(""),
                    $(".removeRegMessages").css("visibility", "visible"),
                    $(".removeRegMessages").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');

                var t = $("#trainer-reg-form").serializeArray();
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
                    $(".removeRegMessages").css("visibility", "hidden"),
                    "ok" == a.status ?
                    $(".removeRegMessages").html(
                        '<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' +
                        a.messages +
                        "</div>"
                    ) :
                    $(".removeRegMessages").html(
                        '<div class="alert alert-danger alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' +
                        a.messages +
                        "</div>"
                    ),
                    $(".removeRegMessages").css("visibility", "visible");
                    Dropzone.forElement('#my-awesome-dropzone').removeAllFiles(true)
            });
            this.on("error", function (e, a) {
                console.log("Response from server"),
                    console.log(a),
                    Dropzone.forElement('#my-awesome-dropzone').removeAllFiles(true)
            });
    },
});

function submitMyFormWithData() {

    var tnc_active = $('#tnc').prop("checked") ? 1 : 0 ;

    if (tnc_active != 1) {
        $('#tnc').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly check the terms and conditions.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        return false;
    } else {
        $('#tnc').removeClass('is-invalid');
    }

    var e = $("#trainer-reg-form").serializeArray();
    console.log(e),
        $.ajax({
            type: "post",
            url: "/register-trainer",
            data: e,
            dataType: "json",
            beforeSend: function () {
                $(".removeRegMessages").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>'),
                    $(".removeRegMessages").css("visibility", "visible");
            },
            success: function (e) {
                "ok" == e.status ?
                    ($(".ajaxRegloader").html(""),
                        $(".ajaxRegloader").css("visibility", "hidden"),
                        $(".removeRegMessages").html(
                            '<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' +
                            e.messages +
                            "</div>"
                        )) :
                    ($(".ajaxRegloader").html(""),
                        $(".ajaxRegloader").css("visibility", "hidden"),
                        $(".removeRegMessages").html(
                            '<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' +
                            e.messages +
                            "</div>"
                        ));
            },
            complete: function () {
                $(".ajaxRegloader").html(""), $(".ajaxRegloader").css("visibility", "hidden"), $(".removeRegMessages").css("visibility", "visible");
            },
        });
}

function validateEmail(e) {
    return /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(String(e).toLowerCase())
}