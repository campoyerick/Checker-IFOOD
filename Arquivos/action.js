/*
 * Projeto Ifood
 * Copie oque eu faço, Mas não pode copiar oque sei fazer
 * Copyright [#ExplicitChecker] 11/2017 by: Thiagão
 * Discord: Thiaga1#3174
 */
var audio_live = new Audio('arquivos/live.wav');
var executar = true;
var proxy_type = "socks5";

$(document).ready(function () {
    $("#iniciar").click(function () {
        $('#result').fadeIn(2000);
        $(this).attr("disabled", true);
        $("#parar").attr("disabled", false);
        $("#status").html('Teste Iniciado Com Sucesso <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');
        executar = true;
        iniciar();
    });
    $("#parar").click(function () {
        $(this).attr("disabled", true);
        $("#iniciar").attr("disabled", false);
        document.getElementById('lista').disabled = false;
        $("#status").html('<i class="fa fa-pause" aria-hidden="true"></i> Teste parado');
        executar = false;
    });

});
/* Get socks */
$('.get_socks').click(function () {
    $(this).html('<i class="fa fa-spinner fa-spin"></i> Aguarde').attr('disabled', true);
    $.get('socks.php', function (data) {
        var s = data.split('|');
        proxy_type = s[0];
        $('#' + s[0]).click();
        $('#socks').val(s[1]);
        $('.get_socks').fadeOut(1500, function () {
            $('.testar_socks').fadeIn(1500);
        }).attr('disabled', false).html('<i class="fa fa-download"></i> Importar Socks5');

    });
});
// Trocar o botão de Testar <> importar
$('#socks').keyup(function () {
    if ($(this).val().length > 0) {
        $('.get_socks').fadeOut(1500, function () {
            $('.testar_socks').fadeIn(1500);
        }).attr('disabled', false);
    } else {
        $('.testar_socks').html('<i class="fa fa-play-circle"></i> Testar Socks5').fadeOut(500, function () {
            $('.get_socks').fadeIn(500);
            $('.t_status').fadeOut(500).find('span').text('0');
        }).attr('disabled', false);
    }
});
function r(e, v) {
    var lines = $(e).val().split('\n');
    lines.forEach(function (va, i) {
        if (va === v) {
            lines.splice(i, 1);
        }
    });
    $(e).val(lines.join("\n"));
}
/* testar socks */
$('.testar_socks').click(function () {

    $(this).html('<i class="fa fa-spinner fa-spin"></i> Aguarde').attr('disabled', true);
    $('#iniciar').attr('disabled', true);
    $('.t_status').find('span').text('0');

    $('.t_status').fadeIn(2500);
    var socks = $('#socks').val().split("\n");
    var total = socks.length;
    if (socks.length === undefined) {
        total = 0;
    }
    $('#t_total').html(total);

    socks.forEach(function (v, f) {
        setTimeout(function () {

            $.get('socks.php?s=' + v + '&type=' + proxy_type, function (data) {
                $("#t_testado").text(eval($('#t_testado').text()) + 1);
                if (data === 'LIVE') {
                    $(".t_aprovados").text(eval($('.t_aprovados').text()) + 1);
                } else {
                    r('#socks', v);

                    $(".t_reprovados").text(eval($('.t_reprovados').text()) + 1);
                }
                if ($("#t_testado").text() === $('#t_total').text()) {
                    $('.testar_socks').html('<i class="fa fa-play-circle"></i> Testar Socks5').attr('disabled', false);
                    $('#iniciar').attr('disabled', false);
                }
            });


        }, 200 * f);
    });
});


function titulo(novo) {
    document.title = novo;
}
function contar_total(lista) {
    'use strict';
    var array = lista.value.split("\n");
    var total = array.length;

    if (array.length === undefined) {
        total = 0;
    }
    $("#tudo_conta").text(total);

}

function remover_linha(id) {
    var lines = $(id).val().split('\n');
    lines.splice(0, 1);
    $(id).val(lines.join("\n"));
}

var socks_die = [];

