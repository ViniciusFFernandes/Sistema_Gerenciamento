function gerarRelatorio(){
    if($("#data_inicio").val() == ''){
        alertaPequeno("Informe o periodo desejado!");
        return;
    }

    if($("#data_fim").val() == ''){
        alertaPequeno("Informe o periodo desejado!");
        return;
    }    

    if($("#idempresas").val() == ''){
        alertaPequeno("Informe a empresa que desejado tirar o relatório!");
        return;
    }

    $("#form_edita").submit();
}
