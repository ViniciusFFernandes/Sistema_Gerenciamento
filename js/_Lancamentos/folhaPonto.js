
      window.setTimeout(function(){
         document.getElementById("botao_alerta").click();
      }, 6000);

      $(document).ready(function(){
        $("#fopo_horario").mask("99/99/9999 99:99", {placeholder: "__/__/____ __:__"})
       });

       function testaDados(){
         var idpessoas = $("#idpessoas").val();
         var data = $("#fopo_horario").val();
         //alert(idpessoas);
         if (idpessoas <= 0) {
           alert("Selecione um funcionario!");
           $("#idpessoas").focus();
           return;
         }
         if (data == '') {
           alert("Selecione o horario!");
           $("#fopo_horario").focus();
           return;
         }
         $("#lancar_ponto").submit();
       }

       function setaDataHora(){
         var data = new Date(),
             dia = data.getDate(),
             mes = data.getMonth() + 1,
             ano = data.getFullYear(),
             hora = data.getHours(),
             minutos = data.getMinutes();
        //
        var dataFormatada = [dia, mes, ano].join('/') + ' ' + [hora, minutos].join(':');
        //
        $("#fopo_horario").val(dataFormatada);
       }
