<p>All opening project in system:</p>
<p>
	<?php 
		foreach ($projects as $project) {
			?>
			<h3><?php echo anchor('project/index/'.$project['id'],$project['name']); ?></h3>
			<p><?php echo $project['description']; ?></p>
			<?php 
		}
	?>
</p>
