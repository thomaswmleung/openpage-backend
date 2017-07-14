function login_function(user_info, login_response, login_error) {
    console.log(user_info);
    $.ajax({
        type: "POST",
        url: base_url('/login/login-user'),
        dataType: 'text', // what to expect back from the PHP script, if anything
//            cache: false,
//            contentType: false,
//            processData: false,
        data: user_info,
        success: function (data) {
         
            var res = JSON.parse(data);
            //and from data you can retrive your user details and show them in the modal
            var result = res['success'];
            if (result == 'true') {
                login_response(res);
            } else {
                login_error(res);
            }

        }

    });
}


function registration_function(user_info, registration_response, registration_error) {

    $.ajax({
        type: "POST",
        url: base_url('/login/user-registration'),
        dataType: 'text', // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: user_info,
        mimeType: "multipart/form-data",
        success: function (data) {
            var result = JSON.parse(data);

            //and from data you can retrive your user details and show them in the modal
            if (result['success'] == 'true') {
                registration_response(result);
            } else {
                registration_error(result);
            }
        }
    });
}