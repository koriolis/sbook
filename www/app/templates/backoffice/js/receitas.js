function setFormSubReceita() {
	$(":not(.inSubReceita)", "div#tabs-1").hide(); // Campos do tab receitas que não pertecem a subreceita
	$("div#tabs-2, #lnkTabs2").hide(); // Demais campos podem ser escondidos pela tab
	$("div#tabs-3, #lnkTabs3").hide();
	$("div#tabs-5, #lnkTabs5").hide();
	$("div#tabs-6, #lnkTabs6").hide();
	$("input[name='tempo']").removeClass("required digits");
	$("input[name='pessoas']").removeClass("required digits");
}
function setFormReceita() {
	$(":not(.inSubReceita)", "div#tabs-1").show(); // Campos do tab receitas que não pertecem a subreceita
	$("div#tabs-2, #lnkTabs2").show();
	$("div#tabs-3, #lnkTabs3").show();
	$("div#tabs-5, #lnkTabs5").show();
	$("div#tabs-6, #lnkTabs6").show();
	$("input[name='tempo']").addClass("required digits");
	$("input[name='pessoas']").addClass("required digits");
}
function randomString(length) {
    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
    
    if (! length) {
        length = Math.floor(Math.random() * chars.length);
    }
    
    var str = '';
    for (var i = 0; i < length; i++) {
        str += chars[Math.floor(Math.random() * chars.length)];
    }
    return str;
}

