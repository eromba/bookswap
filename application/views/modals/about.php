<div id="about-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="About <?php print $site_name; ?>" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="text-center">About <?php print $site_name; ?></h3>
  </div>
  <div class="modal-body text-center">
    <p><?php print $site_name; ?> is a service of<br><a href="<?php print $organization_url; ?>"><?php print $organization_name; ?></a>.</p>
    <p>Powered by the open-source <a href="https://github.com/eromba/bookswap/">BookSwap</a> project.</p>
    <h4 class="text-center"><?php print $site_name; ?> Team</h4>
    <ul class="credits unstyled">
      <li><b>Name</b><br>Position</li>
      <li><b>Name</b><br>Position</li>
      <li><b>Name</b><br>Position</li>
    </ul>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
