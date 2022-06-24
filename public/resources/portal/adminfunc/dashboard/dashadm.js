
get_service_count();
get_client_count();
get_session_count();
get_booking_count();
//draw_chart();
//get_top_trainers_list();
get_training_table();
get_chart_header();
get_chart_data();


function get_booking_count() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-full-event-book-count",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $('#session-book-count').html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function (e) {
            if (e.status == 'ok') {
                $('#session-book-count').html(e.session_count);

            } else {
                $('#session-book-count').html(e.session_count);
            }
        },
        complete: function () {
            //pass
        }
    });

}

function get_chart_data() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-chart-data",
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
            var item_divs = '';
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");


                // $('#complete-sessions-30d').html(e.day_array);
                // $('#pending-sessions-30d').html(e.pending_array);
                // $('#pending-sessions-30d').html(e.confirmed_array);

                draw_chart(e.day_array,e.pending_array,e.confirmed_array)

                
                //$("#loading-div").css({'display':'none'});

            } else {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").css("visibility", "hidden");
            }
        },
        complete: function () {
            $('.loading-div').addClass('hide-me');
            $('.results-div').removeClass('hide-me');
            $(".ajaxDataloader").html("");
            $(".ajaxDataloader").css("visibility", "hidden");
            $(".removeDataMessages").css("visibility", "hidden");
            $(".clientData-btn").prop('disabled', false).html('Log In');
            $(".loading-div-trainers").addClass('hide-me');
            $("#loading-div").css({'display':'none'});
            $(".clientData-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });

}

function get_chart_header() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-chart-header",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $("#complete-sessions-30d").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> loading...</p>');
            $("#pending-sessions-30d").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> loading...</p>');
        },
        success: function (e) {
            //console.log('get services done');
            //console.log(e.data);
            var item_divs = '';
            if (e.status == 'ok') {
                $("#complete-sessions-30d").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> loading...</p>');
                $("#pending-sessions-30d").html('<p class="modal-title"><i class="bx bx-loader bx-spin"></i> loading...</p>');

                $('#complete-sessions-30d').html(e.complete_results);
                $('#pending-sessions-30d').html(e.pending_results);

                
                //$("#loading-div").css({'display':'none'});

            } 
        },
        complete: function () {
           //pass
        }
    });

}

function get_top_trainers_list() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-top-trainers",
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
            var item_divs = '';
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");

                var data_export = e.data;
                $.each(data_export.data, function (key, val) {
                    var trainer_data = val.trainer_activities_array;

                    item_divs += '<div class="mt-3 media align-items-center">'
                    +'<img src="' + val.profile_picture + '" width="45" height="45" class="rounded-circle" alt="">'
                    +'<div class="ml-3 media-body">'
                        +'<p class="mb-0 text-white font-weight-bold">' + val.user_name + '</p>'
                        +'<p class="mb-0">Trainer</p>'
                    +'</div> <a href="/client-vw-book-trainer-session?record-id=' + val.record_id + '" class="btn btn-sm btn-light radius-10">Book ' + val.pronoun + '</a>'
                    +'</div>'
                    +'<hr>';

                });

                $('#top-trainers-div').html(item_divs);

                
                //$("#loading-div").css({'display':'none'});

            } else {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").css("visibility", "hidden");
            }
        },
        complete: function () {
            $('.loading-div').addClass('hide-me');
            $('.results-div').removeClass('hide-me');
            $(".ajaxDataloader").html("");
            $(".ajaxDataloader").css("visibility", "hidden");
            $(".removeDataMessages").css("visibility", "hidden");
            $(".clientData-btn").prop('disabled', false).html('Log In');
            $(".loading-div-trainers").addClass('hide-me');
            $("#loading-div").css({'display':'none'});
            $(".clientData-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });

}

function get_training_table() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-my-trainers-view",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $('#trainers-body').html('<tr>'
            +'<td><i class="bx bx-loader bx-spin"></i></td>'
            +'<td><i class="bx bx-loader bx-spin"></i></td>'
            +'<td><i class="bx bx-loader bx-spin"></i></td>'
            +'<td><i class="bx bx-loader bx-spin"></i></td>'
            +'<td><i class="bx bx-loader bx-spin"></i></td>'
            +'</tr>'
            );
        },
        success: function (e) {
            //console.log('get services done');
            //console.log(e.data);
            var item_divs = '';
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");

                var data_export = e.data;
                // console.log('Data export:',data_export);
                // for (var i = 0; i < data_export.length; i++) {                    
                //     console.log('Data export row:',data_export[i]);
                // }

                $.each(data_export.data, function (key, val) {              
                    //console.log('Data export row:',val);
                    //var session_data = val.trainer_activities_array;             
                    //console.log('Session_data:',session_data);

                    item_divs += '<tr>'
                        +'<td>'+val.user_name+'</td>'
                        +'<td>'+val.date_of_joining+'</td>'
                        +'<td>'+val.phone+'</td>'
                        +'<td>'+val.email_address+'</td>'
                        +'<td>'+val.unit_ui_display+'</td>'
                    +'</tr>';

                });

                $('#trainers-body').html(item_divs);

                
                //$("#loading-div").css({'display':'none'});

            } else {
                $('#trainers-body').html(e.messages);
            }
        },
        complete: function () {
            //pass
        }
    });

}

