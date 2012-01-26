<? if($list['pager']['numpages'] > 1): ?>
	<div id="pagination-results"><?= ($list['pager']['page_first_row']+1) ?> a <?= ($list['pager']['page_last_row']+1) ?> de <?= $list['pager']['count'] ?> resultados</div>
	<ul class="pagination" style="overflow:hidden;float:left;">
		<? if($list['pager']['group'] > 1): // ?>
			<li class="first"><a href="1" class="icon icon-page-first"></a></li>
			<li class="prev" ><a href="<?= $list['pager']['prev_group_end'] ?>" class="icon icon-page-prev"></a></li>
		<? else: ?>
			<!-- <li class="first disabled"><a class="icon icon-page-first"></a></li>-->
			<!--<li class="prev disabled"><a class="icon icon-page-prev"></a></li>-->
		<? endif; ?>


		<? for($p=$list['pager']['group_start']; $p<=$list['pager']['group_end']; $p++): ?>
			<? if($p == $list['pager']['page']): ?>
				<li class="page selected">
					<select name="page-jump" class="page-jump" autocomplete="off">
						<? for($pp=1; $pp<=$list['pager']['numpages']; $pp++): ?>
						<option value="<?= $pp ?>" <?= ($pp == $list['pager']['page']) ? 'selected' : '' ?>><?= $pp ?></option>
						<? endfor; ?>
					</select>
				</li>
			<? else: ?>
				<li class="page"><a href="#"><?= $p ?></a></li>
			<? endif; ?>
		<? endfor; ?>

		<? if($list['pager']['group'] < $list['pager']['numgroups']): // ?>
			<li class="next"><a href="<?= $list['pager']['next_group_start'] ?>" class="icon icon-page-next"></a></li>
			<li class="last"><a href="<?= $list['pager']['last'] ?>" class="icon icon-page-last"></a></li>
		<? else: ?>
			<!--<li class="next disabled"><a class="icon icon-page-next"></a></li>-->
			<!--<li class="last disabled"><a class="icon icon-page-last"></a></li>-->
		<? endif; ?>
	</ul>
	
	
<? endif; ?>