function reseta() {
    $(".aprovada_conta").text("0");
    $(".aprovada_conta_v").text("0");
    $(".reprovada_conta").text("0");
    $("#testado").text("0");
    $("#tudo_conta").text("0");
    $("#sock_die").text("0");
    $("#sock_ruim").html(null);
    status('Aguardando inicio', 'dark');
}
function unique(array) {
    return array.filter(function (el, index, arr) {
        return index == arr.indexOf(el);
    });
}
function remover_linhas_vazias() {
    var array = $("#lista").val().split('\n');
    var array_sock = $("#socks").val().split('\n');
    array = unique(array);
    array_sock = unique(array_sock);
    for (i = 0; i < array.length; i++) {
        array[i] = array[i].trim();
        array[i] = array[i].replace('   ', '');
        if (array[i].length === 0) {
            array.splice(i, 1);
        }

    }
    for (i = 0; i < array_sock.length; i++) {
        array_sock[i] = array_sock[i].trim();
        array_sock[i] = array_sock[i].replace('   ', '');
        if (array_sock[i].length === 0) {
            array_sock.splice(i, 1);
        }

    }
    $("#lista").val(array.join("\n"));
    $("#socks").val(array_sock.join("\n"));
}
function status(text, type) {
    if (!type) {
        type = "primary";
    }
    $("#status").removeClass().addClass("label label-" + type).html(text);
}
function notificar(msg, icone = "notificacao.png") {

    if (Notification.permission === "granted") {
        var options = {
            body: msg,
            icon: "arquivos/" + icone,
            dir: "ltr"
        };
        var notification = new Notification("Informação", options);
    } else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function (permission) {
            if (!('permission' in Notification)) {
                Notification.permission = permission;
            }

            if (permission === "granted") {
                var options = {
                    body: msg,
                    icon: "arquivos/" + icone,
                    dir: "ltr"
                };
                var notification = new Notification("Informação", options);
            }
        });
}
}

function iniciar() {

    document.getElementById('lista').disabled = true;
    reseta();
    var lista = document.getElementById("lista").value;
    var proxys = document.getElementById("socks").value;
    if (lista.length == "0") {
        $("#modal_mailpass").modal();
        document.getElementById('iniciar').disabled = false;
        $("#parar").attr("disabled", false);
        document.getElementById('lista').disabled = false;
        $('#result').fadeOut(1000);
        status('<i class="fa fa-times" aria-hidden="true"></i> Lista Inválida!', 'warning');
        return;
    }
    if (proxys.length == "0") {
        $("#modal_sock").modal();
        document.getElementById('iniciar').disabled = false;
        $("#parar").attr("disabled", false);
        document.getElementById('lista').disabled = false;
        $('#result').fadeOut(1000);
        status('<i class="fa fa-times" aria-hidden="true"></i> Insira os socks5!', 'warning');

        return;
    }
    remover_linhas_vazias();
    lista = document.getElementById("lista").value;
    proxys = document.getElementById("socks").value;
    contar_total(document.getElementById("lista"));
    status('<i class="fa fa-spin fa-spinner" aria-hidden="true"></i> Iniciando Testador', 'info');

    start();

}
function start() {
    if (!executar) {
        return false;
    }
    var socks = document.getElementById("socks").value.split("\n");
    if (socks.length == "1" && socks[0] == "") {
        setTimeout(function () {
            var audio_terminou = new Audio('arquivos/blop.mp3');
            audio_terminou.play();
            notificar("Socks acabaram");
            document.getElementById('iniciar').disabled = false;
            document.getElementById('lista').disabled = false;
            status('<i class="fa fa-times" aria-hidden="true"></i> Socks acabaram', 'danger');
            delete array;
        }, 1001);
        return;
    }

    var array = lista.value.split("\n");
    if (array.length == "1" && array[0] == "") {
        setTimeout(function () {
            var audio_terminou = new Audio('arquivos/blop.mp3');
            audio_terminou.play();
            notificar("Teste Finalizado Com Sucesso!");
            titulo('Ifood');
            document.getElementById('iniciar').disabled = false;
            document.getElementById('lista').disabled = false;
            document.getElementById("lista").value = "";
            status('<i class="fa fa-check" aria-hidden="true"></i> Teste Finalizado com Sucesso! ');
            delete array;
            $("#modal_done").modal();

        }, 1001);
        return;
    }


    startchk(array[0]);
    delete array[0];
    return;


}

