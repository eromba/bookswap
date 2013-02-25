<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>BookSwap 2.0</title>
  <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/custom.css" />
  <link type="text/css" rel="stylesheet" href="<?php echo base_url();?>css/bootstrap.min.css" />
</head>
<?php if ($logged_in) { ?>
<body class="logged-in">
    <div class="navbar">
      <div class="navbar-inner">
        <a class="brand" href="<?php echo base_url().'index.php'?>"><i class="icon-home icon-large"></i> </a><span class="brand"><?php echo $title?></span>
        <?php $this->load->view('search_form');?>
        <ul class="nav pull-right">
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Hey <?php echo($user->first_name);?> <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="<?php echo base_url().'index.php/myposts'?>"><i class="icon-align-justify"></i> My Posts</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo base_url().'index.php/logout'?>"><i class="icon-off"></i> Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
<?php }else{?>
<body class="logged-out">
  <div class="navbar">
    <div class="navbar-inner">
      <a class="brand" href="<?php echo base_url().'index.php'?>"><i class="icon-home icon-large"></i> </a><span class="brand"><?php echo $title?></span>
      <?php $this->load->view('search_form');?>
      <ul class="nav pull-right">
        <?php
          echo form_open('login', array(
            'class' => 'navbar-form pull-right',
            'id'    => 'login-form'
          ));
        ?>
          <input type="text" name="username" class="span2" placeholder="NetID" />
          <input type="password" name="password" class="span2" placeholder="Password" />
          <input type="submit" name="submit" value="Log in" class="btn" />
        </form>
      </ul>
    </div>
  </div>
        
<?php }?>
<div class="main container">
<script>
BASE_URL = "<?php echo base_url();?>"
</script>