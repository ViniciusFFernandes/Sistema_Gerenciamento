
      window.setTimeout(function(){
         document.getElementById("botao_alerta").click();
      }, 6000);

      $(document).ready(function(){
	      buscaTelefones();
        $("#pess_cpf").mask("999.999.999-99");
        $("#pess_rg").mask("99.999.999-9");
        $("#pess_cnpj").mask("99.999.999/9999-99");
        $("#pnum_numero").mask("9999-99999");
        $("#pnum_DDD").mask(" 999");
        $("#pess_cep").mask("99999-999");
       });

       function validarLogin(){
         var _idpessoas = $("#idpessoas").val();
         var _pess_usuario = $("#pess_usuario").val();
         var _pess_senha = $("#pess_senha").val();
         var _verificacaoRetorno = $("#verificacaoRetorno").val();


        if(_pess_senha == ""){
            alert('Não é permitido gravar a senha em branco!');
            return;
        }

        if ((_pess_senha.length) < 4) {
          alert("Sua senha deve possuir mais de 4 digitos!")
          return;
        }

        if (_verificacaoRetorno == "Aprovado") {
          gravarUser(_idpessoas, _pess_usuario, _pess_senha);
        }else{
          alert("Corrija os campos indicados em vermelho!");
        }


       }

       function verificaUser(){
         //alert("aosifnsdgoa");
         var _idpessoas = $("#idpessoas").val();
         var _pess_usuario = $("#pess_usuario").val();
         var _pess_senha = $("#pess_senha").val();

         $.post("cadastro_pessoas_grava.php",
         {operacao: "validaUsuario", idpessoas: _idpessoas, pess_usuario: _pess_usuario},
         function(result){
          //alert(result);
          //return;
           if(result.existe == 'true'){
             //alert("Nome de usuario ja cadastrado!");
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
         $.post("cadastro_pessoas_grava.php",
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
          $("#pess_nome").css("border-color", "red");
          return;
        }else{
          $("#pess_nome").css("border-color", "green");
        }
        $("#operacao").val('pess_gravar')
        $("#cadastro_pessoas").submit();
      }

      function novaPessoa(){
        $("#operacao").val('novoCadastro')
        $("#cadastro_pessoas").submit();
      }

      function excluiCadastro(){
        var result = confirm("Não é indicado excluir um usuario!\n\nDeseja excluir este cadastro?");
        if (result) {
          $("#operacao").val('excluiCad')
          $("#cadastro_pessoas").submit();
        }
      }

      function buscaPessoas(){
        var _pesquisa = $("#pesquisa").val();
        $.post("cadastro_pessoas_grava.php",
        {operacao: "buscaPessoas", pesquisa: _pesquisa},
        function(result){
          $("#pesquisa").val("");
          $("#resultBusca").html(result);
        }, 'HTML');
      }

      function buscaTelefones(){
        var _idpessoas = $("#idpessoas").val();
        $.post("cadastro_pessoas_grava.php",
        {operacao: "buscaTelefones", idpessoas: _idpessoas},
        function(result){
            //alert(result);
          $("#divTelefone").html(result);
        }, 'HTML');
      }

      function adicionaTelefone(){
        var _idpessoas = $("#idpessoas").val();
        if (_idpessoas == "") {
          return;
        }
        var _pnum_DDD = $("#pnum_DDD").val();
        var _pnum_numero = $("#pnum_numero").val();
        $.post("cadastro_pessoas_grava.php",
        {operacao: "gravaTelefones", idpessoas: _idpessoas, pnum_DDD: _pnum_DDD, pnum_numero: _pnum_numero},
        function(result){
          $("#pnum_DDD").val("");
          $("#pnum_numero").val("");
          buscaTelefones();
          //alert(result);
        });
      }

      function zeraBusca(){
        $("#resultBusca").html("");
      }

      function abrePessoa(id){
        var siteRetorno = 'cadastro_pessoas.php?idpessoas=' + id;
        $(location).attr('href', siteRetorno);
      }

      function excluirTelefone(idtelefone){
        $.post("cadastro_pessoas_grava.php",
        {operacao: "excluiTelefones", idtelefone: idtelefone},
        function(result){
          buscaTelefones();
        });
      }