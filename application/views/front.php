<div id="search-bar">
  <?php
    print form_open('search', array(
      'class' => 'form-inline',
      'method' => 'get',
    ));
  ?>
    <input id="search-input" type="text" name="q" placeholder="To Buy or Sell: Search by Title, ISBN, or Course Number" tabindex="1">
    <button id="search-button" class="btn btn-primary" type="submit"><i class="icon-search icon-white"></i></button>
  <?php print form_close(); ?>
</div>

<a id="how-it-works-link" class="btn" href="#how-it-works-modal" role="button" data-toggle="modal">
  <i class="icon-info-sign"></i> How it Works
</a>
