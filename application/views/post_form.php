<?php
print form_open(base_url(uri_string()), array(
  'id' => 'post-form',
  'class' => 'form-horizontal',
));
?>

<div id="book-to-sell" class="entity book clearfix">
  <div class="cover-image">
    <?php if ($book->image_url) { ?>
      <img src="<?php print $book->image_url; ?>" class="img-polaroid" />
    <?php } else { ?>
      <div class="no-image img-polaroid"><span>No Image Available</span></div>
    <?php } ?>
  </div>
  <div class="book-content">
    <h3 class="title"><?php print $book->title; ?></h3>
    <div class="fields">
      <?php if ($book->authors) { ?>
      <div class="by-line field">
        By <span class="author"><?php print $book->authors; ?></span>
      </div>
      <?php } ?>
      <?php if ($book->edition) { ?>
        <div class="edition field">
          <span class="field-label">Edition:</span>
          <span class="field-value"><?php print $book->edition; ?></span>
        </div>
      <?php } ?>
      <?php if ($book->publisher) { ?>
        <div class="edition field">
          <span class="field-label">Publisher:</span>
          <span class="field-value"><?php print $book->publisher; ?></span>
        </div>
      <?php } ?>
      <div class="isbn field">
        <span class="field-label">ISBN:</span>
        <span class="field-value"><?php print $book->isbn; ?></span>
      </div>
    </div>
  </div>
</div>

<fieldset>
  <legend>Describe Your Copy</legend>
  <div class="control-group">
    <label class="control-label" for="edition-input">Edition:</label>
    <div class="controls">
      <input id="edition-input" type="text" name="edition" value="<?php print set_value('edition', $edition); ?>" placeholder="<?php print $book->edition; ?>">
      <span class="help-block">
        Which edition of this book are you selling (if different than above)?
      </span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="condition-input">Condition:</label>
    <div class="controls">
      <?php print form_dropdown('condition', $book_conditions, set_value('condition', $condition), 'id="condition-input"'); ?>
    </div>
  </div>
</fieldset>

<fieldset>
  <legend>Name Your Price</legend>
  <table id="lowest-prices" class="table table-bordered table-condensed">
    <caption><h4>Lowest Prices:</h4></caption>
    <tr>
      <td class="vendor"><?php print $site_name; ?></td>
      <td class="price"><?php print ($book->min_student_price) ? '$' . $book->min_student_price : '(Unavailable)'?></td>
    </tr>
    <tr>
      <td class="vendor"><?php print $bookstore_name; ?></td>
      <td class="price"><?php print ($book->bookstore_new_price) ? '$' . $book->bookstore_new_price : '(Unavailable)'?></td>
    </tr>
    <tr>
      <td class="vendor">Amazon.com</td>
      <td class="price"><?php print ($book->amazon_new_price) ? '$' . $book->amazon_new_price : '(Unavailable)'?></td>
    </tr>
  </table>
  <div class="control-group">
    <label class="control-label" for="price-input">Your Price:</label>
    <div class="controls">
      <div class="input-prepend input-append">
        <span class="add-on">$</span>
        <input id="price-input" class="input-mini" type="text" name="price" value="<?php print set_value('price', $price); ?>" pattern="[0-9]*[05]" title="Multiples of $5 only, please!" required>
        <span class="add-on">.00</span>
      </div>
      <span class="help-block">
        For easier in-person transactions,<br>only multiples of $5 are allowed.
      </span>
    </div>
  </div>
</fieldset>

<fieldset>
  <legend>Give It A Personal Touch</legend>
  <div class="control-group">
    <label class="control-label" for="notes-input">Notes:</label>
    <div class="controls">
      <textarea id="notes-input" class="input-block-level" rows="3" name="notes"><?php print set_value('notes', $notes); ?></textarea>
      <span class="help-block">
        Add details about your book's condition, when/where you would prefer
        to meet buyers, and why you and/or your book are awesome. These notes
        will be displayed only to logged-in students (when they click on the <i class="icon-plus"></i>
        next to your post).
      </span>
    </div>
  </div>
</fieldset>

<div class="form-actions">
  <button type="submit" class="btn btn-primary"><?php print $button_label; ?></button>
</div>

</form>