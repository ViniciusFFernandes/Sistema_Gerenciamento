      $(document).ready(function(){
        $("#dataInicial").mask("99/99/9999", {placeholder: "__/__/____"})
        $("#dataFinal").mask("99/99/9999", {placeholder: "__/__/____"})
       });

       function testaDados(){
         var dataInicial = $("#dataInicial").val();
         var dataFinal = $("#dataFinal").val();
         var idpessoas = $("#idpessoas").val();
         if (dataInicial == '') {
           alert("Selecione a Data!");
           $("#dataInicial").focus();
           return;
         }
          if (dataFinal == '') {
           alert("Selecione a Data!");
           $("#dataFinal").focus();
           return;
         }
         $.post( "rel_folha_ponto_grava.php", {operacao: "Listar", dataInicial: dataInicial, dataFinal: dataFinal, idpessoas: idpessoas}, function( data ) {
            escondeDiv();
            $( "#relatorio" ).html( data );
          }, "html");
       }

       function escondeDiv(){
         $("#mostra").show();
         $("#divSel").hide(500);
         $("#esconde").hide();
       }

       function mostraDiv(){
         $("#mostra").hide();
         $("#divSel").show(500);
         $("#esconde").show();
       }
