/**
 * Procura pelo valor num objecto e devolve a key corresponde se existir, -1 se não existir
 */
var inObject		= function(value, obj){
	var ret = -1;
	$.each(obj,function(key, val){
		if(val === value) {
			ret = key;
			return false;
		}

	});
	return ret;

};

$(function(){

	$("ul.filtros li:last-child").addClass('last-child');

	/*************************
	 * Métodos genéricos de UI
	 *************************/
	 /*
	'source:',	globals.url.attr('source'));
	'protocol:',globals.url.attr('protocol'));
	'host:',	globals.url.attr('host'));
	'port:',	globals.url.attr('port'));
	'query:',	globals.url.attr('query'));
	'file:',	globals.url.attr('file'));
	'hash:',	globals.url.attr('hash'));
	'path:',	globals.url.attr('path'));
	*/
		
   // Pagination actions
   //
   $('UL.pagination > li.page > a').bind('click',function(event){ // Page Number Clicked
		event.stopPropagation();
		event.preventDefault();

		globals.url.param('page',$(this).text());

   });

   $('UL.pagination > li.page > SELECT.page-jump').bind('change',function(event){ // SELECT BOX Jump To Page
		event.stopPropagation();
		event.preventDefault();

		globals.url.param('page',$(this).val());
   });

   $('li:not(.page)>a[href]','UL.pagination').bind('click',function(event){ // Group Nav and First / Last page
		event.stopPropagation();
		event.preventDefault();

		globals.url.param('page',$(this).attr('href'));

   });

   

	// Sort Action
	// 
	$('TH.sort a').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();
		var element = $(this);
		var newSortField = element.attr('rel');
		var newSortDirection = (element.parent().hasClass('sort-asc')) ? 'desc' : 'asc';
		
		globals.url.param({
			sortField: newSortField,
			sortDirection: newSortDirection
		});
	});

	// SELECT Filter Generic action
	//
	$("SELECT.filter").bind('change',function(){

		globals.url.param($(this).attr('name'),$(this).val());

	});

	// Row Actions
	// ---------------

	// Adicionar Row 
	// - Assume-se que a action de adicionar é 'adicionar'
	//
	$('#rowAdd').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		// Adicionar a action 'adicionar' ao url sem alterar os outros
		// parâmetros. Faz reload automático do novo path.
		//
		var newUrl = globals.url.attr('path')+'/adicionar';
		globals.url.attr('path',newUrl.split('//').join('/'));

	});

	// Editar Row
	// - Assume-se que a action de adicionar é 'editar'
	//
	$('ul.row-actions a.editarRow').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		// Adicionar a action 'editar' ao url e o id do item a editar (vem no HREF) sem alterar os outros
		// parâmetros do URL. Faz reload automático do novo path.
		//
		
		var newUrl = globals.url.attr('path')+'/editar/' + $(this).attr('href');
		globals.url.attr('path',newUrl.split('//').join('/'));


	});

	// Apagar Row
	$('ul.row-actions a.deleteRow').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();
		
		if(confirm('Tem a certeza que deseja apagar?')) {
			// Adicionar a action 'apagar' ao url e o id do item a apagar (vem no HREF) sem alterar os outros
			// parâmetros do URL. Faz reload automático do novo path.
			//
			var newUrl = globals.url.attr('path')+'/apagar/' + $(this).attr('href');
			globals.url.attr('path',newUrl.split('//').join('/'));
		}

		
	});

	$('#backToListFromAdd').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();

		// Remove a action 'adicionar' do path.
		// Faz reload automático do novo path (voltar à lista).
		//
		
		var newUrl = globals.url.attr('path').split('/adicionar').join('');
		globals.url.attr('path',newUrl.split('//').join('/'));

	});
	$('#backToListFromEdit').bind('click',function(event){
		event.stopPropagation();
		event.preventDefault();
		
		// Remove a action 'editar' do path.
		// Faz reload automático do novo path (voltar à lista).
		//
		var newUrl = (globals.url.attr('path').split('/editar/')[0]+$(this).attr('rel'));
		globals.url.attr('path',newUrl.split('//').join('/'));

	});

		

	// Se existir uma mensagem de erro vinda do PHP, então faz alert da respectiva mensagem de erro.
	//
	if(globals.errorMessage !== '') {
		$("#okButtonForErrors").live('click', function(event){
			event.stopPropagation();
			event.preventDefault();

			$.unblockUI();
		});
		
		$.blockUI({
			theme: true,
			title: 'ERRO!',
			message: '<br>'+unescape(globals.errorMessage)+'<br style="clear:both;">&nbsp;<hr><button id="okButtonForErrors" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="float: right; margin: .5em .4em .5em 0; cursor: pointer; padding: .2em .6em .3em .6em; line-height: 1.4em; width:auto; overflow:visible;">Ok</button>',
			css: {
				minHeight: '200px'
			}
		});
		
		
	};


});