function get_service_count() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-service-count",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $('#service-count').html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function (e) {
            console.log(e.service_count);
            if (e.status == 'ok') {
                $('#service-count').html(e.service_count);

            } else {
                $('#service-count').html(e.service_count);
            }
        },
        complete: function () {
            //pass
        }
    });

}

function get_client_count() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-client-count",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $('#client-count').html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function (e) {
            if (e.status == 'ok') {
                $('#client-count').html(e.client_count);

            } else {
                $('#client-count').html(e.client_count);
            }
        },
        complete: function () {
            //pass
        }
    });

}

function get_session_count() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-full-session-count",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $('#session-count').html('<i class="bx bx-loader bx-spin"></i>');
        },
        success: function (e) {
            if (e.status == 'ok') {
                $('#session-count').html(e.session_count);

            } else {
                $('#session-count').html(e.session_count);
            }
        },
        complete: function () {
            //pass
        }
    });

}

function draw_chart(date,pending,complete){
    console.log(date);
    console.log(pending);
    console.log(complete);
    "use strict";
	// chart 1
	var options = {
		series: [{
			name: 'Complete',
			data: complete
		}, {
			name: 'Pending',
			data: pending
		}],
		chart: {
			foreColor: 'rgba(255, 255, 255, 0.65)',
			type: 'area',
			height: 340,
			toolbar: {
				show: false
			},
			zoom: {
				enabled: false
			},
			dropShadow: {
				enabled: false,
				top: 3,
				left: 14,
				blur: 4,
				opacity: 0.10,
			}
		},
		legend: {
			position: 'top',
			horizontalAlign: 'left',
			offsetX: -25
		},
		dataLabels: {
			enabled: false
		},
		stroke: {
			show: true,
			width: 3,
			curve: 'smooth'
		},
		tooltip: {
			theme: 'dark',
			y: {
				formatter: function (val) {
					return "" + val + " Sessions"
				}
			}
		},
		fill: {
			type: 'gradient',
			gradient: {
				shade: 'light',
				gradientToColors: ['#fff', 'rgba(255, 255, 255, 0.65)'],
				shadeIntensity: 1,
				type: 'vertical',
				inverseColors: false,
				opacityFrom: 0.4,
				opacityTo: 0.1,
				//stops: [0, 50, 65, 91]
			},
		},
		grid: {
			show: true,
			borderColor: 'rgba(255, 255, 255, 0.12)',
			strokeDashArray: 5,
		},
		colors: ["#fff", "rgba(255, 255, 255, 0.65)"],
		yaxis: {
			labels: {
				formatter: function (value) {
					return value + "$";
				}
			},
		},
		xaxis: {
			categories: date,
		},
        title: {
            text: 'Complete vs Pending Sessions',
            align: 'center',
            margin: 10,
            offsetX: 0,
            offsetY: 0,
            floating: true,
        }
	};
    var chart = new ApexCharts(document.querySelector("#chart1"), options);
	chart.render();
	
}
