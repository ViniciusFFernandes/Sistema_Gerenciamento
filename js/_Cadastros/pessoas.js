$(document).ready(function(){
  buscaTelefones();
  $("#pess_cpf").mask("999.999.999-AA", 
    {translation: {
      '9': {
        pattern: /[0-9]/,
        optional: false
      }
    }
  });
  $("#pess_rg").mask("99.999.999-A", 
    {translation: {
      '9': {
        pattern: /[0-9]/,
        optional: false
      }
    }
  });
  $("#pess_cnpj").mask("99.999.999/9999-99",
    {translation: {
      '9': {
        pattern: /[0-9]/,
        optional: false
      }
    }
  });
  $("#pnum_numero").mask("9999-99999",
    {translation: {
      '9': {
        pattern: /[0-9]/,
        optional: false
      }
    }
  });
  $("#pnum_DDD").mask(" 999",
    {translation: {
      '9': {
        pattern: /[0-9]/,
        optional: false
      }
    }
  });
  $("#pess_cep").mask("99999-999",
    {translation: {
      '9': {
        pattern: /[0-9]/,
        optional: false
      }
    }
  });
});

function validaSenha(){
  var _idpessoas = $("#id_cadastro").val();
  var _pess_senha = $("#pess_senha").val();
  $.post("pessoas_grava.php",
    {operacao: "validaSenha", idpessoas: _idpessoas},
    function(result){
    //alert(result);
    //return;
      if(result.existe == 'true'){
        if (_pess_senha.length < 4 && _pess_senha != '') {
          $("#pess_senha").css({"border-color": "#FE2E2E"});
        $("#verificacaoRetorno").val("Cancelar");
        }else{
          $("#pess_senha").css({"border-color": "#2EFE2E"});
          $("#verificacaoRetorno").val("Aprovado");
        }
      }else{
        if(_pess_senha.length >= 4 && _pess_senha != '') {
          $("#pess_senha").css({"border-color": "#2EFE2E"});
          $("#verificacaoRetorno").val("Aprovado");
        }else{
          $("#pess_senha").css({"border-color": "#FE2E2E"});
          $("#verificacaoRetorno").val("Cancelar");
        }
      }
    }, "json");
}

function validarLogin(){
  var _idpessoas = $("#id_cadastro").val();
  var _pess_usuario = $("#pess_usuario").val();
  var _pess_senha = $("#pess_senha").val();
  var _pess_idgrupos_acessos = $("#pess_idgrupos_acessos").val();
  var _verificacaoRetorno = $("#verificacaoRetorno").val();

  if(_pess_usuario == ""){
    alertaPequeno('Não é permitido gravar o nome em branco!');
    return;
  }

  if(_pess_idgrupos_acessos == ""){
    alertaPequeno('Não é permitido gravar sem um grupo de acesso!');
    return;
  }

  if(_pess_idgrupos_acessos == 1){
    alertaPequeno('Não é permitido incluir usuarios no grupo Administradores!');
    return;
  }

  if (_verificacaoRetorno == "Aprovado") {
    gravarUser(_idpessoas, _pess_usuario, _pess_senha, _pess_idgrupos_acessos);
  }else{
    alertaPequeno("Corrija os campos indicados em vermelho!");
  }
}

function verificaUser(){
  //alert("aosifnsdgoa");
  var _idpessoas = $("#id_cadastro").val();
  var _pess_usuario = $("#pess_usuario").val();

  $.post("pessoas_grava.php",
  {operacao: "validaUsuario", idpessoas: _idpessoas, pess_usuario: _pess_usuario},
  function(data){
  //alert(data);
  //return;
    if(data.existe == 'true'){
      $("#pess_usuario").css({"border-color": "#FE2E2E"});
      $("#verificacaoRetorno").val("Cancelar");
      return;
    }else{
      $("#pess_usuario").css({"border-color": "#2EFE2E"});
      $("#verificacaoRetorno").val("Aprovado");
    }
  }, "json");
}

function gravarUser(idpessoas, pess_usuario, pess_senha, pess_idgrupos_acessos){
  //
  $("#imgCarregandoLogin").show();
  $("#imgCarregandoLogin").html('<img src="../icones/carregando_engrenagens.gif" width="25px">');
  $("#btnGravaLogin").attr("disabled", true);
  //
  $.post("pessoas_grava.php",
  {operacao: "gravaLogin", idpessoas: idpessoas, pess_usuario: pess_usuario, pess_senha: pess_senha, pess_idgrupos_acessos: pess_idgrupos_acessos},
  function(data){
  //
  $("#imgCarregandoLogin").hide();
  $("#imgCarregandoLogin").html('');
  $("#btnGravaLogin").attr("disabled", false);
  //
  if(data.retorno == 'ok'){
    $("#fechaAlteraLogin").click();
    $("#pess_senha").val("");
  }else{
    alertaPequeno(data.msg);
    return;
  }
    
  }, "json");
}

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#pess_nome").val() == ""){
    alertaPequeno("Por favor, informe um nome!")
    $("#pess_nome").css("border-color", "red");
    return;
  }else{
    $("#pess_nome").css("border-color", "green");
  }

  if($("#pess_idcidades").val() <= 0){
    alertaPequeno("Por favor, informe um nome!")
    $("#pess_cidades").css("border-color", "red");
    return;
  }else{
    $("#pess_cidades").css("border-color", "green");
  }

  chamaGravar('gravar');
}

function buscaTelefones(){
  // $("#divTelefone").html('<center><img src="../icones/carregando.gif" width="15px"><center>')
  var _idpessoas = $("#id_cadastro").val();
  if (_idpessoas == "") {
    return;
  }
  $.post("pessoas_grava.php",
  {operacao: "buscaTelefones", idpessoas: _idpessoas},
  function(result){
      //alert(result);
    $("#divTelefone").html(result);
  }, 'HTML');
}

function adicionaTelefone(){
  $("#pnum_DDD").prop("disabled", true);
  $("#pnum_numero").prop("disabled", true);
  $("#btnAddTelefone").html('<img src="../icones/carregando.gif" width="25px">')
  var _idpessoas = $("#id_cadastro").val();
  if (_idpessoas == "") {
    $("#pnum_DDD").val("");
    $("#pnum_numero").val("");
    return;
  }
  var _pnum_DDD = $("#pnum_DDD").val();
  var _pnum_numero = $("#pnum_numero").val();
  $.post("pessoas_grava.php",
  {operacao: "gravaTelefones", idpessoas: _idpessoas, pnum_DDD: _pnum_DDD, pnum_numero: _pnum_numero},
  function(result){
    $("#pnum_DDD").val("");
    $("#pnum_numero").val("");
    $("#pnum_DDD").prop("disabled", false);
    $("#pnum_numero").prop("disabled", false);
    $("#btnAddTelefone").html('<img src="../icones/adiciona.png" onclick="adicionaTelefone()">')
    buscaTelefones();
    //alert(result);
  });
}
      
function excluirTelefone(idtelefone){
  $("#btnExcluiTelefone_" + idtelefone).html('<center><img src="../icones/carregando_engrenagens.gif" width="25px"><center>')
  $.post("pessoas_grava.php",
  {operacao: "excluiTelefones", idtelefone: idtelefone},
  function(result){
    buscaTelefones();
  });
}