<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo lang('guestbook') ?> | <?php echo lang($title) ?></title>
<!--<link href="http://fonts.googleapis.com/css?family=Allura" rel="stylesheet">-->
<link rel="stylesheet" href="<?php echo base_url() ?>themes/default/css.css"/>
<script src="<?php echo base_url() ?>themes/js/jquery-2.1.0.js"></script>
<script src="<?php echo base_url() ?>themes/js/js.js"></script>
</head>
<body id="body<?php echo $this->router->method ?>">

<h1><span role="presentation"><?php echo lang('guestbook') ?> | </span><?php echo lang($title) ?></h1>

<div id="wrapper">

<nav>
<?php if ($this->router->method != 'index') { ?><a href="<?php echo site_url('/') ?>"><?php echo lang('nav_home') ?></a>
<?php } elseif ($this->router->method != 'add') { ?><a href="<?php echo site_url('add') ?>" id="navAdd"><?php echo lang('nav_add_message') ?></a>
<?php } ?><?php if ($this->session->userdata('authUser')) { ?><a href="<?php echo site_url('user/logout') ?>" id="navLogout"><?php echo lang('nav_logout') ?></a>
<?php } ?>
</nav>

<?php if ($this->session->flashdata('gbMessage')) { ?><p id="flash"><?php echo lang($this->session->flashdata('gbMessage')) ?></p><?php } ?>
