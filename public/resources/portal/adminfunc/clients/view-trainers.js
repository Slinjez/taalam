
get_client_list();
function get_client_list() {
    var b = {
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-trainer-lists",
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
                        +'<td>'+val.email_address+'</td>'
                        +'<td>'+val.phone+'</td>'
                        +'<td>'+val.date_of_joining+'</td>'
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
