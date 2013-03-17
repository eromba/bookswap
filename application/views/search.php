<p id="search-query" class="lead text-center">
  <?php if ($num_results == 0) { ?>
    Your search "<?php print $query; ?>" did not match any books.
  <?php } else {?>
    <span id="num-results"><?php print $num_results; ?></span>
    <?php print ($num_results == 1) ? 'book matches' : 'books match'; ?>
    your search "<span id="query-text"><?php print $query; ?></span>"
  <?php } ?>
</p>
<div id="results">
  <?php print $results; ?>
</div>
