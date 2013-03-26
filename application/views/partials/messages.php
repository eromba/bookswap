<div id="messages">
  <?php foreach ($messages as $message) { ?>
    <div class="alert alert-<?php print $message['type']; ?>">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <?php print $message['text']; ?>
    </div>
  <?php } ?>
</div>
