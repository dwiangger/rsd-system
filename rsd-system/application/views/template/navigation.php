<?php 
/**
 * Define for menu: 
 * $nav = array(
 * 	$item1 => array(
 * 		"name" => "text", 		// display name
 * 		"link" => "text", 		// link without prefix 
 * 		"active" => TRUE/FALSE, // active or not 
 * 		"child" => array (
 * 			"item" => "text",	// display name
 * 			"link" => "text",	// link wothou prefix
 * 			"active" => TRUE/FALSE	// active or not
 * 		) 
 * 	),
 * 	$item2, 
 *  $item3, 
 *  ...
 * )
 */
/**
 * Create default navbar in case. 
 */
if ( !isset($nav) )
{
	$nav = array(
		array(
			"name" => "Home",
			"link" => "#",
			"active" => TRUE,
			"child" => array()
		)
	);
}
?>
<div class="nav-collapse">
<ul class="nav">
	<?php 
		foreach ($nav as $item) {
			$hasChild = count($item["child"])>0;
			?>
			<li class="<?= $item["active"]?"active":"" ?>">
				<a class="<?= $hasChild?"dropdown-toggle":"" ?>"
					<?= $hasChild?"data-toggle=\"dropdown\"":"" ?> 
					href="<?= $this->config->item('base_url').$item["link"] ?>">
					<?= $item["name"] ?>
					<?= $hasChild?"<b class=\"caret\"></b>":"" ?>
				</a>
				<?php 
					if ($hasChild)
					{
						?>
				<ul class="dropdown-menu">
							<?php 
								foreach ($item["child"] as $child) {
									?>
					<li class="<?= $item["active"]?"active":"" ?>">
						<a class="<?= $child["active"]?"active":"" ?>"
							href="<?= $this->config->item('base_url').$child["link"] ?>">
							<?= $item["name"] ?>
						</a>
					</li>
									<?php 
								}
							?>
				</ul>
						<?php 
					}
				?>
			</li>
			<?php 
		}
	?>
</ul>
</div><!--/.nav-collapse -->