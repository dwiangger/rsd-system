	<?php 
	if ( $displayAlert ) {
		if ( isset($delay) ) {
			?>
			<div>You are logged failed <?php echo $numOfFailed; ?> time(s). Please wait and try again after <?php echo date("i:s",$numOfFailed*60-$delay); ?>s.</div>
			<?php 
		}else 
		{
			?>
			<div>Invalid user name or password. Please try again.</div>
			<?php 
		}
	}else
	{
		?>
		<div>Please input username/password :</div>
		<?php 
	}
	?>
	
	<?php 
	if ( $displayForm ) {
		?>
	<?php echo form_open('authenticate/login')?>
	<input type="text" name="user-name" id="user-name" value="" />
	<input type="password" name="password" id="password" value="" />
	<input type="submit" name="submit" id="submit" value="Log in" />
	<?php echo form_close(); ?>
		<?php 
	}
	?>