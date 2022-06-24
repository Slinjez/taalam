console.log('Trainer profile');
let searchParams = new URLSearchParams(window.location.search);

let record_id = '';

if (searchParams.has('rec-id')) {
    record_id = searchParams.get('rec-id');
} else {
    console.log('trainer id not set');
    window.location.href = "/admin-vw-new-trainers";
}
try {
    record_id = parseInt(record_id);
    console.log(record_id);

    if (isNaN(record_id) || record_id < 1) {
        console.log('not a good number');
        window.location.href = "/admin-vw-new-trainers";
    }

} catch (Exception) {
    window.location.href = "/admin-vw-new-trainers";
}

get_service_count(record_id);
get_files(record_id);
get_trainer_profile_data(record_id);
get_trainer_comitencies(record_id);

function get_trainer_profile_data(record_id) {
    var b = {
        'record_id': record_id,
    };
    $.ajax({
        type: "post",
        url: "/get-trainer-profile-details-more",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $('#profile-bio').html('<i class="bx bx-loader bx-spin"></i> Loading...');
            $('.location-span').html('<i class="bx bx-loader bx-spin"></i> Loading...');
            $('.client_age').html('<i class="bx bx-loader bx-spin"></i> Loading...');
        },
        success: function(e) {

            if (e.status == 'ok') {
                var client_data = e.data;
                $('.location').html(client_data.location);
                $('.bio').html(client_data.bio);
                $('.education_qualification').html(client_data.education_qualification);

            } else {
                //$('#service-count').html(e.service_count);
            }
        },
        complete: function() {
            //pass
        }
    });

}

function get_trainer_comitencies(record_id) {
    var b = {
        'record_id': record_id,
    };
    $.ajax({
        type: "post",
        url: "/get-trainer-compitency-details-more",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $('#profile-bio').html('<i class="bx bx-loader bx-spin"></i> Loading...');
            $('.location-span').html('<i class="bx bx-loader bx-spin"></i> Loading...');
            $('.client_age').html('<i class="bx bx-loader bx-spin"></i> Loading...');
        },
        success: function(e) {

            if (e.status == 'ok') {
                var client_data = e.data;
                let item_divs='';
                $.each(e.data, function(key,val) {
                    item_divs += '<i class="bx bxs-arrow-to-right"></i>'+val.competency+'<br>';
                    //console.log(val);
                    //console.log("record_id: " +val.record_id+" & service_name: "+val.service_name+"<br/>");
                });
                $('.competencies').html(item_divs);

            } else {
                //$('#service-count').html(e.service_count);
            }
        },
        complete: function() {
            //pass
        }
    });

}
function get_service_count(record_id) {
    var b = {
        'record_id': record_id,
    };
    $.ajax({
        type: "post",
        url: "/get-trainer-profile-details",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $('#profile-bio').html('<i class="bx bx-loader bx-spin"></i> Loading...');
            $('.location-span').html('<i class="bx bx-loader bx-spin"></i> Loading...');
            $('.client_age').html('<i class="bx bx-loader bx-spin"></i> Loading...');
        },
        success: function(e) {

            if (e.status == 'ok') {
                var client_data = e.data;
                $('.location-span').html(client_data.location);
                $('.client_age').html(client_data.client_age);
                $('.email_address').html(client_data.email_address);
                $('.phone').html(client_data.phone);

                $('#link-twitter').attr("href", client_data.social_link_twitter);
                $('#link-fb').attr("href", client_data.social_link_facebook);
                $('#link-insta').attr("href", client_data.social_link_insta);

                $('.member_since').html(client_data.member_since);
                $("#client-bio-field").val(client_data.bio);

                $('#client-full-name').val(client_data.user_name);
                $('.portal-user-name_over').html(client_data.user_name);
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
        complete: function() {
            //pass
        }
    });

}

function get_files(record_id) {
    var b = {
        'record_id': record_id,
    };
    $.ajax({
        type: "post",
        url: "/get-trainer-profile-files",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $('#profile-bio').html('<i class="bx bx-loader bx-spin"></i> Loading...');
            $('.location-span').html('<i class="bx bx-loader bx-spin"></i> Loading...');
            $('.client_age').html('<i class="bx bx-loader bx-spin"></i> Loading...');
        },
        success: function(e) {

            if (e.status == 'ok') {
                var obj = (e.data);
                console.log(obj);
                let link_btns = '';
                for (let key in obj) {
                    if (obj.hasOwnProperty(key)) {
                        for (let key in obj) {
                            console.log(obj[0])
                            link_btns += '<a  href="' +
                                obj[0] + '" type="submit" target="_blank" class="btn btn-light btn-block">Download file</a>';

                            // link_btns += '<div class="mt-3 btn-group w-100"> ' +
                            //     ' < a href="' + obj[0] + '" type = "submit" class = "btn btn-light btn-block" > Download file< /a>' +
                            //     ' <a type = "submit" class = "btn btn-light -ico" > < i class = "lni lni-arrow-right" > < /i> < /a > < /                                div > ';
                        }

                    }
                }
                $('#files-view').html(link_btns);
                // file_data.forEach(function(val, index) {
                //     console.log(index);
                //     console.log(val);
                // });

            } else {
                //$('#service-count').html(e.service_count);
            }
        },
        complete: function() {
            //pass
        }
    });
}

