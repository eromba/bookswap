<div id="login-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="Log in to <?php print $site_name; ?>" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="text-center">Log in to <?php print $site_name; ?></h3>
  </div>
    <div class="modal-body text-center">
      <?php
        echo form_open('login', array(
          'class' => '',
          'id' => 'login-form',
        ));
      ?>
        <label for="netid-field">NetID</label>
        <input id="netid-field" type="text" name="username" placeholder="NetID" />
        <label for="password-field">Password</label>
        <input id="password-field" type="password" name="password" placeholder="Password" />
      </form>
    </div>
    <div class="modal-footer">
      <button type="submit" form="login-form" class="btn btn-primary">Log in</button>
    </div>
</div>
