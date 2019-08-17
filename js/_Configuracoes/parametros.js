
      
      $(document).ready(function(){
	      buscaParametros();
        $('[data-toggle="tooltip"]').tooltip();
       });

      function buscaParametros(){
        $("#retParametros").html('<img src="../icones/carregando2.gif" width="25px;">');
        $.post("parametros_grava.php",
              {operacao: "consultaAjax",
                filtro: $("#consulta").val()},
              function(data){
                $("#retParametros").html(data);
              }, "html");
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
              }, "json")
      }

      function gravarParametros(){
        $("[name=btnGrava]").attr("disabled", true);
        $.post("parametros_grava.php",
              {operacao: 'gravar',
              idparametros: $("#idparametros").val(),
              para_valor: $("#para_valor").val(),
              para_obs: $("#para_obs").val()},
              function(data){
                $("[name=btnGrava]").attr("disabled", false);
                $("#para_valor_" + $("#idparametros").val()).html($("#para_valor").val());
                $("#btnFechaModal").click();
              }, 'html');
      }