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

	$('#deleteImage').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		var bot = $(this);

		// Remove a action 'editar' do path e constrói uma array
		//
		var url		= globals.url.attr('source').split('/editar/').join('/apagarImagem/')

		$.get(url,function(result){
			if(result == 'ok'){

				$("#thumbImage").nextAll('br').remove().end().remove(); // Remove todos os BR seguintes e remove-se a si mesmo
				bot.prev('br').remove().end().remove();	// remove o br anterior e remove-se a si mesmo

				$("<div title='Sucesso!'>A imagem foi apagada com sucesso</div>").dialog({
					modal: true,
					resizable: false,
					buttons: {
						'Ok': function(){
							$(this).dialog('close');						// - Fecha a janela de diálogo
						}
					}
				});

			}
		});
	});

	$('#deleteMapa').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		var bot = $(this);

		// Remove a action 'editar' do path e constrói uma array
		//
		var url		= globals.url.attr('source').split('/editar/').join('/apagarMapa/')

		$.get(url,function(result){
			if(result == 'ok'){

				$("#thumbMapa").nextAll('br').remove().end().remove(); // Remove todos os BR seguintes e remove-se a si mesmo
				bot.prev('br').remove().end().remove();	// remove o br anterior e remove-se a si mesmo

				$("<div title='Sucesso!'>O mapa foi apagada com sucesso</div>").dialog({
					modal: true,
					resizable: false,
					buttons: {
						'Ok': function(){
							$(this).dialog('close');						// - Fecha a janela de diálogo
						}
					}
				});

			}
		});
	});

});
