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
			$("#tabs").tabs("select",0);


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
	$(".activeMarcas").each(function(){
		var obj = $(this);
		var id =obj.attr('rel');
		obj.bind("click",function(e){
			url = globals.uri.base+'marcasevinhos/marcas/toggleActive/'+id;
			$.get(url,function(data){
				if (data!='ok'){
					alert("Erro ao actualizar o registo.");
					return false;
				}
			})
		});
	});

    $("#deleteVideo").each(function(){
		var obj = $(this);
		var id =obj.attr('rel');
		obj.bind("click",function(e){
			url = globals.uri.base+'clube1500/revista1500/deleteLinkYoutube/'+id;
			window.location = url;
	
		});
	});


});