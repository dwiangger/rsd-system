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
		/* handle shorten list by page*/
		var index=1;
		$("div.role-page").each(function(){
			if($(this).attr("page") != 1)
			{
				$(this).hide();
			}
		});
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
			?>
	<br />			
		<div class="row content">
			<div class="span12">
				<div class="row" style="font-weight:bold;border-bottom:1px solid #dddddd;border-top:1px solid #dddddd;">
					<div class="span2">User/Role</div>
					<div class="span1"><?php 
						if ($total > 4)
						{
							echo '<i class="icon-backward"></i>';
						}else
						{
							echo '<i class="icon-backward icon-white"></i>';
						}
					?></div>
<!-- Display role pages, each has 4 roles -->
<?php 
$i = 1;
foreach ($selectedRoles as $id => $role) {
	if($i%4 == 1)
	{
		?>
<div class="span8 role-page" page="<?= floor($i/4 + 1) ?>">
	<div class="row">
		<?php 		
	}
	?>
		<div class="span2"><?= $role ?>
		<input type="hidden" name="roleIdList[]" value="<?= $id ?>" />
		</div>
	<?php 
	if($i%4 == 0)
	{
		?>
	</div>		
</div>
		<?php 
	}
	$i++;
}
/* display incase the last one is not %4 == 0 */
$i--;
if ($i%4 != 0)
{
		?>
	</div>		
</div>
		<?php 
}
?>
<!-- end of all role pages -->
					<div class="span1"><?php 
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
		$i = 1;
		foreach ($selectedRoles as $roleId => $role) {
			if($i%4 == 1)
			{
				?>
		<div class="span8 role-page" page="<?= floor($i/4 + 1) ?>">
			<div class="row">
				<?php 		
			}
			?>
	<div class="span2">
		<input type="checkbox" <?= ($permission[$userId][$roleId]==1?'checked="checked"':'') ?>
			role-id="<?= $roleId ?>" 
			id="<?= 'checkbox_'.$userId.'_'.$roleId ?>" 
			name="<?= 'checkbox_'.$userId.'_'.$roleId ?>" 
			value="1"/>
	</div>
			<?php 
			if($i%4 == 0)
			{
				?>
			</div>		
		</div>
				<?php 
			}
			$i++;
		}
/* display incase the last one is not %4 == 0 */
$i--;
if ($i%4 != 0)
{
		?>
	</div>		
</div>
		<?php 
}
		?>
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
				$i=1; 
				foreach ($selectedRoles as $roleId => $role) {
					if($i%4 == 1)
					{
						?>
				<div class="span8 role-page" page="<?= floor($i/4 + 1) ?>">
					<div class="row">
						<?php 		
					}
					?>
			<div class="span2">
				<input type="checkbox" 
					class="check-all" 
					role-id="<?= $roleId ?>" 
					user-id=""/>
			</div>
					<?php 
					if($i%4 == 0)
					{
						?>
					</div>		
				</div>
						<?php 
					}
					$i++;
				}
/* display incase the last one is not %4 == 0 */
$i--;
if ($i%4 != 0)
{
		?>
	</div>		
</div>
		<?php 
}
				?>
			<div class="span1">&nbsp;</div>
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