$("#contact-form").submit(function (c) {
    var contact_name = $("#contact-name").val();
    var contact_email = $("#contact-email").val();
    var contact_phone = $("#contact-phone").val();
    var contact_subject = $("#contact-subject").val();
    var contact_message = $("#contact-message").val();

    var b = {
        "contact_name": contact_name,
        "contact_email": contact_email,
        "contact_phone": contact_phone,
        "contact_subject": contact_subject,
        "contact_message": contact_message,
    };
    $.ajax({
        type: "post",
        url: "/client-contact-us",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $(".ajaxLoginloader").html('<p class="modal-title"><i class="fa fa-spinner fa-spin"></i> Please wait...</p>');
            $(".ajaxLoginloader").css("visibility", "visible");
            $(".removeLoginMessages").html("");
            $(".removeLoginMessages").css("visibility", "hidden");
            $(".clientlogin-btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        },
        success: function (e) {
            if (e.status == 'ok') {
                $(".ajaxLoginloader").html("");
                $(".ajaxLoginloader").css("visibility", "hidden");
                $(".removeLoginMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                
            } else {
                $(".ajaxLoginloader").html("");
                $(".ajaxLoginloader").css("visibility", "hidden");
                $(".removeLoginMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function () {
            $(".ajaxLoginloader").html("");
            $(".ajaxLoginloader").css("visibility", "hidden");
            $(".removeLoginMessages").css("visibility", "visible");
            $(".clientlogin-btn").prop('disabled', false).html('Message Sent');
            $(".clientlogin-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})