let searchedParams = new URLSearchParams(window.location.search);

let record_id = '';

if (searchParams.has('ref')) {
    record_id = searchParams.get('ref');
} else {
    console.log('trainer id not set');
    window.location.href = "/current-activities";
}
try {
    record_id = parseInt(record_id);
    console.log(record_id);

    if (isNaN(record_id) || record_id < 1) {
        console.log('not a good number');
        window.location.href = "/current-activities";
    }

} catch (Exception) {
    window.location.href = "/current-activities";
}

get_services(record_id);

function get_services(record_id) {
    var b = {
        'record_id': record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-event-by-id-fe-api",
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
                //         '<a href="/current-activities-view?ref=' + val.record_id + '"><img src="' + val.thumbnail + '" alt="" /></a>' +
                //         '<div class="icon-box">' +
                //         '<span class="icon flaticon-tools-and-utensils-1"></span>' +
                //         '</div>' +
                //         '</div>' +
                //         '<div class="lower-content">' +
                //         '<div class="title">' + service_pill + '</div>' +
                //         '<h4><a href="/current-activities-view?ref=' + val.record_id + '">' + session_title + '</a></h4>' +
                //         '<div class="text">'+tag_line+'</div>' +
                //         '<a class="read-more" href="/current-activities-view?ref=' + val.record_id + '">More Details</a>' +
                //         '</div>' +
                //         '</div>' +
                //         '</div>';
                // });
                //$('#results-div').html(item_divs);
                let data = e.data['data'];
                var thumbnail=  data[0].thumbnail;
                console.log(e);
                $('#thumbnail').prop('src',thumbnail);
                $('#event-title').html(data[0].session_title);
                $('#event-tagline').html(data[0].tag_line);
                //$('#event-tagline')
                //.log(data);
                //let index_zero = data[0];
                var age_brackets_string = '| ';
                var training_types_string = '';
                $.each(data, function(key, val) {
                        console.log(key);
                        console.log(val);
                        var age_brackets = val.age_brackets;
                        var type_of_trainings = val.type_of_trainings;
                        var requirement_pdfs = val.requirement_pdfs;
                        console.log('requirement_pdfs',requirement_pdfs);
                        console.log('requirement_pdfs length',requirement_pdfs.length)

                        $.each(age_brackets, function(key, val) {
                            //console.log(val);
                            age_brackets_string += val.description + ' | ';

                            // var i;
                            // for (i in val) {
                            //     if (val.hasOwnProperty(i)) {
                            //         services += val.description + ' | ';
                            //     }
                            // }
                        });

                        if(requirement_pdfs.length>0){
                            $.each(requirement_pdfs, function(key, val) {
                                console.log(val);
                               
                                var i=0;
                                let file_path='#';
                                for (i in val) {
                                    if (val.hasOwnProperty(i)) {
                                        console.log(val.file_path);
                                        //let elem_data = val[i];
                                        file_path = val.file_path;
                                        $('#download-file-button').attr('href',file_path);
                                    }
                                    i++;
                                }
                                
                            });
                            $('#download-files').removeClass('hide-me');
                        }

                        $.each(type_of_trainings, function(key, val) {
                            //console.log(val);
                            training_types_string += '<ul class="service-list">' +
                                '<li>' +
                                '<a href="#">' + val.description + '</a>' +
                                '</li>' +
                                '</ul>';
                        })
                        var val = data[0];
                        let event_dates = val.start_date + ' => ' + val.end_date;
                        console.log('age_brackets_string',val.start_date);
                        $('#desc-body').html(val.description);
                        $('#max-attendee-span').html(val.max_attendee);
                        $('#chaperone-allowed-span').html(val.chaperone_allowed);
                        $('#age-bracket-span').html(age_brackets_string);
                        $('#dates-span').html(event_dates+' at '+val.location);
                        $('#sdbar-div').html(training_types_string);
                        let renerw_link = 'client-vw-book-event-more-details?record-id=' +
                            val.record_id;
                        $('.book-link').attr('href', renerw_link);
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