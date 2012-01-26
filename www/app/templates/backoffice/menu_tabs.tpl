						<div id='logo'><img src="<?= BO_IMG_URI ?>logo.png" align="left"><span id="qod-quote"></span></div>
						<ul class="tabs">
							<li><a href="<?= BO_URI ?>work"  <? if($tab == 'work') print('class="selected"')  ?>>Work</a></li>
							<li><a href="<?= BO_URI ?>knowledge"  <? if($tab == 'knowledge') print('class="selected"')  ?>>Knowledge</a></li>
							<li><a href="<?= BO_URI ?>gci"  <? if($tab == 'gci') print('class="selected"')  ?>>GCI</a></li>
							<li><a href="<?= BO_URI ?>people"  <? if($tab == 'people') print('class="selected"')  ?>>People</a></li>
							<li><a href="<?= BO_URI ?>destaque"  <? if($tab == 'destaque') print('class="selected"')  ?>>Destaques</a></li>
							<li><a href="<?= BO_URI ?>stats"  <? if($tab == 'stats') print('class="selected"')  ?>>Stats</a></li>
						</ul>
