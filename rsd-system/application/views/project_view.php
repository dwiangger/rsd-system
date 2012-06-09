	<h1>Project <?php echo $project['name']?></h1>
	<h3><?php echo $project['description']?></h3>
	
	<div id="body">
		<p>All project team :</p>

		<p>
			<?php 
				foreach ($teams as $team ) {
					?>
					<h3><?php echo anchor('team/index/'.$team['id'],$team['name']); ?></h3>
					<p><?php echo $team['description']; ?></p>
					<?php 
				}
			?>
		</p>
	</div>