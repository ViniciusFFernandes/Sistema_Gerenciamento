window.setTimeout(function(){
    document.getElementById("botao_alerta").click();
    }, 6000);

function alertaPequeno(msg, titulo = '', animate = ''){
    if(titulo == ''){
        titulo = "<b style='color: red;'>Atenção</b>";
    }
    if(animate == ''){
        animate = 'bounceInUp';
    }
    bootbox.alert({
        title: titulo,
        message: msg,
        size: 'small',
        className: animate + ' animated'
    });
}

function alertaGrande(msg, titulo = '', animate = ''){
    if(titulo == ''){
        titulo = "<b style='color: red;'>Atenção</b>";
    }
    if(animate == ''){
        animate = 'bounceInUp';
    }
    bootbox.alert({
        title: titulo,
        message: msg,
        size: 'large',
        className: animate + ' animated'
    });
}

function confirmar(msg, titulo = '', functionCallback = '', animate = ''){
    if(animate == ''){
        animate = 'bounceInUp';
    }
    if(titulo == ''){
        titulo = '<b>Atenção</b>';
    }
    bootbox.confirm({
        title: titulo,
        message: msg,
        className: animate + ' animated',
        buttons: {
            cancel: {
                label: '<img src="../icones/excluir2.png"> Cancelar'
            },
            confirm: {
                label: '<img src="../icones/certo.png"> Confirmar'
            }
        },
        callback: function(result){
            if(result && functionCallback != ''){
                eval(functionCallback);
            }
        }
    });
}
