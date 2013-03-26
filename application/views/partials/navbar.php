<div id="secondary-menu-bar" class="navbar navbar-inverse navbar-static-top">
  <div class="navbar-inner">
    <div class="container">
      <button data-toggle="collapse" data-target="#secondary-menu-bar .nav-collapse" class="btn btn-navbar collapsed">
        <i class="icon-expand icon-chevron-down"></i>
        <i class="icon-collapse icon-chevron-up"></i>
      </button>
      <span class="brand">Welcome to <?php print $site_name; ?></span>
      <nav class="secondary-menu-wrapper menu-wrapper nav-collapse">
        <?php if ($logged_in) { ?>
          <ul class="user-menu nav pull-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user icon-white"></i> Welcome, <?php print $user->first_name; ?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a class="my-books" href="<?php print base_url('my-posts'); ?>"><i class="icon-align-justify"></i> My Posts</a></li>
                <li><a class="logout" href="<?php print base_url('logout'); ?>"><i class="icon-off"></i> Log out</a></li>
              </ul>
            </li>
          </ul>
        <?php } else { ?>
          <div class="user-menu pull-right">
            <button class="login btn btn-inverse"><i class="icon-user icon-white"></i> Log in</button>
          </div>
        <?php } ?>
      </nav>
    </div>
  </div>
</div>
