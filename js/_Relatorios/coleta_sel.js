function gerarRelatorio(){
    if($("#data_inicio").val() == ''){
        alertaPequeno("Informe o periodo desejado!");
        return;
    }

    if($("#data_fim").val() == ''){
        alertaPequeno("Informe o periodo desejado!");
        return;
    }    

    $("#form_edita").submit();
}