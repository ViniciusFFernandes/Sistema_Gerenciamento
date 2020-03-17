
    function atualizarSistema(){
      $("#btnAtualizar").html("Atualizando sistema, aguarde... <img src='../icones/carregando_engrenagens.gif' width='25px;'>");
      $("#relatorioAtualizacaoTitulo").show();
      $("#relatorioAtualizacao").show();
      $.post("versao_atualizacao_grava.php", 
            {operacao: 'atualizarSistema'},
            function(data){
              //console.log(data);
              if(data.executado){
                $("#versaoAtualSistema").html(data.novaVersao);
              }
              $("#relatorioAtualizacao").prepend('<div class="row"><div class="col-md-12 col-sm-12 col-xs-12"><b>Versão: ' + parseFloat(data.novaVersao) + '</b><br><p>' + data.msg + '</p></div></div>');
              if(data.executaNovamente){
                atualizarSistema();
              }else{
                if(data.executado){
                  $("#btnAtualizar").html("Sistema atualizado com sucesso!");
                }else{
                  $("#btnAtualizar").html("Erro ao executar a versão " + data.novaVersao + ", verifique o erro e tente novamente!");
                }
               
              }
            }, 'json')
    }

    function abrirHistoricoAtt(){
      $("#conteudoHistoricoAtt").html("Buscando histórico, aguarde... <img src='../icones/carregando.gif' width='25px;'>");
      $.post("versao_atualizacao_grava.php",
              {operacao: 'buscarHistorico'},
              function(data){
                $("#conteudoHistoricoAtt").html(data);
              }, "html");
    }

    function baixarAtualizacao(){
      $("#conteudoAtualizacao").html("Porcurando por atualizações, aguarde... <img src='../icones/carregando_engrenagens.gif' width='25px;'><br><span class='Obs_claro'>Está operação pode levar alguns minutos</span><br>");
      $.post("versao_atualizacao_grava.php",
              {operacao: 'baixarAtualizacoes'},
              function(data){
                $("#conteudoAtualizacao").html(data);
              }, "html");
    }

    function enviarAtualizacao(idusuario){
      if(idusuario != 1){
        alertaPequeno("Você não tem permissão para executar esta ação!", '', 'tada');
        return;
      }
      $("#conteudoAtualizacao").html("Gerando nova versão, aguarde... <img src='../icones/carregando_engrenagens.gif' width='25px;'><br><span class='Obs_claro'>Está operação pode levar alguns minutos</span><br>");
      $.post("versao_atualizacao_grava.php",
              {operacao: 'gerarAtualizacoes'},
              function(data){
                $("#conteudoAtualizacao").html(data);
              }, "html");
    }