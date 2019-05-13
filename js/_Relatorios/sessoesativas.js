      $(document).ready(function(){
        chamaRelatorio();
       });

       function chamaRelatorio(){
         $.post( "rel_sessoes_ativas_grava.php", {operacao: "Listar"}, function( data ) {
            $( "#relatorio" ).html( data );
          }, "html");
       }
       
       function mostraSessoes(idusuario, tipo){
         $("#mostra_" + idusuario + '_' + tipo).hide();
          $("#detalhes_" + idusuario + '_' + tipo).show(250);
         $("#esconde_" + idusuario + '_' + tipo).show();
       }

       function escondeSessoes(idusuario, tipo){
         $("#mostra_" + idusuario + '_' + tipo).show();
          $("#detalhes_" + idusuario + '_' + tipo).hide(250);
         $("#esconde_" + idusuario + '_' + tipo).hide();
       }

