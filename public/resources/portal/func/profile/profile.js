get_service_count();
function get_service_count() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-profile-details",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $('#profile-bio').html('<i class="bx bx-loader bx-spin"></i> Loading...');
            $('.location-span').html('<i class="bx bx-loader bx-spin"></i> Loading...');
            $('.client_age').html('<i class="bx bx-loader bx-spin"></i> Loading...');
        },
        success: function (e) {
            
            if (e.status == 'ok') {
                var client_data = e.data;
                $('.location-span').html(client_data.location);
                $('.client_age').html(client_data.client_age);

                $('#link-twitter').attr("href",client_data.social_link_twitter);
                $('#link-fb').attr("href",client_data.social_link_facebook);
                $('#link-insta').attr("href",client_data.social_link_insta);

                $('#profile-bio').html(client_data.bio);
                $("#client-bio-field").val(client_data.bio);

                $('#client-full-name').val(client_data.user_name);
                $('#client-phone').val(client_data.mobile);
                $('#client-location').val(client_data.location);
                $('#client-nationality').val(client_data.nationality);

                
                $('#client-twitter').val(client_data.social_link_twitter);
                $('#client-fb').val(client_data.social_link_facebook);
                $('#client-insta').val(client_data.social_link_insta);

                $('#dob').val(client_data.dob_vw);
                $('#member-since').html(client_data.member_since);

            } else {
                //$('#service-count').html(e.service_count);
            }
        },
        complete: function () {
            //pass
        }
    });

}

$("#bio-form").submit(function (c) {
    var client_full_name = $("#client-full-name").val();
    var client_phone = $("#client-phone").val();
    var client_location = $("#client-location").val();
    var client_nationality = $("#client-nationality").val();
    var client_dob = $("#dob").val();
    var client_twitter = $("#client-twitter").val();
    var client_insta = $("#client-insta").val();
    var client_fb = $("#client-fb").val();
    var client_bio_field = $("#client-bio-field").val();

    var b = {
        "client_full_name": client_full_name,
        "client_phone": client_phone,
        "client_location": client_location,
        "client_nationality": client_nationality,
        "client_dob": client_dob,
        "client_twitter": client_twitter,
        "client_insta": client_insta,
        "client_fb": client_fb,
        "client_bio_field":client_bio_field,
        'token': localStorage.token,
    };

    $.ajax({
        type: "post",
        url: "/update-bio",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $(".ajaxbioloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxbioloader").css("visibility", "visible");
            $(".removebioMessages").html("");
            $(".removebioMessages").css("visibility", "hidden");
            $(".clientbio-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function (e) {
            if (e.status == 'ok') {
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                
                localStorage.username = client_full_name;
                //$(".portal-user-name", this).html(client_full_name);
                $('.portal-user-name').each(function() {
                    $(this).html(client_full_name);
                });
                
                get_service_count();
            } else {
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
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