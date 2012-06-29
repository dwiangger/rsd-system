<!DOCTYPE HTML>
<html lang="en">
<head>
	<title>Project Tracking System</title>
	<!-- Default js library & css -->
	<link rel="stylesheet" href="<?= $this->config->item('base_url') ?>css/style.css" type="text/css">
	<link rel="stylesheet" href="<?= $this->config->item('base_url') ?>css/jquery-ui.css" type="text/css">
	
	<!-- Blueprint -->
	<!-- 
	<link rel="stylesheet" href="<?= $this->config->item('base_url') ?>css/blueprint/screen.css" type="text/css">
	<link rel="stylesheet" href="<?= $this->config->item('base_url') ?>css/blueprint/plugins/fancy-type/screen.css" type="text/css">
	 -->
	 
	 <!-- Bootstrap -->
	 <link rel="stylesheet" href="<?= $this->config->item('base_url') ?>css/bootstrap.css" type="text/css">
	 <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    
	 <script type="text/javascript" src="<?= $this->config->item('base_url') ?>scripts/jquery.js"></script>
	 <script type="text/javascript" src="<?= $this->config->item('base_url') ?>scripts/jquery.countdown.min.js"></script>
	 <script type="text/javascript" src="<?= $this->config->item('base_url') ?>scripts/bootstrap.js"></script>
	<script type="text/javascript" src="<?= $this->config->item('base_url') ?>scripts/jquery-ui.js"></script>
	<!-- custom js & css -->
	<?= $_scripts ?>
	<?= $_styles ?>

</head>
<body>
<!-- fixed Header -->
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?= $this->config->item('base_url') ?>">RSD system</a>
          <div class="btn-group pull-right">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="icon-user"></i> <?php
              	$userInfo = $this->authentication->getUserId(); 
				echo $userInfo?$userInfo:'Guest'; ?>
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
            <?php 
            	if($userInfo)
            	{ 
            		?>
              <li><a href="<?= $this->config->item('base_url') ?>index.php/user/index/<?= $userInfo ?>">Profile</a></li>
              <?php 
              	if($this->acl->checkRole($userInfo,'admin'))
              	{
              		?>
				<li class="divider"></li>
				<li><a href="<?= site_url("admin") ?>">Admin</a></li>
              		<?php 
              	}
              ?>
              <li class="divider"></li>
              <li><a href="<?= $this->config->item('base_url') ?>index.php/authenticate/logout">Sign Out</a></li>
            		<?php 
            	}else
            	{
            		?>
              <li><a href="<?= $this->config->item('base_url') ?>index.php/authenticate/login">Log in</a></li>            		
            		<?php 
            	}
            	?>
            </ul>
          </div>
<?= $_navigation ?>
        </div>
      </div>
    </div>
<!-- end of Header -->

<!-- container -->
<div class="container" style="min-height:520px">
<?= $_content ?>
</div> <!-- close:container -->
<div class="footer container">
	<div class="row">
		<div class="copyright span6">&copy; 2012. RSD system.</div>
		<div class="footer-link span6" style="text-align: right;">
			<a href="#">About</a> - 
			<a href="#">Help</a> - 
			<a href="mailto:anntvo@gcs-vn.com">Contact</a>
		</div>
	</div>
</div>
</body>
</html>