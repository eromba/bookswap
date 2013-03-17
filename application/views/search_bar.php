<div id="search-bar">
  <?php
    print form_open('search', array(
      'class' => 'form-inline',
      'method' => 'get',
    ));
  ?>
    <label for="search-input"></label>
    <input id="search-input" type="text" name="q" value="<?php print (isset($query)) ? $query : '' ?>" placeholder="To Buy or Sell: Search by Title, ISBN, or Course Number" tabindex="1">
    <button id="search-button" class="btn btn-primary" type="submit"><i class="icon-search icon-white"></i></button>
  </form>
</div>
