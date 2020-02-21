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

       function validarLogin(){
         var _idpessoas = $("#id_cadastro").val();
         var _pess_usuario = $("#pess_usuario").val();
         var _pess_senha = $("#pess_senha").val();
         var _verificacaoRetorno = $("#verificacaoRetorno").val();


        if(_pess_senha == ""){
            alertaPequeno('Não é permitido gravar a senha em branco!');
            return;
        }

        if(_pess_usuario == ""){
          alertaPequeno('Não é permitido gravar o nome em branco!');
          return;
      }

        if ((_pess_senha.length) < 4) {
          alertaPequeno("Sua senha deve possuir mais de 4 digitos!")
          return;
        }

        if (_verificacaoRetorno == "Aprovado") {
          gravarUser(_idpessoas, _pess_usuario, _pess_senha);
        }else{
          alertaPequeno("Corrija os campos indicados em vermelho!");
        }


       }

       function verificaUser(){
         //alert("aosifnsdgoa");
         var _idpessoas = $("#id_cadastro").val();
         var _pess_usuario = $("#pess_usuario").val();
         var _pess_senha = $("#pess_senha").val();

         $.post("pessoas_grava.php",
         {operacao: "validaUsuario", idpessoas: _idpessoas, pess_usuario: _pess_usuario},
         function(result){
          //alert(result);
          //return;
           if(result.existe == 'true'){
             console.log("Nome de usuario ja cadastrado!");
             $("#pess_usuario").css({"background-color":"#F78181", "border-color": "#FE2E2E"});
             $("#verificacaoRetorno").val("Cancelar");
             return;
           }else{
             $("#pess_usuario").css({"background-color":"#81F781", "border-color": "#2EFE2E"});
             $("#verificacaoRetorno").val("Aprovado");
           }
         }, "json");
       }

       function gravarUser(idpessoas, pess_usuario, pess_senha){
         $.post("pessoas_grava.php",
          {operacao: "gravaLogin", idpessoas: idpessoas, pess_usuario: pess_usuario, pess_senha: pess_senha},
         function(result){
           $("#fechaAlteraLogin").click();
           $("#pess_senha").val("");
         });
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
        chamaGravar('gravar');
      }

      function buscaTelefones(){
        // $("#divTelefone").html('<center><img src="../icones/carregando2.gif" width="15px"><center>')
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
        $("#btnAddTelefone").html('<img src="../icones/carregando2.gif" width="15px">')
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
        $("#btnExcluiTelefone_" + idtelefone).html('<center><img src="../icones/carregando2.gif" width="15px"><center>')
        $.post("pessoas_grava.php",
        {operacao: "excluiTelefones", idtelefone: idtelefone},
        function(result){
          buscaTelefones();
        });
      }