$("#bio-form").submit(function(c) {
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
        "client_bio_field": client_bio_field,
        'token': localStorage.token,
    };

    $.ajax({
        type: "post",
        url: "/update-bio",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxbioloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxbioloader").css("visibility", "visible");
            $(".removebioMessages").html("");
            $(".removebioMessages").css("visibility", "hidden");
            $(".clientbio-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
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
        complete: function() {
            $(".ajaxbioloader").html("");
            $(".ajaxbioloader").css("visibility", "hidden");
            $(".removebioMessages").css("visibility", "visible");
            $(".clientbio-btn").prop('disabled', false).html('Update Profile');
            $(".clientbio-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})

$("#activate-acc").click(function(c) {
    console.log('activate-acc clicked');
    var remarks = $("#remarks").val();

    var b = {
        "remarks": remarks,
        "record_id": record_id,
        'token': localStorage.token,
    };

    $.ajax({
        type: "post",
        url: "/confirm-trainer",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxbioloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxbioloader").css("visibility", "visible");
            $(".removebioMessages").html("");
            $(".removebioMessages").css("visibility", "hidden");
            $(".clientbio-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            if (e.status == 'ok') {
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");


            } else {
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxbioloader").html("");
            $(".ajaxbioloader").css("visibility", "hidden");
            $(".removebioMessages").css("visibility", "visible");
            $(".clientbio-btn").prop('disabled', false).html('Update Profile');
            $(".clientbio-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})

$("#reject-acc").click(function(c) {
    console.log('deactivate-acc clicked');
    var remarks = $("#remarks").val();

    var b = {
        "remarks": remarks,
        "record_id": record_id,
        'token': localStorage.token,
    };

    $.ajax({
        type: "post",
        url: "/de-confirm-trainer",
        data: b,
        dataType: "json",
        beforeSend: function() {
            $(".ajaxbioloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxbioloader").css("visibility", "visible");
            $(".removebioMessages").html("");
            $(".removebioMessages").css("visibility", "hidden");
            $(".clientbio-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function(e) {
            if (e.status == 'ok') {
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");


            } else {
                $(".ajaxbioloader").html("");
                $(".ajaxbioloader").css("visibility", "hidden");
                $(".removebioMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function() {
            $(".ajaxbioloader").html("");
            $(".ajaxbioloader").css("visibility", "hidden");
            $(".removebioMessages").css("visibility", "visible");
            $(".clientbio-btn").prop('disabled', false).html('Update Profile');
            $(".clientbio-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})