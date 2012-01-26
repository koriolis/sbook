$(function(){

	var form_add	= $('form[name=add]');
	var form_edit	= $('form[name=edit]');

	// Adicionar path de submit ao formulário removendo
	// a action para obtermos o path principal
	//
	form_add.attr('action',globals.url.toString());
	form_edit.attr('action',globals.url.toString());
	
	// FX highlight de cada um dos campos quando tem foco
	//
	$('form[name=edit] .input, form[name=add] .input').bind('focus',function(){

		$(this).addClass('ui-state-highlight');

	}).bind('blur',function(){

		$(this).removeClass('ui-state-highlight');
		
	});

	// Para que se utilize o botão de submit
	//
	$('#submitForm').bind('click',function(event){
		event.preventDefault();
		event.stopPropagation();
		$(this).parents('FORM').submit();
	});

		
	var validate_options = {
		errorClass: 'ui-state-error-text',
		errorPlacement: function(error, element) {
			
			switch(element.context.type)
			{
				case 'textarea':
					console.log(element);
					element.after(error);
					break;
				default:
					element.after(error);
					break;
			}
			
		},
		highlight: function(element, errorClass) {
			$(element).addClass('ui-state-error')
			$("#tabs").tabs("select",0);
			
			
		},
		unhighlight: function(element, errorClass) {
			$(element).removeClass('ui-state-error')
		}

	};

	form_add.validate(validate_options);
	form_edit.validate(validate_options);

	
	// Editor WYSIWYG
	//new $.cleditor("corpo");
	if ($("#corpo").size() > 0) {
		CKEDITOR.replace('corpo', {
			filebrowserUploadUrl : globals.uri.base + "noticias/uploadImgTexto"
		});
	}
	// Tabs
	if($("#tabs").size()>0) $("#tabs").tabs();
	
	
	// Imagens
	$("#addImagem").bind('click',function(event){
		event.preventDefault();
		event.stopPropagation();
		campoDaImagem = $(this).prev('input');
		imagem = campoDaImagem.val();
		if (imagem!=''){
			var extensoes=new Array("jpg","jpeg"); 
			var extArr = imagem.split('.');
			extensaoDaImagem =  extArr[extArr.length-1];
			extensaoPermitida = false;
			for (x in extensoes)
			{
				if (extensoes[x].toUpperCase()==extensaoDaImagem.toUpperCase()){
					extensaoPermitida = true;
				}
			}
			if (extensaoPermitida == false){
				alert('Só são permitidas as seguintes imagens '+extensoes+'. O ficheiro escolhido foi '+imagem);
				return false;
			}
			//
			campoDaImagem.attr('rel',imagem )
			novoCampoImagem = campoDaImagem.clone(true).appendTo('#escondido');
			novoCampoImagem.attr("name","imagensNoticiasEscolhido[]");
			htmlNovoCampo = '<input Type="file" name="imagensNoticias[]" id="imagensNoticias[]">';
			campoDaImagem.after(htmlNovoCampo);
			campoDaImagem.remove();
			htmlTabela = '<tr rel="'+imagem+'"><td>'+imagem+'</td><td><a class="removeImagem" rel="'+imagem+'" href="#">remover</a></td></tr>'
			$("#imagens-listagem").append(htmlTabela);
			$('.noResults').remove();
			// Adiciona 1 a contagem de ficheiros adicionados
			var no_imagens = $("input[name=no_imagens]").val();
			no_imagens++;
			$("input[name=no_imagens]").val(no_imagens);
		}
	})
	$(".removeImagem").live("click",function(event){ 
		event.preventDefault();
		event.stopPropagation();
		imagem = $(this).attr('rel');
		campoARetirar = $("input[rel='"+imagem+"']");
		linhaARetirar = $("tr[rel='"+imagem+"']");
		campoARetirar.remove();
		linhaARetirar.remove();
		// Reduz em 1 a contagem de ficheiros adicionados
		var no_imagens = $("input[name=no_imagens]").val();
		if (no_imagens > 0) no_imagens--;
		$("input[name=no_imagens]").val(no_imagens);
		return false;
	})
	
	$(".removeMediaEdit").live("click",function(event){ 
		event.preventDefault();
		event.stopPropagation();
		
		if (confirm("Tem certeza de que quer apagar a imagem?")) {
		
			// Verifica o n.o de imagens actualmente
			var no_imagens = $("input[name=no_imagens]").val();
			
			// Se for a última não permite que a mesma seja apagada
			if (no_imagens == 1) {
				$("<div title='Erro!'>A imagem que está a tentar remover é a única associada à notícia, terá de acrescentar outra primeiro.</div>").dialog({
					modal: true,
					resizable: false,
					buttons: {
						'Ok': function(){
							$(this).dialog('close');						// - Fecha a janela de diálogo
						}
					}
				});
				return false;
			}
			
			// Reduz em 1 a contagem de ficheiros adicionados
			if (no_imagens > 0) no_imagens--;
			$("input[name=no_imagens]").val(no_imagens);
					
			imagem = $(this).attr('rel');
			$.ajax({
				url: globals.uri.base+'noticias/retira_imagem/'+imagem,
				success: function(data) {
					if (data=='ok'){
						linhaARetirar = $("tr[rel='"+imagem+"']");
						linhaARetirar.remove();
						return false;
					}
				}
			});
		
		}
	})

});