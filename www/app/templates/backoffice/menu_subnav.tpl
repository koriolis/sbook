<?/*
<div class="subnav-bar">
	
	<? if($tab == 'clientes'):?>
		<ul>
			<li><a href="<?= BO_URI.'clientes'?>" <?= ($subtab == 'clientes') ? 'class="selected" ':'' ?>>Clientes</a></li>
			<li><a href="<?= BO_URI.'segmentos'?>" <?= ($subtab == 'segmentos') ? 'class="selected" ':'' ?>>Segmentos</a></li>
		</ul>
	<? endif; ?>
	<? if($tab == 'trabalhos'):?>
		<ul>
			<li><a href="<?= BO_URI.'trabalhos'?>" <?= ($subtab == 'trabalhos') ? 'class="selected" ':'' ?>>Trabalhos</a></li>
			<li><a href="<?= BO_URI.'tiposdetrabalho'?>" <?= ($subtab == 'tiposdetrabalho') ? 'class="selected" ':'' ?>>Tipos de Trabalho</a></li>
		</ul>
	<? endif; ?>
</div>
	# Exemplos de Items subnav -->
	<li>
		<a href="#" class="dropdown">Hello</a>
		<ul>
			<li><a href="#">Item 1</a></li>
			<li><a href="#">Item 2</a></li>
			<li><a href="#">Item 3</a></li>
		</ul>
	</li>
	<li><a href="#" class="selected">Marcas</a></li>
	<li><a href="#">Regiões</a></li>
	<li><a href="#">Vinhos</a></li>
	<li><a href="#">Caves</a></li>
	<li><a href="#">Aromas</a></li>

	<li><a href="#" class="defunct">Three</a></li>


						<div class="subnav-bar">
							<?if ($tab == "marcasevinhos"){?>
								<ul>
								
									<li>
										<a href="<?= BO_URI.'marcasevinhos/vinhos'?>" <?= ($subtab == 'vinhos') ? 'class="dropdown selected" ':'class="dropdown"' ?>>Vinhos</a>
										<ul class="dropdown">
											<li><a href="<?= BO_URI.'marcasevinhos/vinhos/cores'?>" class="<?= ( $subtab == 'vinhos' && $subtab_menu_item=='cores')? 'selected':'' ?>">Cores</a></li>
											<li><a href="<?= BO_URI.'marcasevinhos/vinhos/denominacoesdevinhos'?>" class="<?= ( $subtab == 'vinhos' && $subtab_menu_item == 'denominacoesdevinhos') ? 'selected' : '' ?>">Denominações</a></li>
											<li><a href="<?= BO_URI.'marcasevinhos/vinhos/categorias'?>" class="<?= ( $subtab == 'vinhos' && $subtab_menu_item == 'categorias') ? 'selected' : '' ?>">Categorias</a></li>
											<li><a href="<?= BO_URI.'marcasevinhos/vinhos/tipos'?>" class="<?= ( $subtab == 'vinhos' && $subtab_menu_item=='tipos')? 'selected':'' ?>">Tipos</a></li>				
											<li><a href="<?= BO_URI.'marcasevinhos/vinhos/tiposdepremios'?>" class="<?= ( $subtab == 'vinhos' && $subtab_menu_item == 'tiposdepremios') ? 'selected' : '' ?>">Tipos de Prémios</a></li>
											<li><a href="<?= BO_URI.'marcasevinhos/vinhos'?>" class="<?= ( $subtab == 'vinhos' && $subtab_menu_item == 'vinhos') ? 'selected' : '' ?>">Vinhos</a></li>
										</ul>
									</li>
									<li><a href="<?= BO_URI.'marcasevinhos/regioes'?>" <?= ($subtab == 'regioes') ? 'class="selected" ':'' ?>>Regiões</a></li>
									<li><a href="<?= BO_URI.'marcasevinhos/marcas'?>" <?= ($subtab == 'marcas') ? 'class="selected" ':'' ?>>Marcas</a></li>
									<li><a href="<?= BO_URI.'marcasevinhos/caves'?>" <?= ($subtab == 'caves') ? 'class="selected" ':'' ?>>Centros de Visitas</a></li>
									<li><a href="<?= BO_URI.'marcasevinhos/campanhas'?>" <?= ($subtab == 'campanhas') ? 'class="selected" ':'' ?>>Campanhas</a></li>
								</ul>
							<?}elseif ($tab == 'geral'){?>
								<ul>
									<li>
										<a href="<?= BO_URI.'geral/glossario'?>" <?= ($subtab == 'glossario') ? 'class="selected" ':'class=""' ?>>Glossário</a>
									</li>
									<li>
										<a href="<?= BO_URI.'geral/links'?>" <?= ($subtab == 'links') ? 'class="selected" ':'class=""' ?>>Links</a>
									</li>
									<li>
										<a href="<?= BO_URI.'geral/contactos'?>" <?= ($subtab == 'contactos') ? 'class="selected" ':'class=""' ?>>Contactos</a>
									</li>
									<li>
										<a href="<?= BO_URI.'geral/main_destaque_hp'?>" <?= ($subtab == 'main_destaque_hp') ? 'class="selected" ':'class=""' ?>>Main Destaque HP</a>
									</li>
									<li>
										<a href="<?= BO_URI.'geral/destaques'?>" <?= ($subtab == 'destaques') ? 'class="selected" ':'class=""' ?>>Destaques do site</a>
									</li>
									<li>
										<a href="<?= BO_URI.'geral/contactos_recebidos'?>" <?= ($subtab == 'contactos_recebidos') ? 'class="selected" ':'class=""' ?>>Contactos Recebidos</a>
									</li>
								</ul>
							<?}elseif ($tab == 'clube1500'){?>
								<ul>
									<li>
										<a href="<?= BO_URI.'clube1500/membros1500'?>" <?= ($subtab == 'membros1500') ? 'class="selected" ':'class=""' ?>>Membros</a>
									</li>
									<li>
										<a href="<?= BO_URI.'clube1500/categorias1500'?>" <?= ($subtab == 'categorias1500') ? 'class="selected" ':'class=""' ?>>Categorias</a>
									</li>
									<li>
										<a href="<?= BO_URI.'clube1500/artigos1500'?>" <?= ($subtab == 'artigos1500') ? 'class="selected" ':'class=""' ?>>Artigos</a>
									</li>
									<li>
										<a href="<?= BO_URI.'clube1500/revista1500'?>" <?= ($subtab == 'revista1500') ? 'class="selected" ':'class=""' ?>>Revista</a>
									</li>
								</ul>
							<?}elseif ($tab == 'receitasecocktails'){?>
								<ul>
									<li>
										<a href="<?= BO_URI.'receitasecocktails/receitas?subreceita=0'?>" <?= ($subtab == 'receitas') ? 'class="selected" ':'class=""' ?>>Receitas</a>
									</li>
								</ul>
								<ul>
									<li>
										<a href="<?= BO_URI.'receitasecocktails/pratos'?>" <?= ($subtab == 'pratos') ? 'class="selected" ':'class=""' ?>>Pratos</a>
									</li>
								</ul>
								<ul>
									<li>
										<a href="<?= BO_URI.'receitasecocktails/cozinhas'?>" <?= ($subtab == 'cozinhas') ? 'class="selected" ':'class=""' ?>>Cozinhas</a>
									</li>
								</ul>
								<ul>
									<li>
										<a href="<?= BO_URI.'receitasecocktails/cozinhados'?>" <?= ($subtab == 'cozinhados') ? 'class="selected" ':'class=""' ?>>Cozinhados</a>
									</li>
								</ul>
								<ul>
									<li>
										<a href="<?= BO_URI.'receitasecocktails/ingredientes'?>" <?= ($subtab == 'ingredientes') ? 'class="selected" ':'class=""' ?>>Ingredientes</a>
									</li>
								</ul>
								<ul>
									<li>
										<a href="<?= BO_URI.'receitasecocktails/momentos'?>" <?= ($subtab == 'momentos') ? 'class="selected" ':'class=""' ?>>Momentos</a>
									</li>
								</ul>
								<ul>
									<li>
										<a href="<?= BO_URI.'receitasecocktails/tipos_cocktail'?>" <?= ($subtab == 'tipos_cocktail') ? 'class="selected" ':'class=""' ?>>Tipos de cocktail</a>
									</li>
								</ul>
								<ul>
									<li>
										<a href="<?= BO_URI.'receitasecocktails/cocktails'?>" <?= ($subtab == 'cocktails') ? 'class="selected" ':'class=""' ?>>Cocktails</a>
									</li>
								</ul>
								<ul>
									<li>
										<a href="<?= BO_URI.'receitasecocktails/ocasioesespeciais'?>" <?= ($subtab == 'ocasioesespeciais') ? 'class="selected" ':'class=""' ?>>Ocasiões Especiais</a>
									</li>
								</ul>
							<?}elseif ($tab == 'bolsaempregos'){?>
								<ul>
									<li>
										<a href="<?= BO_URI.'bolsaempregos/anuncios_emprego'?>" <?= ($subtab == 'anuncios_emprego') ? 'class="selected" ':'class=""' ?>>Anúncios de emprego</a>
									</li>
								</ul>
                                <ul>
									<li>
										<a href="<?= BO_URI.'bolsaempregos/candidaturas_espontaneas'?>" <?= ($subtab == 'candidaturas_espontaneas') ? 'class="selected" ':'class=""' ?>>Candidaturas Espontaneas</a>
									</li>
								</ul>
							<?}?>
						</div>