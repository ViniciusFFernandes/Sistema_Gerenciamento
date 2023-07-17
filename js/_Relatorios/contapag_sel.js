function gerarRelatorio(){
    if($("#data_inicio").val() == ''){
        alertaPequeno("Informe o periodo desejado!");
        $("#data_inicio").focus();
        return;
    }

    if($("#data_fim").val() == ''){
        alertaPequeno("Informe o periodo desejado!");
        $("#data_fim").focus();
        return;
    }    

    $("#form_edita").submit();
}
