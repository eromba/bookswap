<!DOCTYPE html>
<html class="no-js">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php print $head_title; ?></title>
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="<?php print base_url('css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php print base_url('css/jquery.placeholder.css'); ?>">
    <link rel="stylesheet" href="<?php print base_url('css/base.css'); ?>">
    <link rel="stylesheet" href="<?php print base_url('css/content.css'); ?>">
    <link rel="stylesheet" href="<?php print base_url('css/responsive.css'); ?>">

    <!--[if lt IE 9]>
      <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
      <script>window.html5 || document.write('<script src="<?php print base_url('js/html5shiv.js'); ?>"><\/script>')</script>
    <![endif]-->
  </head>
  <body class="<?php print $body_classes; ?>">
    <!--[if lt IE 7]>
      <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
    <![endif]-->

    <div id="page-wrapper">
      <?php print $navbar; ?>
      <div id="page" class="container">
        <div id="columns-wrapper">
          <?php print $header; ?>
          <div id="columns">
            <?php if (isset($title)) { ?>
              <h1 id="page-title"><?php print $title; ?></h1>
            <?php } ?>
            <?php print $messages; ?>
            <?php print $content; ?>
          </div>
        </div>
      </div>
    </div>

    <?php print $footer; ?>

    <?php print $modals; ?>

    <script>
      window.BOOKSWAP = {
        'base_url': '<?php print base_url(); ?>',
        'csrf_token_name': '<?php print $this->config->item('csrf_token_name'); ?>',
        'csrf_cookie_name': '<?php print $this->config->item('csrf_cookie_name'); ?>'
      };
    </script>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php base_url('js/jquery-1.9.1.min.js'); ?>"><\/script>')</script>

    <script src="<?php print base_url('js/bootstrap.min.js'); ?>"></script>
    <script src="<?php print base_url('js/jquery.placeholder.min.js'); ?>"></script>
    <script src="<?php print base_url('js/jquery.h5validate.min.js'); ?>"></script>
    <script src="<?php print base_url('js/jquery.cookie.min.js'); ?>"></script>
    <script src="<?php print base_url('js/main.js'); ?>"></script>

    <?php if (ENVIRONMENT == 'production') { ?>
    <script>
      var _gaq=[['_setAccount','<?php print $this->config->item('google_analytics_id'); ?>'],['_trackPageview']];
      (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
      g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
      s.parentNode.insertBefore(g,s)}(document,'script'));
    </script>
    <?php } ?>
  </body>
</html>
