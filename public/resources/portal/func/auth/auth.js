//login-form

let searchParams = new URLSearchParams(window.location.search);

let return_url = '/client-auth';

if (searchParams.has('return-url')) {
    return_url = searchParams.get('return-url');
}

$('.login-link').attr('href', return_url);

$('.close').click(function() {
    $(".alert-dismissible").css("display", "none");
})

$('.alerts-div').on('click', 'close', function() {
    $(".alert-dismissible").css("display", "none");
    //do something
});

$("#button-addon2").on('click', function(event) {
    //console.log('Icon clicked');
    event.preventDefault();
    if ($('#loginpassword ').attr("type") == "text") {
        $('#loginpassword ').attr('type', 'password');
        $('#button-addon2 ').html("<i class='bx bx-hide'></i>");
        //$('#button-addon2 ').html( "<i class='bx bx-show-alt'></i>" );
    } else if ($('#loginpassword ').attr("type") == "password") {
        $('#loginpassword ').attr('type', 'text');
        //$('#button-addon2 ').html("<i class='bx bx-hide'></i>" );
        $('#button-addon2 ').html("<i class='bx bx-show-alt'></i>");
    }
});

$("#login-form").submit(function(c) {
    var email = $("#InputEmail1").val();
    var loginpassword = $("#loginpassword").val();

    var b = {
        "email": email,
        "password": loginpassword,
    };
    $.ajax({
        type: "post",
        url: "/loginAction",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxLoginloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxLoginloader").css("visibility", "visible");
            $(".removeLoginMessages").html("");
            $(".removeLoginMessages").css("visibility", "hidden");
            $(".clientlogin-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            if (e.status == 'ok') {
                $(".ajaxLoginloader").html("");
                $(".ajaxLoginloader").css("visibility", "hidden");
                $(".removeLoginMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                localStorage.token = e['vars'].token;
                localStorage.username = e['vars'].username;
                localStorage.profpic = e['vars'].profpic;
                var path = e['vars'].path;
                if (localStorage.profpic != null) {
                    $('.putMyPicHere').attr("src", localStorage.profpic);
                }

                window.location.href = path;
                //location.reload();
            } else {
                $(".ajaxLoginloader").html("");
                $(".ajaxLoginloader").css("visibility", "hidden");
                $(".removeLoginMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxLoginloader").html("");
            $(".ajaxLoginloader").css("visibility", "hidden");
            $(".removeLoginMessages").css("visibility", "visible");
            $(".clientlogin-btn").prop('disabled', false).html('Log In');
            $(".clientlogin-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})

$("#register-form").submit(function(c) {
    $('.show-otp-popup').removeClass('hide-me');
    var username = $("#InputName").val();
    var email = $("#InputEmail1").val();

    var mobile_number = $("#mobile-number").val();
    var loginpassword = $("#loginpassword").val();

    var b = {
        "username": username,
        "email": email,
        "mobile_number": mobile_number,
        "loginpassword": loginpassword,
    };
    $.ajax({
        type: "post",
        url: "/registerAction",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxRegloader").html('<p class="modal-title"><span class="bx bx-loader bx-spin"></span> Please wait...</p>');
            $(".ajaxRegloader").css("visibility", "visible");
            $(".removeRegMessages").html("");
            $(".removeRegMessages").css("visibility", "hidden");
            $("confirm-otp").addClass('disabled');
        },
        success: function(e) {
            if (e.status == 'ok') {
                $('.otp-responses').html(e.messages);
                $(".ajaxRegloader").html("");
                $(".ajaxRegloader").css("visibility", "hidden");
                $(".removeRegMessages").css("visibility", "visible");
                $(".removeRegMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                //$('#confirmotp').modal('show'); 
                $(".removeLoginMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");

                $('#register-form').addClass('hide-me');
                $('#reset-ps2-form').removeClass('hide-me');
            } else {
                $(".ajaxRegloader").html("");
                $(".ajaxRegloader").css("visibility", "hidden");
                $(".removeRegMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")

                $(".removeLoginMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxRegloader").html("");
            $(".ajaxRegloader").css("visibility", "hidden");
            $(".removeRegMessages").addClass("hide-me");
            $(".removeRegMessages").css("visibility", "hidden");
            $(".removeLoginMessages").css("visibility", "visible");
            $("confirm-otp").removeClass('disabled');
        }
    });
    c.preventDefault()
})

$("#reset_email_btn").click(function(c) {
    $('.show-otp-popup').removeClass('hide-me');
    var reset_email_btn = $("#reset_emailadd").val();

    var ps1 = $("#ps1_reset").val();
    var ps2 = $("#ps2_reset").val();
    $('#assocemail').val(reset_email_btn);
    var b = {
        "reset_email_btn": reset_email_btn,
        "ps1": ps1,
        "ps2": ps2,
    };
    $.ajax({
        type: "post",
        url: "/resetpsaction",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxRegloader").html('<p class="modal-title"><span class="bx bx-loader bx-spin"></span> Please wait...</p>');
            $(".ajaxRegloader").css("visibility", "visible");
            $(".removeRegMessages").html("");
            $(".removeRegMessages").css("visibility", "hidden");
        },
        success: function(e) {
            if (e.status == 'ok') {
                $('.otp-responses').html(e.messages);
                $(".ajaxRegloader").html("");
                $(".ajaxRegloader").css("visibility", "hidden");
                $(".removeRegMessages").css("visibility", "visible");
                $(".removeRegMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                $('#confirmotp').modal('show');
            } else {
                $(".ajaxRegloader").html("");
                $(".ajaxRegloader").css("visibility", "hidden");
                $(".removeRegMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxRegloader").html("");
            $(".ajaxRegloader").css("visibility", "hidden");
            $(".removeRegMessages").css("visibility", "visible");
        }
    });
    c.preventDefault()
})

$("#confirm-otp").click(function(c) {
    var ver_otp = $("#ver_otp").val();
    var assocemail = $("#assocemail").val();

    var b = {
        "ver_otp": ver_otp,
        "assocemail": assocemail,
    };

    $.ajax({
        type: "post",
        url: "/activate-otp",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxRegloader").html('<p class="modal-title"><span class="bx bx-loader bx-spin"></span> Please wait...</p>');
            $(".ajaxRegloader").css("visibility", "visible");
            $(".removeRegMessages").html("");
            $(".removeRegMessages").css("visibility", "hidden");
        },
        success: function(e) {
            if (e.status == 'ok') {
                $(".ajaxRegloader").html("");
                $(".ajaxRegloader").css("visibility", "hidden");
                $(".removeRegMessages").css("visibility", "visible");
                $(".removeRegMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                $('#confirmotp').modal('hide');

            } else {
                $(".ajaxRegloader").html("");
                $(".ajaxRegloader").css("visibility", "hidden");
                $(".removeRegMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxRegloader").html("");
            $(".ajaxRegloader").css("visibility", "hidden");
            $(".removeRegMessages").css("visibility", "visible");
        }
    });
    c.preventDefault()
})

$("#reset-ps1-form").submit(function(c) {
    var email = $("#InputEmail1").val();
    var loginpassword = $("#loginpassword").val();

    var b = {
        "email": email,
        "password": loginpassword,
    };
    console.log(b);
    $.ajax({
        type: "post",
        url: "/resetpsaction-client",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxLoginloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxLoginloader").css("visibility", "visible");
            $(".removeLoginMessages").html("");
            $(".removeLoginMessages").css("visibility", "hidden");
            $(".clientResetPs1-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            if (e.status == 'ok') {
                $(".ajaxLoginloader").html("");
                $(".ajaxLoginloader").css("visibility", "hidden");
                $(".removeLoginMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                $('#reset-ps1-form').addClass('hide-me');
                $('#reset-ps2-form').removeClass('hide-me');
                //window.location.href = path;
                //location.reload();
            } else {
                $(".ajaxLoginloader").html("");
                $(".ajaxLoginloader").css("visibility", "hidden");
                $(".removeLoginMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxLoginloader").html("");
            $(".ajaxLoginloader").css("visibility", "hidden");
            $(".removeLoginMessages").css("visibility", "visible");
            $(".clientResetPs1-btn").prop('disabled', false).html('Log In');
            $(".clientResetPs1-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})

$("#reset-ps2-form").submit(function(c) {
    var email = $("#InputEmail1").val();
    var otp = $("#otp").val();

    var b = {
        "assocemail": email,
        "ver_otp": otp,
    };
    console.log(b);
    $.ajax({
        type: "post",
        url: "/activate-otp-client",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxLoginloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxLoginloader").css("visibility", "visible");
            $(".removeLoginMessages").html("");
            $(".removeLoginMessages").css("visibility", "hidden");
            $(".clientResetPs2-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            if (e.status == 'ok') {
                $(".ajaxLoginloader").html("");
                $(".ajaxLoginloader").css("visibility", "hidden");
                $(".removeLoginMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                $('#reset-ps1-form').addClass('hide-me');
                $('#reset-ps2-form').removeClass('hide-me');
                //window.location.href = path;
                //location.reload();
            } else {
                $(".ajaxLoginloader").html("");
                $(".ajaxLoginloader").css("visibility", "hidden");
                $(".removeLoginMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxLoginloader").html("");
            $(".ajaxLoginloader").css("visibility", "hidden");
            $(".removeLoginMessages").css("visibility", "visible");
            $(".clientResetPs2-btn").prop('disabled', false).html('Re-verify');
            $(".clientResetPs2-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})