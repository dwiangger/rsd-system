<?php 
/**
 * $delay: 			If reached over 3 failed times, prevent login. 
 * $displayAlert: 	Display or not alerting abot wrong username/password 
 * None of 2 above is set ? go ahead. 
 */
?>
<div class="row">
	<div class="span4 offset4" style="background:url('../../img/lock.png') no-repeat;">
	<?php echo form_open('authenticate/login'); ?>
				<?php 
					if ( $displayAlert ) 
					{
						?>
		<div class="row">
			<div class="span3 offset1">
				<h4>Invalid user name/password :</h4>
			</div>
		</div>
						<?php 
					}else
					{
						?>
		<div class="row">
			<div class="span2 offset2">
				<h4>Login :</h4>
			</div>
		</div>
						<?php 
					}
				?>
<fieldset class="control-group<?= $displayAlert?" error":"" ?>">
		<div class="row">
			<div class="span3 offset1">
<input type="text" name="user-name" id="user-name" value="" placeholder="username" <?= isset($delay)?"disabled=\"disabled\"":"" ?> />
<input type="password" name="password" id="password" value="" placeholder="password" <?= isset($delay)?"disabled=\"disabled\"":"" ?> />

			</div>
		</div>
</fieldset>
<?php 
	if ( isset($delay) )
	{
		?>
			<div class="row">
				<div class="span3 offset1" style="text-align:right;">
					<div class="small-attention">You are logged failed <?php echo $numOfFailed; ?> time(s).</div>
					<div class="small-attention">Please try again after <?php echo date("i:s",$numOfFailed*60-$delay); ?>s.</div>
				</div>
			</div>
		<?php 
	}else
	{
		?>
	<div class="row">
		<div class="span4" style="text-align:right;">
<input type="submit" class="btn btn-primary" name="submit" id="submit" value="Log in" />
		</div>
	</div>
		<?php 
	}
?>
	<?php echo form_close(); ?>
	</div>
</div>