function startchk(url) {

    var proxy = $("#socks").val().split('\n');
    proxy = proxy[0];

    $.ajax({
        url: "api.php?lista=" + encodeURIComponent(url) + "&proxy=" + proxy + "&type=" + proxy_type,
        type: "GET",
        async: true,

        success: function (data) {


            var countsock = (eval(document.getElementById("sock_die").innerHTML) + 1);

            var json = $.parseJSON(data);
            var str = json.str;

            switch (json.status) {
                case 0:
                    remover_linha("#lista");
                    status('<i class="fa fa-check" aria-hidden="true"></i> Aprovada! ', 'success');
                    setTimeout(function () {
                        status('<i class="fa fa-spinner fa-spin fa-fw"></i> Testando.', 'info');
                    }, 500);
                    setTimeout(function () {
                        status('<i class="fa fa-spinner fa-spin fa-fw"></i> Testando..', 'info');
                    }, 750);
                    setTimeout(function () {
                        status('<i class="fa fa-spinner fa-spin fa-fw"></i> Testando...', 'info');
                    }, 1000);
                    var div = document.createElement("div");
                    div.innerHTML = json.str;
                    notificar(div.innerText, "check.png");
                    audio_live.play();
                    $("#aprovadas").append(str);
                    $(".aprovada_conta").text(eval($('.aprovada_conta')[0].innerText) + 1);

                    document.getElementById("testado").innerHTML = (eval(document.getElementById("testado").innerHTML) + 1);



                    break;
                case 5:
                    remover_linha("#lista");
                    status('<i class="fa fa-times" aria-hidden="true"></i> Reprovada! ', 'danger');

                    setTimeout(function () {
                        status('<i class="fa fa-spinner fa-spin fa-fw"></i> Testando.', 'info');
                    }, 500);
                    setTimeout(function () {
                        status('<i class="fa fa-spinner fa-spin fa-fw"></i> Testando..', 'info');
                    }, 750);
                    setTimeout(function () {
                        status('<i class="fa fa-spinner fa-spin fa-fw"></i> Testando...', 'info');
                    }, 1000);

                    document.getElementById("testado").innerHTML = (eval(document.getElementById("testado").innerHTML) + 1);
                    $(".reprovada_conta").text(eval($('.reprovada_conta')[0].innerText) + 1);

                    $("#reprovadas").append(str);
                    break;

                case 56:

                    status('<i class="fa fa-times" aria-hidden="true"></i> Sock Die! ', 's bg-purple');
                    setTimeout(function () {
                        status('<i class="fa fa-spinner fa-spin fa-fw"></i> Testando.', 'info');
                    }, 500);
                    setTimeout(function () {
                        status('<i class="fa fa-spinner fa-spin fa-fw"></i> Testando..', 'info');
                    }, 750);
                    setTimeout(function () {
                        status('<i class="fa fa-spinner fa-spin fa-fw"></i> Testando...', 'info');
                    }, 1000);
                    $("#countsock").text(countsock);

                    $("#sock_ruim").append(str);
                    document.getElementById("sock_die").innerHTML = countsock;
                    remover_linha("#socks");

                    break;

            }
            titulo('[' + $('#testado').text() + '/' + $('#tudo_conta').text() + '] Ifood');
            start();
        },
        error: function () {
            start();
        }


    });


}
function selectText(containerid) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select();
    } else if (window.getSelection()) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
    }
}

$(document).ready(function () {
    $('#btn_live').click(function () {
        $('#aprovadas').toggle(200, function () {
            if ($(this).is(':visible')) {
                $('#btn_live').html('<i class="fa fa-minus"> </i><font color="black"> Esconder </font>');
            } else {
                $('#btn_live').html('<i class="fa fa-plus"> </i><font color="black"> Mostrar </font>');
            }
        });

    });
    $('#btn_die').click(function () {
        $('#reprovadas').toggle(200, function () {
            if ($(this).is(':visible')) {
                $('#btn_die').html('<i class="fa fa-minus"> </i><font color="black"> Esconder </font>');
            } else {
                $('#btn_die').html('<i class="fa fa-plus"> </i><font color="black"> Mostrar </font>');
            }
        });
    });
    $('#btn_live_v').click(function () {
        $('#aprovadas_v').toggle(200, function () {
            if ($(this).is(':visible')) {
                $('#btn_live_v').html('<i class="fa fa-minus"> </i><font color="black"> Esconder </font>');
            } else {
                $('#btn_live_v').html('<i class="fa fa-plus"> </i><font color="black"> Mostrar </font>');
            }
        });
    });

    $('#btn-sock-hide').click(function () {
        $('#sock_ruim').toggle(200, function () {
            if ($(this).is(':visible')) {
                $('#btn-sock-hide').html('<i class="fa fa-minus"> </i><font color="black"> Esconder </font>');
            } else {
                $('#btn-sock-hide').html('<i class="fa fa-plus"> </i><font color="black"> Mostrar </font>');
            }
        });
    });

});
