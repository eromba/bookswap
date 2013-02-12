<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>BookSwap 2.0</title>
  <link type="text/css" rel="stylesheet" href="<?echo BASE;?>css/custom.css" />
  <link type="text/css" rel="stylesheet" href="<?echo BASE;?>css/font-awesome.css" />
  <link type="text/css" rel="stylesheet" href="<?echo BASE;?>css/bootstrap.min.css" />
</head>
<?if($this->session->userdata('bookswap_user')){$user=$this->session->userdata('bookswap_user');?>
<body class="logged-in">
    <div class="navbar">
      <div class="navbar-inner">
        <a class="brand" href="<?echo BASE.'index.php'?>"><i class="icon-home icon-large"></i> </a><span class="brand"><?echo $title?></span>
        <?$this->load->view('searchform');?>
        <ul class="nav pull-right">
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Hey <?echo($user->first_name);?> <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="<?echo BASE.'index.php/myaccount'?>"><i class="icon-cog"></i> Preferences</a></li>
                <li><a href="<?echo BASE.'index.php/myposts'?>"><i class="icon-align-justify"></i> My Posts</a></li>
                <li class="divider"></li>
                <li><a href="<?echo BASE.'index.php/logout'?>"><i class="icon-off"></i> Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
<?}else{?>
<body class="logged-out">
  <div class="navbar">
    <div class="navbar-inner">
      <a class="brand" href="<?echo BASE.'index.php'?>"><i class="icon-home icon-large"></i> </a><span class="brand"><?echo $title?></span>
      <?$this->load->view('searchform');?>
      <?$this->load->view('loginform');?>
      <?if ($this->session->flashdata('headernotice')){?>
        <div class = "header-notice pull-right">
        <?echo($this->session->flashdata('headernotice'));?>
      </div>
      <?}?>
    </div>
  </div>
        
<?}?>
<div class="main container">
<script>
BASE_URL = "<?echo BASE;?>"
</script>