<header id="header" class="clearfix">
  <div id="branding" class="clearfix">
    <div id="logo">
      <a href="<?php print base_url(); ?>">
        <img alt="<?php print $site_name; ?>" src="<?php print base_url('img/logo.png'); ?>" />
      </a>
    </div>
  </div>
  <?php if ( ! $is_front_page) { print $search_bar; } ?>
</header>
