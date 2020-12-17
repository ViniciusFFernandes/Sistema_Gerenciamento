$(document).ready(function(){
    $("#sala_vlr_total").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
});

function testaDados(operacao){
    chamaGravar(operacao);
}

function excluirFuncionario(idsalarios_funcionarios){
    $("#btnExcluir_" + idsalarios_funcionarios).html('<img src="../icones/carregando.gif" width="15px;">');
    //
    $.post("salarios_grava.php",
        {operacao: "excluiFunc", idsalarios_funcionarios: idsalarios_funcionarios},
        function(data){
            if(data == 'Ok'){
                $("#linhaSalario_" + idsalarios_funcionarios).remove();
            }else{
                alertaPequeno(data);
                $("#btnExcluir_" + idsalarios_funcionarios).html('<img src="../icones/excluir2.png" width="15px;">');
            }
        }, "html");
}