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


$('#editor-2').wysiwyg({
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

$('#editor-3').wysiwyg({
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

$('#editor-4').wysiwyg({
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

$('#editor-5').wysiwyg({
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


get_opportunity_details();

function get_opportunity_details() {
    var b = {
        'record_id': record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/admin-vw-byid-opportunities",
        data: b,
        dataType: "json",
        success: function(e) {
            if (e.status == 'ok') {
                var s = '<option value="">Select service</option>';
                console.log(e);
                let var_data = e.data;

                $('#title').val(var_data.title);
                $('#location').val(var_data.location);
                $('#contract-legth').val(var_data.contract_length);
                $('#status-disp').html(var_data.unit_ui_display);

                $('#editor').prevAll(".editor-content").html(var_data.over_view);
                $('#editor-2').prevAll(".editor-content").html(var_data.responsibilities);
                $('#editor-3').prevAll(".editor-content").html(var_data.desirability);
                $('#editor-4').prevAll(".editor-content").html(var_data.qualifications);
                $('#editor-5').prevAll(".editor-content").html(var_data.commitment);
                //$('#editor').val(var_data.over_view);
                //$('#editor-2').val(var_data.responsibilities);
                //$('#editor-3').val(var_data.desirability);
                //$('#editor-4').val(var_data.qualifications);
                // $('#editor-5').val(var_data.commitment);
                // $('#editor-5').val(var_data.commitment);
            }
        },
        complete: function() {}
    });
}

$("#new-opportunity-form").submit(function(c) {
    var title = $("#title").val();
    var location = $("#location").val();
    var contract_legth = $("#contract-legth").val();
    let overview = $("#editor").val();
    let responsibility = $("#editor-2").val();
    let desirebility = $("#editor-3").val();
    let qualifications = $("#editor-4").val();
    let commitment = $("#editor-5").val();
    let status_select = $("#status-select").select2('val');


    var b = {
        "record_id": record_id,
        "title": title,
        "location": location,
        "contract_legth": contract_legth,
        "overview": overview,
        "responsibility": responsibility,
        "desirebility": desirebility,
        "qualifications": qualifications,
        "commitment": commitment,
        'token': localStorage.token,
        "status_select": status_select
    };
    console.log(b);
    if (title == '') {
        $('#title').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter opportunity title.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#title').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#title').removeClass('is-invalid');
    }

    if (location == '') {
        $('#location').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter opportunity location.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#location').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#location').removeClass('is-invalid');
    }

    if (contract_legth == '') {
        $('#contract-legth').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter opportunity contract legth.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#contract-legth').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#contract-legth').removeClass('is-invalid');
    }

    if (overview == '') {
        $('#editor-1').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter opportunity overview.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#editor-1').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#editor-1').removeClass('is-invalid');
    }

    if (responsibility == '') {
        $('#editor-2').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter opportunity responsibilities.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#editor-2').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#editor-2').removeClass('is-invalid');
    }

    if (desirebility == '') {
        $('#editor-3').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter opportunity desirability.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#editor-3').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#editor-3').removeClass('is-invalid');
    }

    if (commitment == '') {
        $('#editor-5').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter our commitment.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#editor-5').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#editor-5').removeClass('is-invalid');
    }

    if (qualifications == '') {
        $('#editor-5').addClass('is-invalid');
        Lobibox.notify('default', {
            title: 'Required',
            msg: 'Kindly enter opportunity qualifications.',
            pauseDelayOnHover: true,
            continueDelayOnInactiveTab: false,
            position: 'center top',
            showClass: 'fadeInDown',
            hideClass: 'fadeOutDown',
            width: 600,
        });
        $('html, body').animate({
            scrollTop: $('#editor-5').focus().offset().bottom - 25
        }, 2000);
        return false;
    } else {
        $('#editor-5').removeClass('is-invalid');
    }


    //console.log('SERVER VAL:', b);

    $.ajax({
        type: "post",
        url: "/edit-new-opportunities",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxBookloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxBookloader").css("visibility", "visible");
            $(".removeBookMessages").html("");
            $(".removeBookMessages").css("visibility", "hidden");
            $(".clientlogin-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            if (e.status == 'ok') {
                $(".ajaxBookloader").html("");
                $(".ajaxBookloader").css("visibility", "hidden");
                $(".removeBookMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                $('#editor-5').addClass('is-invalid');
                Lobibox.notify('default', {
                    title: 'Required',
                    msg: e.messages,
                    pauseDelayOnHover: true,
                    continueDelayOnInactiveTab: false,
                    position: 'center top',
                    showClass: 'fadeInDown',
                    hideClass: 'fadeOutDown',
                    width: 600,
                });
                get_opportunity_details();
                $('html, body').animate({
                    scrollTop: $('#notif-span').focus().offset().bottom - 25
                }, 2000);
            } else {
                $(".ajaxBookloader").html("");
                $(".ajaxBookloader").css("visibility", "hidden");
                $(".removeBookMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxBookloader").html("");
            $(".ajaxBookloader").css("visibility", "hidden");
            $(".removeBookMessages").css("visibility", "visible");
            $(".clientlogin-btn").prop('disabled', false).html('Log In');
            $(".clientlogin-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})