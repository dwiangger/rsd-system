<html>
<head>
	<title>Project Tracking System</title>
	<!-- Default js library & css -->
	<link rel="stylesheet" href="<?= $this->config->item('base_url') ?>css/style.css" type="text/css">
	<link rel="stylesheet" href="<?= $this->config->item('base_url') ?>css/jquery-ui.css" type="text/css">
	<link rel="stylesheet" href="<?= $this->config->item('base_url') ?>css/blueprint/screen.css" type="text/css">
	<link rel="stylesheet" href="<?= $this->config->item('base_url') ?>css/blueprint/plugins/fancy-type/screen.css" type="text/css">
	
	<script type="text/javascript" src="<?= $this->config->item('base_url') ?>scripts/jquery.js"></script>
	<script type="text/javascript" src="<?= $this->config->item('base_url') ?>scripts/jquery-ui.js"></script>
	<!-- custom js & css -->
	<?= $_scripts ?>
	<?= $_styles ?>

</head>
<body>
	<div id="header" class="container">
<div class="logo span-6">
	<a href="<?= $this->config->item('base_url') ?>" title="Home">
		<img src="<?= $this->config->item('base_url') ?>images/gcs_logo.jpg" alt="GCS"/>
	</a>
</div>
<div class="title span-15 ">
	<div class="name">RSD PROJECT MANAGEMENT SYSTEM</div>
	<div class="slogan">TS Division - Not just test, build your best !</div>
</div>
<div class="user-info span-3 last">
	<div>Welcome <a href="<?php
		$userInfo = $this->authentication->getUserId();
		echo $userInfo?$userInfo:'#'; 
	?>"><?= $userInfo?$userInfo:'Guest'; ?></a></div>
	<div><?php echo date("d M, Y")?></div>
</div>
	</div>
	<div id="navigator" class="container">
<?= $_navigation ?>
	</div>
	<div id="main-content" class="container">
<?= $_content ?>
	</div>
	<hr />
	<div id="footer" class="container">
<div class="copyright span-12">&copy; 2012. RSD system.</div>
<div class="footer-link span-12 last">
	<a href="#">About</a> - 
	<a href="#">Help</a> - 
	<a href="mailto:anntvo@gcs-vn.com">Contact</a>
</div>
	</div>
</body>
</html>