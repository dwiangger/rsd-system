<ul>
	<?php if ( isset($nav) ) {
			foreach ($nav as $parent => $child) { ?>
				<li><span><?= $parent ?></span>
				<?php foreach ($child as $text => $link) { ?>
					<li><a href="<?= $this->config->item('base_url').$link ?>" title="<?= $text ?>">
						<span><?= $text ?></span></a></li>
					<?php } ?>
				</li>
				<?php }
		} ?>
</ul>