$(function(){

	var form_add			= $('form[name=add]');
	var form_edit			= $('form[name=edit]');
	var form_prato			= $("#frmAdcPrato");
	var form_cozinha		= $("#frmAdcCozinha");
	var form_cozinhado		= $("#frmAdcCozinhado");
	var form_momento		= $("#frmAdcMomento");
	var form_ingrediente	= $("#frmAdcIngrediente");
	var form_subreceita		= $("#frmAdcSubreceita");
	var form_tipo_cocktail	= $("#frmAdcTipoCocktail");


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



	$.validator.setDefaults({
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

	});


	var validate_subform_options = {
		submitHandler: function(form){
			var select = $(".submitSubForm", $(form)).attr("rel");
			var descricao = $("input[name=descricao]", $(form)).val();
			//$.ajaxSetup({type: 'POST'});
			existentes = $('select[name='+select+']').children('[text=' + descricao + ']').length;
			if (existentes > 0) {
				alert("O item já se encontra na lista");
			} else {
				$.post($(form).attr("action"), $(form).serializeArray(), function(novoItem){
					var novoitemjson = $.parseJSON(novoItem);
				//	console.log();

					$("select[name="+select+"]").addOption(novoitemjson, true);
					$(form).parents(".subFormDialog").dialog("close");
				});
			}
		}

	};

	var validate_receita_options = {
		highlight: function(element, errorClass) {
			$(element).addClass('ui-state-error');
			$("#tabs").tabs('select', 0);
		},
		submitHandler: function(form){
			// Remove da lista os passos vazios
			$("#tabs").tabs('select', "#tabs-2");
			$("div.itemPasso textarea[name^=passos]").each(function() {
				var tamPasso = $(this).val().length;
				if (tamPasso < 1) {
					var divRemover = $(this).parents("div.itemPasso");
					divRemover.remove();
				}
			});
			// Se apagou todos os passos cria um novo vazio
			if ($(".itemPasso").size() < 1) {
				$("#addPasso").trigger("click");
			}
			// O primeiro passo não deve ter botão de remover (caso tenho sido trocado por ter sido deixado vazio)
			$(".itemPasso").first().children("a.btnRemovePasso").remove();
			// Valida o form
			var isSubreceita = $("input[name=subreceita]").attr("checked");
			if (($("textarea[name=passos\\[\\]]").val().length < 1) && (!isSubreceita)) { // Verifica se adicionou passos
				$("#tabs").tabs('select', "#tabs-2");
				$('#listaPassos').next('label').remove().end().after('<label class="ui-state-error-text">Tem de adicionar ao menos um passo</label>');
				return false;
			} else if ($("input[name^=ingredientes]", $('#ingredientes-listagem')).size() < 1) { // Verifica se adicionou ingredientes (size de ingredientes[] > 0)
				$("#tabs").tabs('select', "#tabs-4");
				$('#ingredientes-listagem').parent().next('label').remove().end().after('<label class="ui-state-error-text">Tem de adicionar ao menos um ingrediente</label>');
				return false;
			} else if (($("input[name^=vinhos]", $('#vinhos-listagem')).size() < 1) && (!isSubreceita)) { // Verifica se adicionou vinhos (size de ingredientes[] > 0)
				$("#tabs").tabs('select', "#tabs-6");
				$('#vinhos-listagem').parent().next('label').remove().end().after('<label class="ui-state-error-text">Tem de adicionar ao menos um vinho</label>');
				return false;
			} else {
				form.submit();
			}
			form.submit();
		}
	};

	form_prato.validate(validate_subform_options);
	form_cozinha.validate(validate_subform_options);
	form_cozinhado.validate(validate_subform_options);
	form_momento.validate(validate_subform_options);
	form_ingrediente.validate(validate_subform_options);
	form_subreceita.validate(validate_subform_options);
	form_tipo_cocktail.validate(validate_subform_options);


	form_add.validate(validate_receita_options);
	form_edit.validate(validate_receita_options);

	// Para que se utilize o botão de submit
	$('#submitForm, .submitSubForm').bind('click',function(event){
		event.preventDefault();
		event.stopPropagation();
		$(this).parents('FORM').submit();
	});


	$(".btnRemovePasso").live("click", function(e){
		e.stopPropagation();
		e.preventDefault();
		 $(this).parent().remove();
	});

	$("#addPasso").bind("click", function(e){
		// Monta o html
		var id = randomString(10);
		var html = "<div class=\"itemPasso\">\n" +
			"<label>Passo</label>\n" +
			"<textarea name=\"passos[]\" class=\"input\" id=\"" + id + "\"></textarea>\n" +
			botRemovePasso +
			"<br><br><br></div>";
		
		// Insere no div dos passos
		$("#listaPassos").append(html);
		// Aplica o resizable no novo textBox
		$("textarea",  $(".itemPasso").last()).resizable({
			handles: 's',
			minHeight: 68
		});
		// Editor wysiwyg
		new $.cleditor(id); 
	});

	$("#addPrato").bind("click", function(event) {
		event.stopPropagation();
		event.preventDefault();
		$("#novoPrato").dialog("open");
	});
	$("#addCozinha").bind("click", function(event) {
		event.stopPropagation();
		event.preventDefault();
		$("#novaCozinha").dialog("open");
	});
	$("#addCozinhado").bind("click", function(event) {
		event.stopPropagation();
		event.preventDefault();
		$("#novoCozinhado").dialog("open");
	});
	$("#addMomento").bind("click", function(event) {
		event.stopPropagation();
		event.preventDefault();
		$("#novoMomento").dialog("open");
	});
	$("#SubreceitaAddNovoIngrediente").live("click", function(event) {
		event.stopPropagation();
		event.preventDefault();
		$("#novoIngrediente").dialog("open");
	});
	$("#addNovoIngrediente").bind("click", function(event) {
		event.stopPropagation();
		event.preventDefault();
		$("#novoIngrediente").dialog("open");
	});
	$("#addNovaSubreceita").bind("click", function(event) {
		event.stopPropagation();
		event.preventDefault();
		$("#novaSubreceita").dialog("open");
	});
	$("#addTipoCocktail").bind("click", function(event) {
		event.stopPropagation();
		event.preventDefault();
		$("#novoTipoCocktail").dialog("open");
	});

	$('a.deleteIngrediente').live('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		$('tr.noResults').remove();
		var noResultsTemplate = '<tr class="noResults"><td colspan="3">Ainda não adicionou ingredientes a esta Receita</td></tr>';
		var bot		= $(this);

		var numRows = bot.parents('table.listagem tbody').children('tr').size();
		var tBody	= bot.parents('tbody');

		if(numRows > 1) {
			bot.parents('tr').remove();

		} else if(numRows == 1){
			bot.parents('tr').remove();
			tBody.append(noResultsTemplate);

		}
		// Botões de up&down
		$('a.moveIngredienteUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
		$('a.moveIngredienteDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");

	});
	$('a.subreceitaDeleteIngrediente').live('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		$('tr.noResults').remove();
		var noResultsTemplate = '<tr class="noResults"><td colspan="3">Ainda não adicionou ingredientes a esta Sub Receita</td></tr>';
		var bot		= $(this);

		var numRows = bot.parents('table.listagem tbody').children('tr').size();
		var tBody	= bot.parents('tbody');

		if(numRows > 1) {
			bot.parents('tr').remove();

		} else if(numRows == 1){
			bot.parents('tr').remove();
			tBody.append(noResultsTemplate);

		}
		// Botões de up&down
		$('a.moveSubreceitaIngredienteUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
		$('a.moveSubreceitaIngredienteDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");

	});
	$('a.deleteSubreceita').live('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		$('tr.noResults').remove();
		var noResultsTemplate = '<tr class="noResults"><td colspan="2">Ainda não adicionou sub receitas a esta Receita</td></tr>';
		var bot		= $(this);

		var numRows = bot.parents('table.listagem tbody').children('tr').size();
		var tBody	= bot.parents('tbody');

		if(numRows > 1) {
			bot.parents('tr').remove();

		} else if(numRows == 1){
			bot.parents('tr').remove();
			tBody.append(noResultsTemplate);

		}
		// Botões de up&down
		$('a.moveSubreceitaUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
		$('a.moveSubreceitaDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");

	});

	$('a.deleteVinho').live('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		$('tr.noResults').remove();
		var noResultsTemplate = '<tr class="noResults"><td colspan="2">Ainda não adicionou vinhos a esta Receita</td></tr>';
		var bot		= $(this);

		var numRows = bot.parents('table.listagem tbody').children('tr').size();
		var tBody	= bot.parents('tbody');

		if(numRows > 1) {
			bot.parents('tr').remove();

		} else if(numRows == 1){
			bot.parents('tr').remove();
			tBody.append(noResultsTemplate);

		}
		// Botões de up&down
		$('a.moveVinhoUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
		$('a.moveVinhoDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
	});

	$('#addIngrediente').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		var bot = $(this);

		var errorTemplate	= '<label class="ui-state-error-text">{msg}</label>';

		var newRow = function(ingrediente, descricao, quantidade){
			var newRowTemplate			= '<tr class="alternate"><td>'+botIngredienteUp+'<br>'+ botIngredienteDown + '</td><td>{descricao}<input type="hidden" name="ingredientes[]" value="{ingrediente}"><ul class="row-actions"><li class="ui-corner-all"><a href="#" class="deleteIngrediente">Remover</a></li></ul></td><td>{quantidade}<input type="hidden" name="quantidades[]" value="{quantidade}"></td></tr>';

			return newRowTemplate
						.split('{ingrediente}').join(ingrediente)
						.split('{descricao}').join(descricao)
						.split('{quantidade}').join(quantidade);

		}

		var errorContent	= function(msg){
			return errorTemplate.split('{msg}').join(msg);
		}
		var removeError = function(element){
			element.removeClass('ui-state-error').nextAll('label.ui-state-error-text').first().remove();
		}
		var removeAllErrors = function(){
			var fieldset = bot.parents('.fieldset').first();
			fieldset.children('label.ui-state-error-text').remove();
			$('.ui-state-error',fieldset).removeClass('ui-state-error');

		}

		var addError = function(element, msg){

			removeAllErrors();

			if(element.get(0).localName == 'textarea'){
				element.addClass('ui-state-error').one('change keyup',function(){
					removeError(element);
				}).parent().after(errorContent(msg));

			} else {
				/*
				element.addClass('ui-state-error').one('change keyup',function(){
					removeError(element);
				}).nextAll('br').first().after(errorContent(msg));
				*/
				element.addClass('ui-state-error').one('change keyup',function(){
					removeError(element);
				}).parent().nextAll('br').first().after(errorContent(msg));

			}
		}

		var tBody			= $('#ingredientes-listagem tbody');

		var selIngrediente	= $('select[name=listaIngredientes]', $("#tabs-4"));
		var qtdIngrediente	= $('input[name=qtdIngrediente]');


		var idIngrediente = selIngrediente.val();
		var nomeIngrediente = selIngrediente.children(':selected').text();
		var qtdIng = qtdIngrediente.val();

		var duplicated = false;
		$("input[name^=ingredientes]", tBody).each(function(){
			if ($(this).val() == idIngrediente) {
				duplicated = true;
				return false;
			}
		});
		if (qtdIng.length == 0) {
			addError(qtdIngrediente, "O campo quantidade é obrigatório");
		} else if (!duplicated) {
			$('tr.noResults', tBody).remove();
			tBody.append(newRow(idIngrediente, nomeIngrediente, qtdIng));
			qtdIngrediente.val("");
			selIngrediente.val($('select[name=listaIngredientes] option:first').val());
			// Botões de up&down
			$('a.moveIngredienteUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveIngredienteDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		} else {
			addError(selIngrediente, "O ingrediente já esta na lista");
		}
	});

	$('#SubreceitaAddIngrediente').live('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		var bot = $(this);

		var errorTemplate	= '<label class="ui-state-error-text">{msg}</label>';

		var newRow = function(ingrediente, descricao, quantidade){
			var newRowTemplate			= '<tr class="alternate"><td>'+botSubreceitaIngredienteUp+'<br>'+ botSubreceitaIngredienteDown + '</td><td>{descricao}<input type="hidden" name="ingredientes[]" value="{ingrediente}"><ul class="row-actions"><li class="ui-corner-all"><a href="#" class="subreceitaDeleteIngrediente">Remover</a></li></ul></td><td>{quantidade}<input type="hidden" name="quantidades[]" value="{quantidade}"></td></tr>';

			return newRowTemplate
						.split('{ingrediente}').join(ingrediente)
						.split('{descricao}').join(descricao)
						.split('{quantidade}').join(quantidade);

		}

		var errorContent	= function(msg){
			return errorTemplate.split('{msg}').join(msg);
		}
		var removeError = function(element){
			element.removeClass('ui-state-error').nextAll('label.ui-state-error-text').first().remove();
		}
		var removeAllErrors = function(){
			var fieldset = bot.parents('.fieldset').first();
			fieldset.siblings('label.ui-state-error-text').remove();
			$('.ui-state-error',fieldset).removeClass('ui-state-error');

		}

		var addError = function(element, msg){

			removeAllErrors();

			if(element.get(0).localName == 'textarea'){
				element.addClass('ui-state-error').one('change keyup',function(){
					removeError(element);
				}).parent().after(errorContent(msg));

			} else {
				element.addClass('ui-state-error').one('change keyup',function(){
					removeError(element);
				}).parent().parent().nextAll('br').first().after(errorContent(msg));
			}
		}

		var tBody			= $('#subreceita-ingredientes-listagem tbody');

		var selIngrediente	= $('select[name=listaIngredientes]', $(".ingredienteInSubreceita"));
		var qtdIngrediente	= $('input[name=subreceitaQtdIngrediente]');


		var idIngrediente = selIngrediente.val();
		var nomeIngrediente = selIngrediente.children(':selected').text();
		var qtdIng = qtdIngrediente.val();

		var duplicated = false;
		$("input[name^=ingredientes]", tBody).each(function(){
			if ($(this).val() == idIngrediente) {
				duplicated = true;
				return false;
			}
		});
		if (qtdIng.length == 0) {
			addError(qtdIngrediente, "O campo quantidade é obrigatório");
		} else if (!duplicated) {
			$('tr.noResults', tBody).remove();
			tBody.append(newRow(idIngrediente, nomeIngrediente, qtdIng));
			qtdIngrediente.val("");
			selIngrediente.val($('select[name=subreceitaListaIngredientes] option:first').val());
			// Botões de up&down
			$('a.moveSubreceitaIngredienteUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveSubreceitaIngredienteDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		} else {
			addError(selIngrediente, "O ingrediente já esta na lista");
		}
	});

	$('#addSubreceita').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		var bot = $(this);

		var errorTemplate	= '<label class="ui-state-error-text">{msg}</label>';

		var newRow = function(subreceita, titulo){
			var newRowTemplate	= '<tr class="alternate"><td>'+botSubreceitaUp+'<br>'+ botSubreceitaDown + '</td><td>{titulo}<input type="hidden" name="subreceitas[]" value="{subreceita}"><ul class="row-actions"><li class="ui-corner-all"><a href="#" class="deleteSubreceita">Remover</a></li></ul></td></tr>';

			return newRowTemplate
						.split('{subreceita}').join(subreceita)
						.split('{titulo}').join(titulo);

		}

		var errorContent	= function(msg){
			return errorTemplate.split('{msg}').join(msg);
		}
		var removeError = function(element){
			element.removeClass('ui-state-error').nextAll('label.ui-state-error-text').first().remove();
		}
		var removeAllErrors = function(){
			var fieldset = bot.parents('.fieldset').first();
			fieldset.children().children('label.ui-state-error-text').remove();
			$('.ui-state-error',fieldset).removeClass('ui-state-error');

		}

		var addError = function(element, msg){

			removeAllErrors();

			element.addClass('ui-state-error').one('change keyup',function(){
				removeError(element);
			}).nextAll('br').first().after(errorContent(msg));
		}

		var tBody			= $('#subreceitas-listagem tbody');

		var selSubreceitas	= $('select[name=listaSubreceitas]');


		var idSubreceita = selSubreceitas.val();
		var nomeSubreceita = selSubreceitas.children(':selected').text();

		var duplicated = false;
		$("input[name^=subreceitas]", tBody).each(function(){
			if ($(this).val() == idSubreceita) {
				duplicated = true;
				return false;
			}
		});
		if (!duplicated) {
			$('tr.noResults', tBody).remove();
			tBody.append(newRow(idSubreceita, nomeSubreceita));
			selSubreceitas.val($('select[name=listaSubreceitas] option:first').val());
			// Botões de up&down
			$('a.moveSubreceitaUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveSubreceitaDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		} else {
			addError(selSubreceitas, "A sub receita já esta na lista");
		}
	});

	$('#addVinho').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		var bot = $(this);

		var errorTemplate	= '<label class="ui-state-error-text">{msg}</label>';

		var newRow = function(vinho, descricao){
			var newRowTemplate			= '<tr class="alternate"><td>'+botVinhoUp+'<br>'+ botVinhoDown + '</td><td>{descricao}<input type="hidden" name="vinhos[]" value="{vinho}"><ul class="row-actions"><li class="ui-corner-all"><a href="#" class="deleteVinho">Remover</a></li></ul></td></tr>';

			return newRowTemplate
						.split('{vinho}').join(vinho)
						.split('{descricao}').join(descricao);

		}

		var errorContent	= function(msg){
			return errorTemplate.split('{msg}').join(msg);
		}
		var removeError = function(element){
			element.removeClass('ui-state-error').nextAll('label.ui-state-error-text').first().remove();
		}
		var removeAllErrors = function(){
			var fieldset = bot.parents('.fieldset').first();
			fieldset.children().children('label.ui-state-error-text').remove();
			$('.ui-state-error',fieldset).removeClass('ui-state-error');

		}

		var addError = function(element, msg){

			removeAllErrors();

			element.addClass('ui-state-error').one('change keyup',function(){
				removeError(element);
			}).nextAll('br').first().after(errorContent(msg));
		}

		var tBody			= $('#vinhos-listagem tbody');

		var selVinho		= $('select[name=listaVinhos]');


		var idVinho = selVinho.val();
		var nomeVinho = selVinho.children().children(':selected').text();

		var duplicated = false;
		$("input[name^=vinhos]", tBody).each(function(){
			if ($(this).val() == idVinho) {
				duplicated = true;
				return false;
			}
		});
		if (!duplicated) {
			$('tr.noResults', tBody).remove();
			tBody.append(newRow(idVinho, nomeVinho));
			selVinho.val($('select[name=listaVinhos] option:first').val());
			// Botões de up&down
			$('a.moveVinhoUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveVinhoDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		} else {
			addError(selVinho, "O vinho já esta na lista");
		}
	});


	$(".moveIngredienteDown").live("click", function() {
		if (!$(this).hasClass('ui-state-disabled')) {
			var tBody = $('#ingredientes-listagem tbody');
			var linhaOriginal = $(this).parent().parent();
			var linhaSeguinte = linhaOriginal.next("tr");
			// Cria uma cópia do elemento após o próximo
			linhaOriginal.clone().insertAfter(linhaSeguinte);
			// Remove o original
			linhaOriginal.remove();
			// Botões de up&down
			$('a.moveIngredienteUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveIngredienteDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		}
	});
	$(".moveIngredienteUp").live("click", function() {
		if (!$(this).hasClass('ui-state-disabled')) {
			var tBody = $('#ingredientes-listagem tbody');
			var linhaOriginal = $(this).parent().parent();
			var linhaAnterior = linhaOriginal.prev("tr");
			// Cria uma cópia do elemento antes do anterior
			linhaOriginal.clone().insertBefore(linhaAnterior);
			// Remove o original
			linhaOriginal.remove();
			// Botões de up&down
			$('a.moveIngredienteUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveIngredienteDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		}
	});
	$(".moveVinhoDown").live("click", function() {
		if (!$(this).hasClass('ui-state-disabled')) {
			var tBody = $('#vinhos-listagem tbody');
			var linhaOriginal = $(this).parent().parent();
			var linhaSeguinte = linhaOriginal.next("tr");
			// Cria uma cópia do elemento após o próximo
			linhaOriginal.clone().insertAfter(linhaSeguinte);
			// Remove o original
			linhaOriginal.remove();
			// Botões de up&down
			$('a.moveVinhoUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveVinhoDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		}
	});
	$(".moveVinhoUp").live("click", function() {
		if (!$(this).hasClass('ui-state-disabled')) {
			var tBody = $('#vinhos-listagem tbody');
			var linhaOriginal = $(this).parent().parent();
			var linhaAnterior = linhaOriginal.prev("tr");
			// Cria uma cópia do elemento antes do anterior
			linhaOriginal.clone().insertBefore(linhaAnterior);
			// Remove o original
			linhaOriginal.remove();
			// Botões de up&down
			$('a.moveVinhoUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveVinhoDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		}
	});
	$(".moveSubreceitaIngredienteDown").live("click", function() {
		if (!$(this).hasClass('ui-state-disabled')) {
			var tBody = $('#subreceita-ingredientes-listagem tbody');
			var linhaOriginal = $(this).parent().parent();
			var linhaSeguinte = linhaOriginal.next("tr");
			// Cria uma cópia do elemento após o próximo
			linhaOriginal.clone().insertAfter(linhaSeguinte);
			// Remove o original
			linhaOriginal.remove();
			// Botões de up&down
			$('a.moveSubreceitaIngredienteUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveSubreceitaIngredienteDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		}
	});
	$(".moveSubreceitaIngredienteUp").live("click", function() {
		if (!$(this).hasClass('ui-state-disabled')) {
			var tBody = $('#subreceita-ingredientes-listagem tbody');
			var linhaOriginal = $(this).parent().parent();
			var linhaAnterior = linhaOriginal.prev("tr");
			// Cria uma cópia do elemento antes do anterior
			linhaOriginal.clone().insertBefore(linhaAnterior);
			// Remove o original
			linhaOriginal.remove();
			// Botões de up&down
			$('a.moveSubreceitaIngredienteUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveSubreceitaIngredienteDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		}
	});
	$(".moveSubreceitaDown").live("click", function() {
		if (!$(this).hasClass('ui-state-disabled')) {
			var tBody = $('#subreceitas-listagem tbody');
			var linhaOriginal = $(this).parent().parent();
			var linhaSeguinte = linhaOriginal.next("tr");
			// Cria uma cópia do elemento após o próximo
			linhaOriginal.clone().insertAfter(linhaSeguinte);
			// Remove o original
			linhaOriginal.remove();
			// Botões de up&down
			$('a.moveSubreceitaUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveSubreceitaDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		}
	});
	$(".moveSubreceitaUp").live("click", function() {
		if (!$(this).hasClass('ui-state-disabled')) {
			var tBody = $('#subreceitas-listagem tbody');
			var linhaOriginal = $(this).parent().parent();
			var linhaAnterior = linhaOriginal.prev("tr");
			// Cria uma cópia do elemento antes do anterior
			linhaOriginal.clone().insertBefore(linhaAnterior);
			// Remove o original
			linhaOriginal.remove();
			// Botões de up&down
			$('a.moveSubreceitaUp', tBody).removeClass("ui-state-disabled").first().addClass("ui-state-disabled");
			$('a.moveSubreceitaDown', tBody).removeClass("ui-state-disabled").last().addClass("ui-state-disabled");
		}
	});

	$("#subreceita").bind("click", function(){
		isSubReceita = $(this).attr("checked");
		if (isSubReceita) {
			setFormSubReceita();
		} else {
			setFormReceita();
		}
	});

	$("ul.filtros a").bind("click", function(event){
		event.stopPropagation();
		event.preventDefault();

		globals.url.param("subreceita", $(this).attr("href"));
		//globals.url.param('page',$(this).attr('href'));
	});

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
});
