let searchParams = new URLSearchParams(window.location.search);

let record_id = '';

if (searchParams.has('ref')) {
    record_id = searchParams.get('ref');
} else {
    console.log('trainer id not set');
    window.location.href = "/blog";
}
try {
    record_id = parseInt(record_id);
    console.log(record_id);

    if (isNaN(record_id) || record_id < 1) {
        console.log('not a good number');
        window.location.href = "/blog";
    }

} catch (Exception) {
    window.location.href = "/blog";
}

get_services(record_id);

function get_services(record_id) {
    var b = {
        'record_id': record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-blog-by-id-fe-api",
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
                // $.each(data_export.data, function (key, val) {
                //     var services = '| ';
                //     var trainer_data = val.type_of_trainings;
                //     $.each(trainer_data, function (key, val) {
                //         //console.log(val);
                //         services += val.description + ' | ';

                //         // var i;
                //         // for (i in val) {
                //         //     if (val.hasOwnProperty(i)) {
                //         //         services+=val.description+' | ';
                //         //     }
                //         // }
                //     })
                //     var tag_line = val.tag_line;
                //     services = truncate(services, 25);
                //     tag_line = truncate(tag_line, 80);
                //     var session_title = truncate(val.session_title, 40);
                //     var service_pill = 'Training activities: <span class="badge badge-light">' + services + '</span>';
                //     item_divs += '<div class="classess-block col-lg-4 col-md-6 col-sm-12">' +
                //         '<div class="inner-box elem-paro">' +
                //         '<div class="image">' +
                //         '<a href="/blog-view?ref=' + val.record_id + '"><img src="' + val.thumbnail + '" alt="" /></a>' +
                //         '<div class="icon-box">' +
                //         '<span class="icon flaticon-tools-and-utensils-1"></span>' +
                //         '</div>' +
                //         '</div>' +
                //         '<div class="lower-content">' +
                //         '<div class="title">' + service_pill + '</div>' +
                //         '<h4><a href="/blog-view?ref=' + val.record_id + '">' + session_title + '</a></h4>' +
                //         '<div class="text">'+tag_line+'</div>' +
                //         '<a class="read-more" href="/blog-view?ref=' + val.record_id + '">More Details</a>' +
                //         '</div>' +
                //         '</div>' +
                //         '</div>';
                // });
                //$('#results-div').html(item_divs);
                let data = e.data['data'];
                //.log(data);
                //let index_zero = data[0];
                var age_brackets_string = '| ';
                var training_types_string = '';
                $.each(data, function(key, val) {
                        console.log(key);
                        console.log(val);

                        let blogger_name = val.blogger_name;
                        let blogger_id = val.blogger_id;
                        let title = val.title;
                        let blog = val.blog;
                        let start_date = val.start_date;
                        let thumbnail = val.thumbnail;
                        let teaser = val.teaser;
                        
                        $('#blogger_name').html(blogger_name);
                        $('.title').html(title);
                        $('#blogger_name').html(blogger_name);
                        $('#start_date').html(start_date);
                        $('#teaser').html(teaser);

                        $('#desc-body').html(blog);
                        $('#blog-thumb').attr('src',thumbnail);

                        //$('#dates-span').html(event_dates);

                    })
                    // $('#desc-body').html(index_zero.description);
                    // $(".paginate").paginga({
                    //     // use default options
                    //     itemsPerPage: 6,
                    // });
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