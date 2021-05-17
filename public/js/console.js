var user;
var pipe = {};
var input_password;
var selector;

var Typer = {
    text: null,
    accessCountimer: null,
    index: 0,
    speed: 8,
    accessCount: 0,
    deniedCount: 0,
    blocked: false, 
    init: function () {
        accessCountimer = setInterval(function () {
            Typer.updLstChr();
        }, 500);
        Typer.text = Typer.text.slice(0, Typer.text.length - 1);
    },

    content: function () {
        return $("#console").html();
    },

    write: function (str, prefix = true) {
        $('#console span#marker').remove();
        if (Typer.blocked === false) {
            if (str !== "<span id='marker'>|</span>") {
                if (prefix) {
                    str = "<span class='a'>" + machine_number + "</span>:<span class='b'>~</span><span class='c'>$</span> " + str;
                }
                Typer.text += str;
                var rtn = new RegExp("\n", "g");
                $("#console").html(Typer.text.replace(rtn, "<br/>"));
            } else {
                $('#console').append(str);
            }
            return false;
        }
    },
    
    input: function (key) {
        if (Typer.blocked === false) {
            $('#console span#marker').remove();
            if ($('#console span#command').length > 0) {
                if (input_password === true) { input = "*"; } 
                else { input = key; }
                $('#console span#command').append(input).attr('value', $('#console span#command').attr('value') + key);
            } else {
                if (input_password === true) { input = "*"; } 
                else { input = key; }
                $('#console').append("<span id='command' value='"+key+"'>"+input+"</span>");
            }
            $(document).scrollTop($(document).height()); 
            return false;
        }
    },
    
    cancel: function () {
        if (Typer.blocked === true) {
            Typer.blocked = false;
        }
        Typer.write("<br/>", false);
        Typer.write("")
    },

    delete: function () {
        $('#console span#marker').remove();
        if ($('#console span#command').length > 0) {
            $('#console span#command').text($('#console span#command').text().slice(0, -1));
        }
        if ($('#console span#command').attr('value')) {
            $('#console span#command').attr('value', $('#console span#command').attr('value').slice(0, -1));
        }
        return false;
    },

    clear: function () {
        $('#console span#marker').remove();
        Typer.text = "";
        $('#console').empty();
        Typer.write("");
    },
    
    submit: function () {
        $('#console span#marker').remove();
        if ($('#console span#command').length > 0 && $('#console span#command').text()) {
            selector = $('#console span#command');
            histories.push(selector.text());
            console.log(histories);
            Typer.write(selector.html() + "<br/>", false);
            if (selector.attr('value') === 'nettoyer') {
                Typer.clear();
            } else {
                if (typeof data !== "undefined" && data.url) {
                    url = data.url;
                } else { url = "/api/command"; }
                request(url);
            }
        }
        $('#console span#command').attr('id', null);
        return false;
    },

    addText: function (key) {
        if (key.keyCode == 18) {
            Typer.accessCount++;

            if (Typer.accessCount >= 3) {
                Typer.makeAccess();
            }
        } else if (key.keyCode == 20) {
            Typer.deniedCount++;

            if (Typer.deniedCount >= 3) {
                Typer.makeDenied();
            }
        } else if (key.keyCode == 27) {
            Typer.hidepop();
        } else if (Typer.text) {
            var cont = Typer.content();
            if ($('#console span#marker').length > 0) {
                $('#console span#marker').remove();
            }

            if (cont.substring(cont.length - 1, cont.length) == "|") {
                $("#console").html($("#console").html().substring(0, cont.length - 1));
            }

            if (key.keyCode != 8) {
                Typer.index += Typer.speed;
            } else {
                if (Typer.index > 0)
                    Typer.index -= Typer.speed;
            }
            var text = Typer.text.substring(0, Typer.index)
            var rtn = new RegExp("\n", "g");
           
            $("#console").html(text.replace(rtn, "<br/>"));
        }

        if (key.preventDefault && key.keyCode != 122) {
            key.preventDefault()
        };

        if (key.keyCode != 122) { // otherway prevent keys default behavior
            key.returnValue = false;
        }
    },

    updLstChr: function () {
        var cont = this.content();
        if ($('#console span#marker').length > 0) {
            $('#console span#marker').remove();
        } else if (cont.substring(cont.length - 1, cont.length) == "|") {
            $("#console").html($("#console").html().substring(0, cont.length - 1));
        } else {
            this.write("<span id='marker'>|</span>"); // else write it
        }
    }
}

function replaceUrls(text) {
    var http = text.indexOf("http://");
    var space = text.indexOf(".me ", http);

    if (space != -1) {
        var url = text.slice(http, space - 1);
        return text.replace(url, "<a href=\"" + url + "\">" + url + "</a>");
    } else {
        return text
    }
}

var timer = setInterval("t();", 30);

function t() {
    Typer.addText({
        "keyCode": 123748
    });

    if (Typer.index > Typer.text.length) {
        clearInterval(timer);
    }
}