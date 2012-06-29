<?php 
/**
 * Define for menu: 
 * $nav = array(
 * 	$item1 => array(
 * 		"name" => "text", 		// display name
 * 		"link" => "text", 		// link without prefix 
 * 		"active" => TRUE/FALSE, // active or not 
 * 		"child" => array (
 * 			$child1 => array(
 * 				"item" => "text",	// display name
 * 				"link" => "text",	// link without prefix
 * 				"active" => TRUE/FALSE	// active or not
 * 			),
 * 			$child2, 
 * 			$child3, 
 * 			...
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
if ( !isset($nav) 
	|| count($nav) <= 0 )
{
	$nav = array(
		array(
			"name" => "Project",
			"link" => "#",
			"active" => FALSE,
			"child" => array(
				/* All related project */
		
				/* All project link */
				array(
					"name" => "All project",
					"link" => site_url("welcome/"),
					"active" => FALSE
				)
			)
		),
		array(
			"name" => "Task",
			"link" => "#",
			"active" => FALSE,
			"child" => array()
		),
		array(
			"name" => "Device",
			"link" => "#",
			"active" => FALSE,
			"child" => array()
		),
	);
}
?>
<div class="nav-collapse">
<ul class="nav">
	<?php 
		foreach ($nav as $item) {
			$hasChild = count($item["child"])>0;
			?>
			<li class="<?= $item["active"]?"active":"" ?><?= $hasChild?" dropdown":"" ?>">
				<a class="<?= $hasChild?"dropdown-toggle":"" ?>"
					<?= $hasChild?"data-toggle=\"dropdown\"":"" ?> 
					href="<?= $item["link"] ?>">
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
					<li class="<?= $child["active"]?"active":"" ?>">
						<a class="<?= $child["active"]?"active":"" ?>"
							href="<?= $child["link"] ?>">
							<?= $child["name"] ?>
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