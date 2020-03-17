
      
      $(document).ready(function(){
	      buscaParametros();
       });

      function buscaParametros(){
        $("#retParametros").html('<img src="../icones/carregando.gif" width="35px;">');
        $.post("parametros_grava.php",
              {operacao: "consultaAjax",
                filtro: $("#consulta").val()},
              function(data){
                $("#retParametros").html(data);
                $('[data-toggle="tooltip"]').tooltip();
              }, "html");
      }

      function setToolTipo(){
        $('[data-toggle="tooltip"]').tooltip();
      }

      function editaParametros(idparametros){
        $("[name=espera]").show();
        $("[name=conteudo]").hide();
        $("[name=btnGrava]").hide();
        $.post("parametros_grava.php",
              {operacao: "buscaDadosAjax",
                idparametros: idparametros},
              function(data){
                $("[name=espera]").hide();
                $("[name=conteudo]").show();
                $("[name=btnGrava]").show();
                $("#para_nome").val(data.para_nome);
                $("#para_valor").val(data.para_valor);
                $("#para_obs").val(data.para_obs);
                $("#idparametros").val(data.idparametros);
                $("#para_tipo").val(data.para_tipo);
                $("#para_nome_constante").val(data.para_nome_constante);
                if(data.para_tipo == 'parametro'){
                  $("#div_nome_constante").hide();
                }else{
                  $("#div_nome_constante").show();
                }
              }, "json")
      }

      function gravarParametros(){
        $("[name=btnGrava]").attr("disabled", true);
        $.post("parametros_grava.php",
              {operacao: 'gravar',
              idparametros: $("#idparametros").val(),
              para_valor: $("#para_valor").val(),
              para_obs: $("#para_obs").val(),
              para_tipo:  $("#para_tipo").val(),
              para_nome_constante:  $("#para_nome_constante").val()},
              function(data){
                $("[name=btnGrava]").attr("disabled", false);
                var para_valor = $("#para_valor").val();
                if(para_valor == ''){
                  para_valor = '<span class="Obs_claro">*Em Branco*</span>';
                }
                $("#para_valor_" + $("#idparametros").val()).html(para_valor);
                $("#btnFechaModal").click();
              }, 'html');
      }

      function testaTipo(){
        if($("#para_tipo").val() == 'parametro'){
          $("#div_nome_constante").hide();
        }else{
          $("#div_nome_constante").show();
        }
      }