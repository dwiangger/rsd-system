<?php 
	if (isset($userInfo))
	{
?>
<h1><?php echo $userInfo['first_name'],' ',$userInfo['last_name']; ?>'s info :</h1>

<div id="body">
	All public/private user info
</div>

<?php 
	}else {
?>
<h3><?php echo $userId; ?> is hidden or unavailable. </h3>
<?php 
	}
?>
