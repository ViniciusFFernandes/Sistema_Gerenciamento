
$(document).ready(function(){
  $("#emp_cnpj").mask("99.999.999/9999-99",
    {translation: {
      '9': {
        pattern: /[0-9]/,
        optional: false
      }
    }
  });
  $("#emp_telefone").mask("(999) 9999-99999",
    {translation: {
      '9': {
        pattern: /[0-9]/,
        optional: false
      }
    }
  });
  $("#emp_cep").mask("99999-999",
    {translation: {
      '9': {
        pattern: /[0-9]/,
        optional: false
      }
    }
  });
});

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#emp_nome").val() == ""){
    $("#emp_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#emp_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}

function attNomeLogo(inputFile){
  $('#spanNomeLogo').text($(inputFile)[0].files[0].name);
}

function enviaLogo(){
  $('#divProgress').show();
  $('#progress').css('width', 0);
  $('#progress').html(0+'%');
  $("#statusEnvio").html("<font color='blue'><b>Enviado imagem...</b></font>")
  //
  $('#formUpload').ajaxSubmit({
    dataType: 'json',
    url: 'empresas_grava.php',
    resetForm: true,
    uploadProgress: function(event, position, total, percentComplete) {
      $('#progress').css('width',percentComplete);
      $('#progress').html(percentComplete+'%');
        
    },        
    success: function(data) {
      console.log(data)
      $('#divProgress').hide();
      //
      $('#progress').css('width','100');
      $('#progress').html('100%');                
      if(data.sucesso == true){
          $('#imgLogo').html('<img src="'+ data.msg +'" class="img-fluid"/>');
          $("#statusEnvio").html("<font color='green'><b>Enviado com sucesso</b></font>")
      }
      else{
        alertaPequeno(data.msg);
        $("#statusEnvio").html("<font color='red'><b>Erro no envio</b></font>")
      }                
    },
    error : function(){
      $('#divProgress').hide();
      alertaPequeno('Erro ao enviar logo!');
      $("#statusEnvio").html("<font color='red'><b>Erro no envio</b></font>")
    }
  });
}

function limpaStatus(){
  $("#statusEnvio").html("")
}