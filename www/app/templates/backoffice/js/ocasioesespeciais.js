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
					element.parent().after(error);
					break;
				default:
					element.after(error);
					break;
			}

		},
		highlight: function(element, errorClass) {
			$(element).addClass('ui-state-error')
		},
		unhighlight: function(element, errorClass) {
			$(element).removeClass('ui-state-error')
		}

	};

	form_add.validate(validate_options);
	form_edit.validate(validate_options);



	/*


	// Garantir que os checkboxes têm sempre um valor (1 ou 0) consoante o seu estado (checked, unchecked)
	$('input[type=checkbox]').bind('change',function(){
		var new_state = $(this).attr('checked');
		$(this).val((new_state == true) ? '1' : '0' );
	}).bind('changedvalue',function(){
		var new_value = $(this).val();
		$(this).attr('checked',(new_value== '1') ? true : false );
	});

	*/
	

});

//Busca a lista das receitas conforme a o momento seleccionado 
function getReceitas() {
	var id_momento = $("select[name=momento]").val();
	// Bloqueia e limpa todos os selects de receitas & vinhos
	$("select.selPrato").attr('disabled', 'disabled').html("");
	$("select.selVinho").attr('disabled', 'disabled').html("");
	// Busca as receitas de cada tipo de prato
	$("select.selPrato").each(function(){
		var id_prato = $(this).attr('rel');
		var url = baseuri + "backoffice/receitasecocktails/ocasioesespeciais/getReceitasAjax/"+id_momento+"/"+id_prato;
		$.get(url, function(resposta){
			// Aplica a lista no respectivo select
			with ($("select.selPrato[rel="+ id_prato + "]")) {
				html('<option value="0">Não escolher</option>'+resposta);
				if (resposta.length > 0) {
					removeAttr('disabled');
				}
				var id_receita = val();
			}
			// Se estiver a editar, deixa o item da receita com selected
			if (typeof(objMenu) == "object") {
				if ((typeof(objMenu[id_prato]) == "object") && (objMenu[id_prato] != null)) {
					var prato = objMenu[id_prato];
					if (prato.receitas_id != null) {
						$("select.selPrato[rel="+ id_prato +"] option[value=" + prato.receitas_id + "]").attr("selected", "selected"); // Selecciona o prato
						// Actualiza a receita para buscar os vinhos da seleccionada
						id_receita = objMenu[id_prato].receitas_id  
						// uma vez que os itens iniciais foram exibidos, apaga a var do item no menu.
						objMenu[id_prato].receitas_id = null;
					}
				}
			}
			// Busca o vinho
			if (id_receita != "0") {
				var url2 = baseuri + "backoffice/receitasecocktails/ocasioesespeciais/getVinhosAjax/"+id_receita;
				$.get(url2, function(resposta){
					with ($("select.selVinho[rel="+ id_prato + "]")) {
						html(resposta);
						if (resposta.length > 0) {
							removeAttr('disabled');
						}
					}
					// Se estiver a editar, deixa o item do vinho com selected
					if (typeof(objMenu) == "object") {
						if ((typeof(objMenu[id_prato]) == "object") && (objMenu[id_prato] != null)) {
							var prato = objMenu[id_prato];
							if (prato.vinhos_id != null) {
								$("select.selVinho[rel="+ id_prato +"] option[value=" + prato.vinhos_id + "]").attr("selected", "selected"); // Selecciona o vinho
								// uma vez que os itens iniciais foram exibidos, apaga a var do item no menu.
								objMenu[id_prato].vinhos_id = null;
								objMenu[id_prato] = null;
							}
						}
					}
				});
			}
		});
	});
}

function getVinhos(id_prato) {
	$("select.selVinho[rel="+ id_prato + "]").attr("disabled", "disabled").html("");
	id_receita = $("select.selPrato[rel="+id_prato+"]").val();
	var url = baseuri + "backoffice/receitasecocktails/ocasioesespeciais/getVinhosAjax/"+id_receita;
	$.get(url, function(resposta){
		with ($("select.selVinho[rel="+ id_prato + "]")) {
			html(resposta);
			if (resposta.length > 0) {
				removeAttr('disabled');
			}
		}
	});
}
