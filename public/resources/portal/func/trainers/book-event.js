/*!
 * paginga - jQuery Pagination Plugin v0.8.1
 * https://github.com/mrk-j/paginga
 *
 * Copyright 2017 Mark and other contributors
 * Released under the MIT license
 * https://github.com/mrk-j/paginga/blob/master/LICENSE
 */
let searchParams = new URLSearchParams(window.location.search);

let record_id = '';

if (searchParams.has('record-id')) {
  record_id = searchParams.get('record-id');
} else {
  console.log('trainer id not set');
  window.location.href = "/client-vw-book-trainer";
}
try{
    record_id = parseInt(record_id);
    console.log(record_id);

    if(isNaN(record_id) || record_id < 1 ){                
        console.log('not a good number');
        window.location.href = "/client-vw-book-trainer";
    }

}catch(Exception){
  window.location.href = "/client-vw-book-trainer";
}

$(document).ready(function() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0 so need to add 1 to make it 1!
    var yyyy = today.getFullYear();
    if(dd<10){
      dd='0'+dd
    } 
    if(mm<10){
      mm='0'+mm
    } 
    

    var oneYearFromNow = new Date();
    oneYearFromNow.setFullYear(oneYearFromNow.getFullYear() + 1);
    var ny_dd = oneYearFromNow.getDate();
    var ny_mm = oneYearFromNow.getMonth()+1; //January is 0 so need to add 1 to make it 1!
    var ny_yyyy = oneYearFromNow.getFullYear();
    if(ny_dd<10){
        ny_dd='0'+ny_dd
    } 
    if(ny_mm<10){
        ny_mm='0'+ny_mm
    } 


    today = yyyy+'-'+mm+'-'+dd+'T00:00:00';
    next_today = ny_yyyy+'-'+ny_mm+'-'+ny_dd+'T00:00:00';

    
    // console.log('today',today);
    // console.log('next_today',next_today);


    var x = document.getElementById("session-date").min = today;
    var y = document.getElementById("session-date").max = next_today;
    $("#session-date").attr("min", today); 
    $("#session-date").attr("max", next_today); 
    //"2006-05-05T16:15:23";
    //document.getElementById("demo").innerHTML = "The value of the min attribute was changed from '2000-10-06T22:22:55' to '2006-05-05T16:15:23'.";
    //$("#session-date").attr("min", today); 
    //document.getElementById("session-date").min = today;
});

get_service_list(record_id);
get_trainer_data(record_id);

function get_service_list(record_id) {
    var b = {
        'trainer':record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-trainer-services-morph",
        data: b,
        dataType: "json",
        
        success: function (e) {
            if (e.status == 'ok') {

                var s = '<option value="">Select service</option>';
                var ex='';
                
                $.each(e.data, function(key,val) {
                    s += '<option value="' + val.record_id + '">' + val.service_name + "</option>";
                });
                $('#service-select').append(s);
                $('#service-select').select2({ selectOnClose: !0 });                
            } 
        },
        complete: function () {

        }
    });
    
}

function get_trainer_data(record_id){
    var b = {
        'trainer-id':record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-trainer-by-id",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $('.loading-div').removeClass('hide-me');
            $('.results-div').addClass('hide-me');
            $(".ajaxDataloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxDataloader").css("visibility", "visible");
            $(".removeDataMessages").html("");
            $(".removeDataMessages").css("visibility", "hidden");
            $(".clientData-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function (e) {
            //console.log('get services done');
            //console.log(e.data);
            //var item_divs = '';
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
               
                var data_export = e.data;
                $.each(data_export.data, function(key,val) {
                    var services='| ';
                    var trainer_data = val.trainer_activities_array;
                    //console.log('trainer_data',trainer_data);
                    //var key_service = trainer_data.trainer_activities_array;
                    $.each(trainer_data, function(key,val) {
                        console.log('val',val);
                        //val[1].join(', ');
                        //console.log('val',val[0][1]);
                        //services+=val[0][1]+'|';

                        var i;
                        for (i in val) {
                            if (val.hasOwnProperty(i)) {
                                //count++;
                                services+=val[i][1]+' | ';
                            }
                        }
                    })

                    $('#display-trainer-name').html(val.user_name);
                    var service_pill = 'Specialization: <span class="badge badge-light">'+services+'</span>';
                    $('#display-trainer-services').html(service_pill);

                    $('#display-trainer-gender').html(val.pronoun);
                    $("#trainer-profile-img").attr("src",val.profile_picture);
                    $('.display-trainer-name').html(val.user_name);
                    // item_divs += '<div class="col-12 col-lg-3 col-xl-3">'
					// 		+'<div class="card">'
					// 			+'<img src="'+val.profile_picture+'" class="card-img-top" alt="...">'
					// 			+'<div class="card-body">'
					// 				+'<h5 class="card-title mb-0">'+val.user_name+'</h5>'
					// 				+'<p class="mb-0">'+services+'</p>'
                    //                 +'<hr>'                         
					// 				+'<a href="/client-vw-book-trainer-session?record-id='+val.record_id+'" class="btn btn-light">Book '+val.pronoun+' now</a>'
					// 			+'</div>'
					// 		+'</div>'
					// 	+'</div>';
                    //console.log(val);
                    //console.log("record_id: " +val.record_id+" & service_name: "+val.service_name+"<br/>");
                });

                //$('#results-div').html(item_divs);

                // $(".paginate").paginga({
                //     // use default options
                //     itemsPerPage: 6,
                // });
                
            } else {
                console.log('not a good number');
                window.location.href = "/client-vw-book-trainer";
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function () {
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

$("#book-trainer-form").submit(function (c) {
    let searchParams = new URLSearchParams(window.location.search);

    let record_id = '';

    if (searchParams.has('record-id')) {
        record_id = searchParams.get('record-id');
    }

    var service = $("#service-select").val();
    var session_date = $("#session-date").val();
    var training_activities = $("#training-activities").val();

    var b = {        
        'trainer-id':record_id,
        "service": service,
        "session_date": session_date,
        "training_activities":training_activities,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/book-session",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $(".ajaxBookloader").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> Please wait...</p>');
            $(".ajaxBookloader").css("visibility", "visible");
            $(".removeBookMessages").html("");
            $(".removeBookMessages").css("visibility", "hidden");
            $(".book-trainer-btn").prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function (e) {
            if (e.status == 'ok') {
                $(".ajaxBookloader").html("");
                $(".ajaxBookloader").css("visibility", "hidden");
                $(".removeBookMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                
            } else {
                $(".ajaxBookloader").html("");
                $(".ajaxBookloader").css("visibility", "hidden");
                $(".removeBookMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function () {
            $(".ajaxBookloader").html("");
            $(".ajaxBookloader").css("visibility", "hidden");
            $(".removeBookMessages").css("visibility", "visible");
            $(".book-trainer-btn").prop('disabled', false).html('Book Session');
            $(".book-trainer-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
    c.preventDefault()
})

// $(function() {
//     $(".paginate").paginga({
//         // use default options
//     });
  
//     $(".paginate-page-2").paginga({
//         page: 2
//     });

//     $(".paginate-no-scroll").paginga({
//         scrollToTop: false
//     });
// });