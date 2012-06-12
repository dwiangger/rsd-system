<?php 
	if (isset($userInfo))
	{
?>
<h1><?php echo $userInfo->first_name,' ',$userInfo->last_name; ?>'s info :</h1>

<div id="body">
	<?php 
		foreach ($userInfo as $propName => $propValue) {
			?>
			<div><?= $propName ?>:<?= $propValue ?></div>
			<?php 
		}
	?>
</div>

<?php 
	}else {
?>
<h3><?php echo $userId; ?> is hidden or unavailable. </h3>
<?php 
	}
?>
