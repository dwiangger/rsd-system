<div id="matrix">

</div> <!-- close:matrix -->
<div id="input-box">
	<div class="row">
		<div class="span4 offset1">
			<div class="row">
				<div class="span4">Select users :</div>
			</div>
			<div class="row">	
				<div class="span4">
					<select id="users" name="users">
<?php 
foreach ($users as $id => $user) {
	?>
						<option value="<?php echo $id; ?>"><?php echo $user['name']; ?></option>
	<?php 
}
?>
					</select>
				</div>
			</div>
		</div>
		<div class="span4">			
			<div class="row">
				<div class="span4">Select role :</div>
			</div>
			<div class="row">	
				<div class="span4">
					<select id="roles" name="roles">
<?php 
foreach ($roles as $id => $role) {
	?>
						<option value="<?php echo $id; ?>"><?php echo $role['name']; ?></option>
	<?php 
}
?>
					</select>
				</div>
			</div>
		</div>
		<div class="span2">update</div>
	</div>
</div> <!-- close:input-box -->