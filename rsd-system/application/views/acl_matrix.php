<?php 
	if($matrix)
	{
		?>
<script>
	$(document).ready(function(){
		$("#input-box div.content").toggle();
		$("#input-box div.title a").click(function(e){
			e.preventDefault();

			$("#input-box div.content").toggle("slow");
		});
	});
</script>
		<?php 
	}
?>
<div id="matrix">
	<div class="row title">
		<div class="span12">
			<h3><a href="#">Access control list</a></h3>
		</div>
	</div>
		<?php 
		if($matrix)
		{
			?>
		<div class="row content">
			<div class="span12">
				<div class="row" style="font-weight:bold;border-bottom:1px solid #dddddd;border-top:1px solid #dddddd;">
					<div class="span2">User/Role</div>
					<div class="span1">left</div>
<?php 
foreach ($selectedRoles as $id => $role) {
	?>
<div class="span2"><?= $role ?></div>
	<?php 
}
/* Add more col in case total role < 4 */
$pad = (4 - count($selectedRoles) > 0)?(4 - count($selectedRoles)):0;
?>
					<div class="span1<?= ' offset'.($pad*2) ?>">right</div>
				</div>
<?php 
foreach ($selectedUsers as $userId => $userName) {
	?>
<div class="row" style="border-bottom:1px solid #dddddd;">
	<div class="span2"><?= $userName ?></div>
	<div class="span1">&nbsp;</div>
		<?php 
		foreach ($selectedRoles as $roleId => $role) {
			?>
	<div class="span2"><?php
			echo $permission[$userId][$roleId]; 
			?></div>
			<?php 
		}
		?>
	<div class="span1<?= ' offset'.($pad*2) ?>">&nbsp;</div>
</div>
	<?php 
}
?>
			</div>
		</div>				
			<?php 
		}
		?>
</div> <!-- close:matrix -->
<div id="input-box">
<?= form_open('authenticate/acl_matrix/view'); ?>
	<div class="row title">
		<div class="span12">
			<h3><a href="#">Input box</a></h3>
		</div>
	</div>
	<div class="row content">
		<div class="span3 offset2">
			<div class="row">
				<div class="span3">Select users :</div>
			</div>
			<div class="row">	
				<div class="span3">
					<select id="users" name="users[]" multiple="multiple">
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
		<div class="span3">			
			<div class="row">
				<div class="span3">Select role :</div>
			</div>
			<div class="row">	
				<div class="span3">
					<select id="roles" name="roles[]" multiple="multiple">
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
		<div class="span2"><button class="btn btn-primary">update</button></div>
	</div>
<?= form_close(); ?>
</div> <!-- close:input-box -->