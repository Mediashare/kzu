function connexion (selector) {
    current_command = "connexion";
    if (!step) {
        $.post('/api/connexion/', {}, function (data) {
            Typer.write(data.response);
            if (!data.step) { Typer.write(""); }
            $(document).scrollTop($(document).height());
            step = data.step
        });
    } else if (step == '1') {
        $.post('/api/connexion/', {email: selector.html()}, function (data) {
            Typer.write(data.response);
            if (!data.step) { Typer.write(""); }
            $(document).scrollTop($(document).height());
            if (data.status === "error") {
                input_password = false;
                current_command = null;
                step = null
            } else {
                step = data.step
                input_password = true;
            }
        });
    } else if (step == '2') {
        $.post('/api/connexion/', {password: selector.attr('value')}, function (data) {
            Typer.write(data.response);
            if (!data.step) { Typer.write(""); }
            $(document).scrollTop($(document).height());
            step = data.step
            input_password = false;
            current_command = null;
        });
    }
}
function inscription (selector) {
    current_command = "inscription";
    if (!step) {
        $.post('/api/inscription/', {}, function (data) {
            Typer.write(data.response);
            $(document).scrollTop($(document).height());
            step = data.step
        });
    } else if (step == '1') {
        $.post('/api/inscription/', {email: selector.html()}, function (data) {
            Typer.write(data.response);
            if (!data.step) { Typer.write(""); }
            $(document).scrollTop($(document).height());
            if (data.status === "error") {
                input_password = false;
                current_command = null;
                step = null
            } else {
                step = data.step
                input_password = true;
            }
        });

    } else if (step == '2') {
        input_password = false;
        $.post('/api/inscription/', {password: selector.attr('value')}, function (data) {
            Typer.write(data.response);
            $(document).scrollTop($(document).height());
            if (data.status === "error") {
                input_password = false;
                current_command = null;
                step = null
            } else {
                step = data.step
                input_password = true;
            }
        });
    } else if (step == '3') {
        input_password = false;
        $.post('/api/inscription/', {repeat_password: selector.attr('value')}, function (data) {
            Typer.write(data.response);
            $(document).scrollTop($(document).height());
            step = data.step
            Typer.write("");
            current_command = null;
        });
    }
}