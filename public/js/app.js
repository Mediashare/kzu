function flash(type, message) {
    var options = {
        progress: true,
    };
    if (type === 'success') {
        window.FlashMessage.success(message, options);
    } else if (type === 'warning' || type === 'warnings') {
        window.FlashMessage.warning(message, options);
    } else if (type === 'error' || type === 'errors') {
        window.FlashMessage.error(message, options);
    } else if (type === 'info') {
        window.FlashMessage.info(message, options);
    } else {
        window.FlashMessage.info(message, options);
    }
}

function request (url = "/api/command") {
    $.post(url, 
        {
            command: selector.attr('value'),
            pipe: pipe,
            input_password: input_password
        }, 
        function (data) {
            console.log(data);
            if (!data) {
                Typer.write("Command not found. <br/>");
                Typer.write("");
                user();
            } else {
                user(data.user.email);
                Typer.write(data.response);
                pipe = data.pipe;
                input_password = data.input_password;
                if (data.status !== "input" && data.status !== "wait") { Typer.write(""); }
                else if (data.status == "wait") { 
                    wait(data.url);
                }
            }
            $(document).scrollTop($(document).height());
        }
    );
}

function user (email) {
    if (typeof email === "undefined" || email == null) {
        email = "guest@marquand.pro";
    }
    machine_number = email;
}

function wait (url = null) {
    Typer.write('<div class="spinner-grow spinner-grow-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>');
    Typer.blocked = true; 
    // Wait {data.seconds}
    // send request to api url
}

function history (index) {
    value = $(histories).get(index);
    if (typeof value !== undefined) {
        if ($('#console span#command').length > 0) {
            $('#console span#command').text(value).attr('value', value);
        } else {
            $('#console').append("<span id='command' value='"+value+"'>"+value+"</span>");
        }
    }
}