<html>
<head>
	<title>Project Tracking System</title>
	<!-- Default js library & css -->
	<!-- <link rel="stylesheet" href="<?= $this->config->item('base_url') ?>css/style.css" type="text/css"> -->
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
              <li class="divider"></li>
              <li><a href="<?= $this->config->item('base_url') ?>index.php/authenticate/logout">Sign Out</a></li>
            		<?php 
            	}else
            	{
            		?>
              <li><a href="#">Log in</a></li>            		
            		<?php 
            	}
            	?>
            </ul>
          </div>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="<?= $this->config->item('base_url') ?>">Home</a></li>
              <!-- <li><a href="#about">About</a></li> -->
              <!-- <li><a href="#contact">Contact</a></li> -->
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
<!-- end of Header -->

<!-- container -->
<div class="container">
	
	<div class="row">
		<div class="span6">
			<div id="navigator">
<?= $_navigation ?>
			</div>
		</div>
	</div>
	
	<div id="main-content">
<?= $_content ?>
	</div>
	<hr />
	<div class="footer">
<div class="copyright">&copy; 2012. RSD system.</div>
<div class="footer-link">
	<a href="#">About</a> - 
	<a href="#">Help</a> - 
	<a href="mailto:anntvo@gcs-vn.com">Contact</a>
</div>
	</div>
</div> <!-- close:container -->
</body>
</html>