// Menu
function closeMenu() {
    $('#menu-close').toggle('hide');
    $('.list-group').toggle('hide');
    $('#menu-open').toggle('show').removeClass('d-none');
}
function openMenu() {
    $('#menu-open').toggle('hide');
    $('.list-group').toggle('show');
    $('#menu-close').toggle('show');
}
// Editor
function saveContent() {
    var filename = $('#input_filename');
    var content = myCodeMirror.getValue();
    if (filename.val()) {
        filename.removeClass('is-invalid');
        $.post(window.location, {filename: filename.val(), content: content}).done(function (data) {
            if (data.redirection) {
                window.location = data.redirection;
                return true;
            } else {
                window.location = window.location;
            }
        }).fail(function (data) {
            if (data.responseJSON) {
                $(data.responseJSON.messages).each(function (index, message) {
                    flash(data.responseJSON.status, message);
                });
            } else {
                flash('error', 'Request return ('+data.status+'): '+data.statusText+'.');
            }
        });
    } else {
        flash('error', 'You need to input filename.');
        filename.addClass('is-invalid').focus();
    }
}
$('html').on('keydown', function (event) {
    if (event.metaKey || event.ctrlKey) {
        var key = String.fromCharCode(event.which).toLowerCase();
        if (key === 's') {
            event.preventDefault();
            saveContent();
        }
    }
});