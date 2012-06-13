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
	}
	if ( $displayForm ) {
		$attributes = array('class' => 'form-horizontal');
		?>
	<div class="span4 offset2">
	<div class="row">
		<div class="span4" style="text-align: center;">
			<h3>Log in :</h3>
		</div>
	</div>
	<?php echo form_open('authenticate/login',$attributes); ?>
<fieldset>
	<div class="control-group">
		<label class="control-label" for="input01">Username :</label>
		<div class="controls">
		<input type="text" class="input-large" name="user-name" id="user-name" value="" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="input01">Pasword :</label>
		<div class="controls">
		<input type="password" class="input-large" name="password" id="password" value="" />
		</div>
	</div>
	<div class="control-group" style="text-align: right;">
		<input type="submit" class="btn btn-primary" name="submit" id="submit" value="Log in" />
	</div>
</fieldset>
	<?php echo form_close(); ?>
	</div>
		<?php 
	}
	?>