<?php foreach ($posts as $post) { $book = $post->book; ?>
  <div id="post-<?php print $post->pid; ?>" class="entity post" data-pid="<?php print $post->pid; ?>">

    <div class="cover-image">
      <?php if ($book->image_url) { ?>
        <img src="<?php print $book->image_url; ?>" class="img-polaroid" />
      <?php } else { ?>
        <div class="no-image img-polaroid"><span>No Image Available</span></div>
      <?php } ?>
    </div>

    <div class="post-content">

      <h3 class="title"><?php print $book->title;?></h3>

      <h4 class="date-posted">Posted <?php print date('F j, Y', strtotime($post->postdate));?></h4>

      <div class="details clearfix">
        <div class="fields">
          <div class="edition field">
            <span class="field-label">Edition:</span>
            <span class="field-value"><?php print htmlentities(($post->edition) ? $post->edition : $book->edition); ?></span>
          </div>
          <div class="edition field">
            <span class="field-label">Condition:</span>
            <span class="field-value"><?php print $book_conditions[$post->condition]; ?></span>
          </div>
          <div class="price field">
            <span class="field-label">Price:</span>
            <span class="field-value">$<?php print $post->price; ?></span>
          </div>
          <div class="notes field">
            <span class="field-label">Notes:</span>
            <span class="field-value"><?php print ($post->notes) ? htmlentities($post->notes) : '(None)'; ?></span>
          </div>
        </div>
      </div>

    </div>

    <?php if ($post->active) { ?>
      <div class="post-actions">
        <a class="btn btn-primary" href="<?php print base_url('my-posts/' . $post->pid); ?>" role="button"><i class="icon-pencil icon-white"></i> Edit Post</a>
        <a class="deactivate-post btn btn-danger" href="#deactivate-post-modal" role="button" data-toggle="modal"><i class="icon-trash icon-white"></i> Deactivate Post</a>
      </div>
    <?php } ?>

  </div>
<?php } ?>
