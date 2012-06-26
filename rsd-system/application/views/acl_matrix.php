<?php 
	if($matrix)
	{
		?>
<script>
	$(document).ready(function(){
		/* toggle input area if matrix is displayed */
		$("#input-box div.content").toggle();
		$("#input-box div.title a").click(function(e){
			e.preventDefault();

			$("#input-box div.content").toggle("slow");
		});
		/* check-all check box */
		$("input.check-all").change(function(){
			if( $(this).is(":checked") )
			{
				var roleId = $(this).attr('role-id');
				$('input[role-id="'+roleId+'"]').attr("checked","checked");
			}else
			{
				var roleId = $(this).attr('role-id');
				$('input[role-id="'+roleId+'"]').removeAttr("checked");				
			}
		});
		/* handle shorten list by 4*/
	});
</script>
		<?php 
	}
?>
<div id="matrix">
<?= form_open('authenticate/update_matrix'); ?>
	<div class="row title">
		<div class="span12">
			<h3><a href="#">Access control list</a></h3>
		</div>
	</div>
		<?php 
		if($matrix)
		{
/* Add more col in case total role < 4 */
$total = count($selectedRoles);
$pad = (4 - ($total % 4))%4;
			?>
	<br />			
		<div class="row content">
			<div class="span12">
				<div class="row" style="font-weight:bold;border-bottom:1px solid #dddddd;border-top:1px solid #dddddd;">
					<div class="span2">User/Role</div>
					<div class="span1"><?php 
						if ($total > 4)
						{
							echo '<i class="icon-forward"></i>';
						}else
						{
							echo '<i class="icon-forward icon-white"></i>';
						}
					?></div>
<?php 
foreach ($selectedRoles as $id => $role) {
	?>
<div class="span2"><?= $role ?>
<input type="hidden" name="roleIdList[]" value="<?= $id ?>" />
</div>
	<?php 
}
?>
					<div class="span1<?= ' offset'.($pad*2) ?>"><?php 
						if ($total > 4)
						{
							echo '<i class="icon-forward"></i>';
						}else
						{
							echo '<i class="icon-forward icon-white"></i>';
						}
					?></div>
				</div>
<?php 
foreach ($selectedUsers as $userId => $userName) {
	?>
<div class="row" style="border-bottom:1px solid #dddddd;">
	<div class="span2"><?= $userName ?>
	<input type="hidden" name="userIdList[]" value="<?= $userId ?>" />
	</div>
	<div class="span1">&nbsp;</div>
		<?php 
		foreach ($selectedRoles as $roleId => $role) {
			?>
	<div class="span2">
		<input type="checkbox" <?= ($permission[$userId][$roleId]==1?'checked="checked"':'') ?>
			role-id="<?= $roleId ?>" 
			id="<?= 'checkbox_'.$userId.'_'.$roleId ?>" 
			name="<?= 'checkbox_'.$userId.'_'.$roleId ?>" 
			value="1"/>
	</div>
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
		<div class="row">
			<div class="span2">&nbsp;</div>
			<div class="span1">&nbsp;</div>
				<?php 
				foreach ($selectedRoles as $roleId => $role) {
					?>
			<div class="span2">
				<input type="checkbox" 
					class="check-all" 
					role-id="<?= $roleId ?>" 
					user-id=""/>
			</div>
					<?php 
				}
				?>
			<div class="span1<?= ' offset'.($pad*2) ?>">&nbsp;</div>
		</div>
		<br />
		<div class="row">
			<div class="span12" style="text-align:center;">
				<fieldset>
				<button class="btn btn-primary">Update</button>
				<a href="<?= site_url('authenticate/acl_matrix') ?>" class="btn">Cancel</a>
				</fieldset>
			</div>
		</div>						
			<?php 
		}
		?>

<?= form_close(); ?>
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
					<select id="users" name="users[]" multiple="multiple" required="required">
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
					<select id="roles" name="roles[]" multiple="multiple" required="required">
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
		<div class="span2"><button class="btn btn-primary">View matrix</button></div>
	</div>
<?= form_close(); ?>
</div> <!-- close:input-box -->