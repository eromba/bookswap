<h2>Active Posts</h2>
<div id="active-posts" class="posts <?php print ( ! $active_posts) ? 'no-posts' : ''; ?>">
  <?php print $active_posts; ?>
  <p class="no-posts">You have no active posts.</p>
</div>
<h2>Deactivated Posts</h2>
<div id="deactivated-posts" class="posts <?php print ( ! $deactivated_posts) ? 'no-posts' : ''; ?>">
  <?php print $deactivated_posts; ?>
  <p class="no-posts">You have no deactivated posts.</p>
</div>