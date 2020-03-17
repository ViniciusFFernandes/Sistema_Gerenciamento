function ativarDesativar(operacao, idgrupos_acessos_programas){
  var novoBtn;
  var executa;
  if(operacao == 'Ativar'){
    executa = 1;
    novoBtn = '<button type="button" onclick="ativarDesativar(\'Desativar\', ' + idgrupos_acessos_programas + ')" class="btn btn-danger">Desativar</button>';
  }

  if(operacao == 'Desativar'){
    executa = 0;
    novoBtn = '<button type="button" onclick="ativarDesativar(\'Ativar\', ' + idgrupos_acessos_programas + ')" class="btn btn-success">Ativar</button>';
  }
  $("#btn_" + idgrupos_acessos_programas).html('<img src="../icones/carregando_engrenagens.gif" width="34px">');
  $.post("grupos_acessos_grava.php", 
  {operacao: 'ativarDesativarProgram', gap_executa: executa, idgrupos_acessos_programas: idgrupos_acessos_programas}, 
  function(data){
    if(data.retorno == 'ok'){
      $("#btn_" + idgrupos_acessos_programas).html(novoBtn);
    }else{
      alertaGrande(data.msg)
    }
  }, "json")
}

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#grac_nome").val() == ""){
    $("#grac_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#grac_nome").css("border-color", "green");
  }
  //
 chamaGravar('gravar');
}