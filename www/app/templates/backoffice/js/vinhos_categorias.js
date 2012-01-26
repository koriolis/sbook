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

	

	
});