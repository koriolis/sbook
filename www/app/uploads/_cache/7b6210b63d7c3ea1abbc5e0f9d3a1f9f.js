$(function(){




	$("#enviaForm").bind("click",function(e){
		var nome = $("#inputNome").val();
		var email = $("#inputEmail").val();
		var empresa = $("#inputEmpresa").val();
		var morada = $("#inputMorada").val();
		var cp1 = $("#inputCodPostal").val();
		var cp2 = $("#inputCodPostal2").val();
		var localidade = $("#inputLocalidade").val();
		var pais = $("#inputPais").val();
		var assunto = $("#inputAssunto").val();
		var mensagem = $("#inputMensagem").val();
		var regEmail = /^([a-z0-9_-]+[.])*[a-z0-9_-]+@([a-z0-9-]+[.])*[a-z0-9-]{2,}[.][a-z]{2,6}$/;
		var erros = "";
		if (nome ==''){
			erros += "Por favor preencha o seu nome.<br>";
		}
		if (email ==''){
			erros += "Por favor preencha o seu email.<br>";
		}else{
			if (regEmail.exec(email)==null ){
				erros += "Por favor preencha um email válido.<br>";
			}
		}
		if (assunto ==''){
			erros += "Por favor preencha o assunto.<br>";
		}
		if (mensagem ==''){
			erros += "Por favor preencha o mensagem.<br>";
		}
		
		if (erros !=''){
			e.preventDefault();
			e.stopPropagation();
			alerta(erros);
			return false;
		}
		return true;
		
	
	})
	
	

});