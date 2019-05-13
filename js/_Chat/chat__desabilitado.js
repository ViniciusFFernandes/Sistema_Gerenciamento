    var attChat = true;
    var qteMsg = 0;
    var attPosicao = true;

    $(document).keypress(function(e) {
        if(e.which == 13) $('#clickEnvia').click();
    });

     function abreFechaChat(operacao){
         if(operacao == 'F'){
            $('#divChatPrincipal').show(250);
            $('#chatPrincipalUsers').hide(250);
         }
         if(operacao == 'A'){
            $('#divChatPrincipal').hide(250);
            $('#chatPrincipalUsers').show(250);
         }
     }

     function abreConversa(idRemet, idDest){
          $.post("../_Chat/chat_grava.php",
                {operacao: 'abrirConversa', idDest: idDest},
                function(data){
                  $("#chatPrincipalUsers").html(data);
                  $("#textMsg").focus();
                  listaMsg(idRemet, idDest, true);
                }, "HTML");
          attChat = false;
     }

     function voltarChat(){
       attChat = true;
       qteMsg = 0;
       $.post("../_Chat/chat_grava.php",
             {operacao: 'abrirLista'},
             function(data){
               $("#chatPrincipalUsers").html(data);
               atualizaChat();
             }, "HTML");

     }

     function enviaMsg(remetente, destinatario){
       var msg = $("#textMsg").val();
       if(msg.replace(" ","") == ''){
           return;
       }
       $.post("../_Chat/chat_grava.php",
             {operacao: 'gravaMsg', remetente: remetente, destinatario: destinatario, msg: msg},
             function(data){
               $("#textMsg").val('');
               listaMsgEnvio(remetente, destinatario);
             });
     }

     function listaMsg(remetente, destinatario, tudo){
         $.post("../_Chat/chat_grava.php",
               {operacao: 'listaMsg', remetente: remetente, destinatario: destinatario, tudo: tudo},
               function(data){
                    $("#mensagens").append(data);
                    posicaoInicial();
                    setTimeout("listaMsgDest(" + remetente + ", " + destinatario + ")",1000);
               });
     } 
     function listaMsgDest(remetente, destinatario){
         $.post("../_Chat/chat_grava.php",
               {operacao: 'listaMsgDest', remetente: remetente, destinatario: destinatario},
               function(data){
                   $("#mensagens").bind('scroll', function() {
                        if($(this).scrollTop() + $(this).innerHeight() < this.scrollHeight) {
                           attPosicao = false;
                        }else{
                            attPosicao = true;
                        }
                    }); 
                    $("#mensagens").append(data);
                    if(attPosicao && data != ''){
                         posicaoInicial();
                    }
                    
                 if (!attChat){
                   setTimeout("listaMsgDest(" + remetente + ", " + destinatario + ")",1000);
                 }
               }, 'HTML');
     }
     function listaMsgEnvio(remetente, destinatario){
         $.post("../_Chat/chat_grava.php",
               {operacao: 'listaMsgEnvio', remetente: remetente, destinatario: destinatario},
               function(data){
                 $("#mensagens").append(data);
                 posicaoInicial();
               }, 'HTML');
     }
     function posicaoInicial(){
         $("#mensagens").prop("scrollTop", $("#mensagens").prop("scrollHeight"));
     }
