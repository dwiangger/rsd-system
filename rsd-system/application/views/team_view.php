<h1>Team <?php echo $team['name']; ?></h1>
<h3><?php echo $team['description']; ?></h3>

<div id="body">
	<p>All member :</p>

	<p>
		<?php 
			foreach ($users as $user ) {
				?>
				<h3><?php echo anchor('/user/index/'.$user['id'],$user['user_id']); ?></h3>
				<?php 
			}
		?>
	</p>
</div>
