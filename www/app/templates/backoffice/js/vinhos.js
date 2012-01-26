var spacer = function(numSpaces){
	var space = '&nbsp;';
	var ret = '';
	for(var i=0; i<numSpaces; i++) ret += space;

	return ret;
}



$(function(){
	var blockUI_defaults = {
		message: '<img src="'+globals.uri.img+'loading_small.gif" width="16" height="16" align="left">',
		css: {
			border: 'none',
			padding: '15px',
			backgroundColor: '#777',
			'-webkit-border-radius': '10px',
			'-moz-border-radius': '10px',
			textShadow: '1px 1px 1px #FFF',
			opacity: 1,
			color: '#000',
			fontSize: '14px',
			width: '16px',
			marginLeft: '15%'
		},
		overlayCSS: {
			backgroundColor: '#fff'
		},
		centerX: true

	}

	if($("#tabs").size()>0) $("#tabs").tabs();

	$("#marcas_id").bind('change',function(){

		globals.url.param('marcas_id',$(this).val());

	});


	var form_add	= $('form[name=add]');
	var form_edit	= $('form[name=edit]');
	var forms		= $('form[name=add], form[name=edit]');

	// Adicionar path de submit ao formulário removendo
	// a action para obtermos o path principal
	//
	form_add.attr('action',globals.url.toString());
	form_edit.attr('action', globals.url.toString());



	$('a.deletePremio').live('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		$('#premios-listagem tr.noResults').remove();
		var noResultsTemplate = '<tr class="noResults"><td colspan="3">Ainda não adicionou prémios a este Vinho</td></tr>';
		var bot		= $(this);

		var numRows = bot.parents('table.listagem tbody').children('tr').size();
		var tBody	= bot.parents('tbody');

		$.get(globals.uri.base + 'marcasevinhos/vinhos/apagarPremio/'+bot.data('premio_id'), function(status){
			
			if(status !== 'ok'){
				$("<div title='OK!'>Prémio apagado.</div>").dialog({
					modal: true,
					resizable: false,
					buttons: {
						'Ok': function(){
							$(this).dialog('close');						// - Fecha a janela de diálogo
						}
					}
				});
			}

			if(numRows > 1) {
				bot.parents('tr').remove();

			} else if(numRows == 1){
				bot.parents('tr').remove();
				tBody.append(noResultsTemplate);

			}
		});

	});

	$("#addCasta").bind('click', function(event){
		event.stopPropagation();
		event.preventDefault();

		var element		= $('input[name=casta_name]');
		var list		= $('#castas-list');
		var itemHTML	= '<li class="ui-corner-all">{casta_nome}<span class="ui-icon ui-icon-close ui-button"></span><input type="hidden" name="castas[]" value="{casta_nome}"></li>';
		var numItems	= list.children().size();


		if($.trim(element.val()).length > 0){
			var newItem		= $(itemHTML.split('{casta_nome}').join(element.val()));
			var existingItems = list.children();

			var itemExists = false;
			$.each(existingItems, function(idx, item){
				if ($(item).text() == newItem.text()) {
					itemExists = true;
					return false;
				}
			});

			if(!itemExists){
				newItem
					.appendTo(list);
				element.val('');
			} else {
				// ERRO: Item já existe
			}

		} else {
			// ERRO: Item vazio
		}
	});

	$('#castas-list li span').live('click', function(){
		$(this).parent().remove();
	});
	
	
	$('SELECT[name=s_marcas_id]').bind('change',function(event){
		
		var marca_id	= $(this).val();
		if(marca_id > 0){
			var url		= globals.url.attr('source').split('#')[0].split('/editar/')[0]+'/getVinhosByMarca/'+marca_id;
			
			$('SELECT[name=s_vinhos_id]')
				.empty()
				.addOption('loading','Loading...')
				.ajaxAddOption(url,{},false,function(){
					$(this).removeOption(0)
				});
		}
	});
	
	$("#substituirVinhoAssociado").click(function(event){
		event.preventDefault();
		event.stopPropagation();
		
		var marca_id = $('SELECT[name=s_marcas_id]').val();
		var old_vinho_id = $('SELECT[name=s_vinhos_id]').val();
		var new_vinho_id = $('INPUT[name=s_novo_vinho_id]').val();
		
		if(marca_id === '' || old_vinho_id === '') {
			$("<div title='Erro!'>Por favor escolha uma marca e um vinho!</div>").dialog({
					modal: true,
					resizable: false,
					buttons: {
						'Ok': function(){
							$(this).dialog('close');						// - Fecha a janela de diálogo
						}
					}
				});
		} else {
			
			var url		= globals.url.attr('source').split('#')[0].split('/editar/')[0]+'/substituirVinhoAssociado/'+old_vinho_id+'/'+new_vinho_id;
			var sucesso	= function(){
				
				$("<div title='Sucesso!'>Vinho Substituido!</div>").dialog({
									modal: true,
									resizable: false,
									buttons: {
										'Ok': function(){
											$(this).dialog('destroy');						// - Fecha a janela de diálogo
										}
									}
								});
			};
			
			var confirmacao = $("<div title='Atenção!'>Tem a certeza que deseja substituir?</div>").dialog({
					modal: true,
					resizable: false,
					buttons: {
						'Não': function(){
							$(this).dialog('close');						// - Fecha a janela de diálogo							
						},
						'Sim': function(){
							$(this).dialog('close');
							$.get(url,function(){
								
								$("<div title='Sucesso!'>Vinho Substituido!</div>").dialog({
									modal: true,
									resizable: false,
									buttons: {
										'Ok': function(){
											$(this).dialog('destroy');						// - Fecha a janela de diálogo
										}
									}
								});
								
							});
						}
					}
				});
			
		}
		
		
	});
	
	$('SELECT[name=aroma_outer_id]').bind('change',function(event){

		var select				= $(this);
		var selected_id			= select.val();
		//var selected_text		= $('option:selected',select).text();
		var aromas_middle_name	= $('input[name=aromas_middle]');
		var aromas_middle_id	= $('input[name=aromas_middle_id]');
		var aromas_inner_name	= $('input[name=aromas_inner]');
		var aromas_inner_id		= $('input[name=aromas_inner_id]');

		if(selected_id > 0) {

			// Get middle parent for this id
			var selected_row = $.grep(aromas_outer, function(obj){
				return (obj.id == selected_id);
			});
			var middle_row = $.grep(aromas_middle, function(obj){
				return (obj.id == selected_row[0].parent_id);
			});
			var inner_row = $.grep(aromas_inner, function(obj){
				return (obj.id == middle_row[0].parent_id);
			});

			aromas_middle_name.val('').val(middle_row[0].aroma);
			aromas_middle_id.val('').val(middle_row[0].id);
			aromas_inner_name.val('').val(inner_row[0].aroma);
			aromas_inner_id.val('').val(inner_row[0].id);


		} else {
			select.selectOptions('0',true);
			aromas_middle_name.val('');
			aromas_middle_id.val('');
			aromas_inner_name.val('');
			aromas_inner_id.val('');

		}

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

	$('#addAroma').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		var bot = $(this);
		var tBody = $('#aromas-listagem tbody');

		var errorTemplate	= '<label class="ui-state-error-text">{msg}</label>';
		var errorContent	= function(msg){
			return errorTemplate.split('{msg}').join(msg);
		}


		var aroma_outer_select	= $('SELECT[name=aroma_outer_id]');
		var aroma_outer_id		= aroma_outer_select.val();
		var aroma_outer_name	= $('option:selected',aroma_outer_select).text();
		var aroma_middle_name	= $('input[name=aromas_middle]').val();
		var aroma_middle_id		= $('input[name=aromas_middle_id]').val();
		var aroma_inner_name	= $('input[name=aromas_inner]').val();
		var aroma_inner_id		= $('input[name=aromas_inner_id]').val();

		var tableBody = $('#aromas-listagem tbody:visible');

		var getNewRow = function(row){
			var rowHTMLTemplate = '<tr><td>{outer_name}<input type="hidden" name="aroma_outer_ids[]" value="{outer_id}"><ul class="row-actions"><li class="ui-corner-all"><a href="#" class="deleteAroma">Apagar</a></li></ul></td><td>{middle_name}<input type="hidden" name="aroma_middle_ids[]" value="{middle_id}"></td><td>{inner_name}<input type="hidden" name="aroma_inner_ids[]" value="{inner_id}"></td></tr>';
			for(var key in row){
				rowHTMLTemplate = rowHTMLTemplate.split('{'+key+'}').join(row[key]);
			}
			return rowHTMLTemplate;
		};

		var removeAllErrors = function(){
			var fieldset = bot.siblings('.fieldset').first();
			fieldset.children('label.ui-state-error-text').remove();
			$('.ui-state-error',fieldset).removeClass('ui-state-error');

		}

		var addError = function(element, msg){

			removeAllErrors();

			element.addClass('ui-state-error').one('change keyup',function(){
				removeError(aroma_outer_select);
			}).nextAll('br').first().after(errorContent(msg));

		}

		var removeError = function(element){
			element.removeClass('ui-state-error').nextAll('label.ui-state-error-text').first().remove();
		}

		if(aroma_outer_select.val() == '0'){
			addError(aroma_outer_select, 'Campo de preenchimento obrigatório');

		} else {
			$('#aromas-listagem tr.noResults').remove();

			$(getNewRow({
				outer_name:		aroma_outer_name,
				outer_id:		aroma_outer_id,
				middle_name:	aroma_middle_name,
				middle_id:		aroma_middle_id,
				inner_name:		aroma_inner_name,
				inner_id:		aroma_inner_id
			})).appendTo(tableBody);


			aroma_outer_select.selectOptions('0');
			$('input[name=aromas_middle]').val('');
			$('input[name=aromas_inner]').val('');
		}



	});

	$('a.deleteAroma').live('click',function(event){
		event.stopPropagation();
		event.preventDefault();


		var noResultsTemplate = '<tr class="noResults"><td colspan="3">Ainda não adicionou aromas a este Vinho</td></tr>';
		var bot		= $(this);

		var numRows = bot.parents('#aromas-listagem tbody').children('tr').size();
		var tBody	= bot.parents('#aromas-listagem tbody');

		$('tr.noResults',tBody).remove();


		if(numRows > 1) {
			bot.parents('tr').remove();

		} else if(numRows == 1){
			bot.parents('tr').remove();
			tBody.append(noResultsTemplate);

		}

	});


	$('#addPremio').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		var bot = $(this);

		var errorTemplate	= '<label class="ui-state-error-text">{msg}</label>';

		var newRow = function(ano, tipo_nome, tipo_id, descricao){
			var newRowTemplate			= '<tr class="alternate"><td>{ano}<input type="hidden" name="premios_anos[]" value="{ano}"></td><td>{tipo_nome}<input type="hidden" name="premios_tipos_ids[]" value="{tipo_id}"><ul class="row-actions"><li class="ui-corner-all"><a href="#" class="deletePremio">Apagar</a></li></ul></td><td>{descricao}<input type="hidden" name="premios_descricoes[]" value="{descricao}"></td></tr>';

			return newRowTemplate
						.split('{ano}').join(ano)
						.split('{tipo_nome}').join(tipo_nome)
						.split('{tipo_id}').join(tipo_id)
						.split('{descricao}').join(descricao);

		}

		var errorContent	= function(msg){
			return errorTemplate.split('{msg}').join(msg);
		}
		var removeError = function(element){
			element.removeClass('ui-state-error').nextAll('label.ui-state-error-text').first().remove();
		}
		var removeAllErrors = function(){
			var fieldset = bot.siblings('.fieldset').first();
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
				element.addClass('ui-state-error').one('change keyup',function(){
					removeError(element);
				}).nextAll('br').first().after(errorContent(msg));
			}
		}




		var tBody			= $('#premios-listagem tbody');

		var anoPremio		= $('input[name=ano_premio]');
		var tipoPremio		= $('select[name=tipo_premio]');
		var descricaoPremio = $('textarea[name=descricao_premio]');

		// Validação para os prémios
		//
		var anoPremioValue			= $.trim(anoPremio.val());
		var tipoPremioValue			= tipoPremio.val();
		var descricaoPremioValue	= descricaoPremio.val();

		var removeNoResults = function(){
			console.log(tBody);
		};

		if( anoPremioValue.length == 0 ){
			addError(anoPremio, 'Campo de preenchimento obrigatório');

		} else if( !(/^\d+$/.test(anoPremioValue)) ){
			addError(anoPremio, 'O ano só pode conter algarismos')

		} else if(anoPremioValue.length != 4){
			addError(anoPremio, 'Um ano contem exactamente 4 algarismos.')

		} else if( tipoPremioValue == 0){
			addError(tipoPremio, spacer(36)+'O Tipo de Prémio é obrigatório.')

		} else if( descricaoPremioValue.length == 0 ) {
			addError(descricaoPremio, 'O campo Descrição é obrigatório');

		} else {
			removeAllErrors();
			$('tr.noResults',tBody).remove();

			tBody.append( newRow(anoPremioValue, tipoPremio.children(':selected').text(), tipoPremioValue, descricaoPremioValue) );
			anoPremio.val('');
			tipoPremio.val(0);
			descricaoPremio.val('')
		}



	});


	$('form[name=edit] div, form[name=add] div').children('.input, select, textarea')
		.bind('focus',function(){
			$(this).addClass('ui-state-highlight');
		})
		.bind('blur',function(){
			$(this).removeClass('ui-state-highlight');
		});

	var validate_options = {
		errorClass: 'ui-state-error-text',
		errorPlacement: function(error, element) {
			error.insertAfter(element.nextAll("br").first());
		},
		highlight: function(element) {
			$(element).addClass('ui-state-error');
		},
		unhighlight: function(element) {
			$(element).removeClass('ui-state-error')
		},

		rules: {
			nome:				'required',
			marcas_id:			'required',
			ano:				'digits',
			imagem:	{
				accept:		'jpg'
			},
			prova_ano:			'digits',
			garrafas_produzidas:'digits'
		},
		submitHandler: function(form){
			// Verifica se foram adicionadas imagens ao vinho
			var no_imagens = $("input[name=no_imagens]").val();
			if (no_imagens < 1) { 
				$("#tabs").tabs('select', "#tabs-5");
				$('#camposImagem').next('label').remove().end().after('<label class="ui-state-error-text">Tem de adicionar ao menos uma imagem</label>');
				return false;
			}
			if($(form).attr('name')=='edit'){	// Quando estivermos no formulário de edit é necessário perguntar ao user
												// se deseja criar uma nova versão


				// Gera o elemento automaticamente e usa-o como elemento do dialog
				//
				$("<div title='Atenção!'>Deseja gerar uma nova versão?</div>").dialog({
					modal: true,
					resizable: false,
					buttons: {
						'Não': function(){
							$('INPUT[name=nova_versao]', form).val('0');	// - Muda o valor do campo hidden para enviar com o form
							$(this).dialog('close');						// - Fecha a janela de diálogo
							form.submit();									// - Envia o formulário
						},
						'Sim': function(){
							$('INPUT[name=nova_versao ]', form).val('1');	// - Muda o valor do campo hidden para enviar com o form
							$(this).dialog('close');						// - Fecha a janela de diálogo
							form.submit();									// - Envia o formulário

						}
					}
				});

			} else {	// Quando estivermos no formulário de adicionar o comportamento deve ser normal (não é necessário perguntar
						// se o utilizador deseja criar uma nova versão do vinho

				form.submit();
			}
		}

	};

	form_add.validate(validate_options);
	form_edit.validate(validate_options);

	// botão de submit de ambos os forms
	$('a.submit').bind('click',function(event){
		event.preventDefault();
		event.stopPropagation();
		
		$(this).parents('FORM').submit();
	});

	//	Paginação (este tipo de função é auto-executável)
	//
	if(typeof(versoes_pager) != 'undefined'){ // Só executar na presença do objecto js versoes_pager que é passado para o JS na template vinhos_editar.tpl

		(function buildPagination(){

			var paginationList 		= $("#versoes-pagination");
			var versoesTableBody	= $('#versoes-listagem tbody');
			var firstHTML 			= '<li class="first"><a href="1" class="icon icon-page-first"></a></li>';
			var prevHTML 			= '<li class="prev" ><a href="'+versoes_pager.prev_group_end+'" class="icon icon-page-prev"></a></li>';
			var nextHTML			= '<li class="next"><a href="'+versoes_pager.next_group_start+'" class="icon icon-page-next"></a></li>';
			var lastHTML			= '<li class="last"><a href="'+versoes_pager.last+'" class="icon icon-page-last"></a></li>';
			var selectPageHTMLHead	= '<li class="page selected"><select name="page-jump" class="page-jump" autocomplete="off">';
			var selectPageHTMLFoot	= '</select></li>';
			var selectPageHTMLBody	= '';

			for(var pp=1; pp<= versoes_pager.numpages; pp++){
				selectPageHTMLBody += '"<option value="'+pp+'" '+((pp == versoes_pager.page) ? 'selected' : '')+'>'+pp+'</option>';
			}
			var selectPageHTML 		= selectPageHTMLHead + selectPageHTMLBody + selectPageHTMLFoot;


			var first 	= $(firstHTML).appendTo(paginationList);
			var prev	= $(prevHTML).appendTo(paginationList);

			for(var p=versoes_pager.group_start; p<=versoes_pager.group_end; p++) {

				if(p == versoes_pager.page) {
					$(selectPageHTML).appendTo(paginationList);

				} else {
					$('<li class="page"><a href="#">'+p+'</a></li>').appendTo(paginationList);

				}
			}

			var next = $(nextHTML).appendTo(paginationList);
			var last = $(lastHTML).appendTo(paginationList);

			// Desligar botões irrelevantes
			//
			if(versoes_pager.group <= 1) {
				first.addClass('disabled');
				prev.addClass('disabled');
			}

			if(versoes_pager.group < versoes_pager.numgroups) {
				next.removeClass('disabled');
				last.removeClass('disabled');
			} else {
				next.addClass('disabled');
				last.addClass('disabled');
			}

			// Mudança de página simples
			//
			$('li.page > a',paginationList).unbind().one('click',function(event){ // Page Number Clicked
				event.stopPropagation();
				event.preventDefault();

				$.blockUI(blockUI_defaults);

				versoes_pager.page = $(this).text();

				var url = globals.url.attr('source').split('editar').join('getPagedVersoes/'+versoes_pager.page);

				$.getJSON(url,function(data){

					paginationList.children().remove();
					versoes_pager = data.pager;
					buildPagination();
					versoesTableBody.children().remove();
					$(data.html).appendTo(versoesTableBody);
					$.unblockUI();
					return false;
				});

			});

			// Mudança de página via Select
			//
			$('li.page > SELECT.page-jump', paginationList).unbind().one('change',function(){
				$.blockUI(blockUI_defaults);
				versoes_pager.page = $(this).val();

				var url = globals.url.attr('source').split('editar').join('getPagedVersoes/'+versoes_pager.page);

				$.getJSON(url,function(data){

					paginationList.children().remove();
					versoes_pager = data.pager;
					buildPagination();
					versoesTableBody.children().remove();
					$(data.html).appendTo(versoesTableBody);
					$.unblockUI();
					return false;

				});
			});

			$('li:not(.page)>a[href]', paginationList).unbind().one('click',function(event){ // Group Nav and First / Last page
				event.stopPropagation();
				event.preventDefault();

				//globals.url.param('page',$(this).attr('href'));
				$.blockUI(blockUI_defaults);

				versoes_pager.page = $(this).attr('href');

				var url = globals.url.attr('source').split('editar').join('getPagedVersoes/'+versoes_pager.page);

				$.getJSON(url,function(data){

					paginationList.children().remove();
					versoes_pager = data.pager;
					buildPagination();
					versoesTableBody.children().remove();
					$(data.html).appendTo(versoesTableBody);
					$.unblockUI();
					return false;

				});

		   });


		})();
	}
	
	
	

	/*Código para processamento da colecção de imagens associadas a um vinho*/
	//código para o processamento das imagens
	$("#addImagem").bind('click',function(event){
		event.preventDefault();
		event.stopPropagation();
		campoDaImagem = $(this).prev('select').prev('input');
		campoDoTipo = $(this).prev('select');
		if (campoDoTipo.val() == "produto") { // Só pode haver uma imagem do tipo produto, verificar se já há alguma
			var existe = false;
			$.each($("td.tipo_imagem"), function(key, item) {
				if ($(item).text() == 'produto') {
					// Já existe uma imagem de produto
					alert("Só é permitida uma imagem do tipo Imagem do produto por vinho, para mais imagens utilize os tipos destaque ou genérica");
					existe = true;
					return false;
				}
			});
			if (existe) return false;
		}
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
			tipo = campoDoTipo.val();
			campoDoTipo.attr('rel',tipo);
			novoCampoTipo = campoDoTipo.clone(true).appendTo('#escondido');
			tipo = campoDoTipo.attr("rel");
			novoCampoTipo.attr("name","tipoImagensVinhoEscolhido[]");
			novoCampoImagem.attr("name","imagensVinhosEscolhido[]");
			novoCampoTipo.attr("imagem",imagem);
			novoCampoTipo.val(tipo);
			htmlNovoCampo = '<input Type="file" name="imagensVinhos[]" id="imagensVinhos[]"><select name="tipoImagensVinho[]" id="tipoImagensVinho"><option selected value="produto">Imagem do Produto</option><option value="destaque">Imagem para destaque</option><option value="generica">Imagem Genérica</option></select>';
			campoDaImagem.after(htmlNovoCampo);
			campoDaImagem.remove();
			campoDoTipo.remove();
			htmlTabela = '<tr colspan="2" rel="'+imagem+'"><td>'+imagem+'</td><td class="tipo_imagem">'+tipo+'</td><td><a class="removeImagem" rel="'+imagem+'" href="#">remover</a></td></tr>'
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
		tipoARetirar = $("select[imagem='"+imagem+"']");
		linhaARetirar = $("tr[rel='"+imagem+"']");
		tipoARetirar.remove();
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
		
		// Verifica o n.o de imagens actualmente
		var no_imagens = $("input[name=no_imagens]").val();
		
		// Se for a última não permite que a mesma seja apagada
		if (no_imagens == 1) {
			$("<div title='Erro!'>A imagem que está a tentar remover é a única associada ao vinho, terá de acrescentar outra primeiro.</div>").dialog({
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
			url: globals.uri.base+'marcasevinhos/vinhos/retira_media/'+imagem,
		  	success: function(data) {
		  		if (data=='ok'){
					linhaARetirar = $("tr[rel='"+imagem+"']");
					linhaARetirar.remove();
					return false;
		  		}
		  	}
		});
	})


});
