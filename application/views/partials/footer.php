<footer id="footer" class="clearfix" role="contentinfo">
  <div class="container">
    <div class="footer-inner">
      <a href="<?php print $organization_url; ?>" class="org-logo">
        <img alt="Student Government Logo" src="" width="120" height="120" />
      </a>
      <div class="footer-text">
          &copy; <?php print (date('Y', time()) . ' ' . $organization_name); ?><br>
          Contact us at <a href="mailto:books@example.com">books@example.com</a><br>
          <small>Powered by <a href="https://github.com/eromba/bookswap/">BookSwap</a> | <a href="#about-modal" role="button" data-toggle="modal">About this site</a></small>
      </div>
      <a href="<?php print $university_url; ?>" class="university-logo">
        <img alt="University Logo" src="" width="120" height="120" />
      </a>
    </div>
  </div>
</footer>
