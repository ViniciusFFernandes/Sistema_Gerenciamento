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
    if(operacao == 'reabrir'){
        var titulo = "<b>Deseja reabrir os salários?</b>";
        var msg = "<b style='color: red;'>Atenção:</b> Não aconselhamos está ação, todas as contas referentes aos salários serão excluidas!";
        confirmar(msg, titulo, "chamaGravar('" + operacao + "');", "tada");    
    }else{
        chamaGravar(operacao);
    }
    
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

function gravaDadosSalarios(idsalarios_funcionarios){
    $("#spanAtt_" + idsalarios_funcionarios).html('<img src="../icones/carregando.gif" width="15px;">');
    //
    var faltas = $("#safu_dias_" + idsalarios_funcionarios).val();
    var valor = $("#safu_total_" + idsalarios_funcionarios).val();
    //
    $.post("salarios_grava.php",
        {operacao: "gravaDadosSalario", idsalarios_funcionarios: idsalarios_funcionarios, safu_dias: faltas, safu_total: valor},
        function(data){
            if(data == 'Ok'){
                $("#spanAtt_" + idsalarios_funcionarios).html('<font color="#32CD32"><b>Ok</b></font>');
            }else{
                //alertaPequeno(data);
                $("#spanAtt_" + idsalarios_funcionarios).html('<font color="red"><b>Erro</b></font>');
            }
        }, "html");
    //
}