
    function atualizarSistema(){
      $("#btnAtualizar").html("Atualizando sistema, aguarde... <img src='../icones/carregando2.gif' width='12px;'>");
      $("#relatorioAtualizacaoTitulo").show();
      $("#relatorioAtualizacao").show();
      $.post("versao_atualizacao_grava.php", 
            {operacao: 'atualizarSistema'},
            function(data){
              //console.log(data);
              $("#versaoAtualSistema").html(data.novaVersao);
              $("#relatorioAtualizacao").prepend('<div class="row"><div class="col-md-12 col-sm-12 col-xs-12"><b>Versão: ' + data.novaVersao + '</b><br><p>' + data.msg + '</p></div></div>');
              if(data.executaNovamente){
                atualizarSistema();
              }else{
                $("#btnAtualizar").html("Sistema atualizado com sucesso!");
              }
            }, 'json')
    }