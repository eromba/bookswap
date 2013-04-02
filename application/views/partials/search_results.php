<?php foreach ($books as $book) { ?>
  <div id="book-<?php print $book->bid; ?>" class="entity book search-result">

    <div class="cover-image">
      <?php if ($book->image_url) { ?>
        <img src="<?php print $book->image_url; ?>" class="img-polaroid" />
      <?php } else { ?>
        <div class="no-image img-polaroid"><span>No Image Available</span></div>
      <?php } ?>
    </div>

    <div class="book-content">

      <h3 class="title"><?php print $book->title;?></h3>

      <div class="details clearfix">
        <div class="fields">
          <?php if ($book->authors) { ?>
            <div class="by-line field">
              By <span class="author"><?php print $book->authors;?></span>
            </div>
          <?php } ?>
          <?php if ($book->edition) { ?>
            <div class="edition field">
              <span class="field-label">Edition:</span>
              <span class="field-value"><?php print $book->edition;?></span>
            </div>
          <?php } ?>
          <?php if ($book->publisher) { ?>
            <div class="edition field">
              <span class="field-label">Publisher:</span>
              <span class="field-value"><?php print $book->publisher;?></span>
            </div>
          <?php } ?>
          <?php if ($book->isbn) { ?>
          <div class="isbn field">
            <span class="field-label">ISBN:</span>
            <span class="field-value"><?php print $book->isbn;?></span>
          </div>
          <?php } ?>
        </div>
        <div class="courses">
          <?php foreach ($book->courses as $course_type => $courses) { ?>
          <?php if ($courses) { ?>
            <?php print $course_type; ?> for:
            <ul>
              <?php foreach ($courses as $course) { ?>
                <li class="course"><?php print $course->name . ', Section ' . $course->section;?></li>
              <?php } ?>
            </ul>
            <?php } ?>
          <?php } ?>
        </div>
      </div>

      <div class="sell-action">
        <?php if ($logged_in) { ?>
          <?php if ($book->user_pid) { ?>
            <span>You are selling a copy of this book</span>
            <a class="btn" href="<?php print base_url('my-posts/' . $book->user_pid); ?>" role="button"><i class="icon-pencil"></i> Edit Your Post</a>
          <?php } else { ?>
            <span>Have one to sell?</span>
            <a class="btn sell-your-copy" href="<?php print base_url('sell/' . $book->bid); ?>" role="button"><i class="icon-book"></i> Sell Your Copy</a>
          <?php } ?>
        <?php } else { ?>
          <span>Have one to sell?</span>
          <a class="btn sell-your-copy login" href="#"><i class="icon-user"></i> Log in to sell your copy</a>
        <?php } ?>
      </div>

      <div class="offers">
        <ul class="nav nav-tabs">
          <li class="student-offers active">
            <a href="#book-<?php print $book->bid;?>-student-offers" data-toggle="tab">
              <?php if ($book->num_posts > 0) { ?>
                <span class="quantity"><?php print $book->num_posts; ?></span> @ <?php print $university_abbr; ?> from <span class="min-price">$<?php print $book->min_student_price; ?></span>
              <?php } else { ?>
                <span class="quantity">0</span> copies @ <?php print $university_abbr; ?>
              <?php } ?>
            </a>
          </li>
          <li class="store-offers">
            <a href="#book-<?php print $book->bid;?>-store-offers" data-toggle="tab">
              <?php if ($book->num_store_offers > 0) { ?>
                <span class="quantity"><?php print $book->num_store_offers; ?></span> New from <span class="min-price">$<?php print $book->min_store_price; ?></span>
              <?php } else { ?>
                <span class="quantity">0</span> new copies
              <?php } ?>
            </a>
          </li>
        </ul>
        <div class="tab-content">
          <div id="book-<?php print $book->bid; ?>-student-offers" class="student-offers tab-pane active">
            <?php if ($book->num_posts > 0) { ?>
              <table class="table">
                <thead>
                  <tr>
                    <?php if ($logged_in) { ?>
                      <th class="more-info-button"></th>
                      <th class="seller">Seller</th>
                    <?php } ?>
                    <th class="edition">Edition</th>
                    <th class="price">Price</th>
                    <th class="condition">Condition</th>
                    <th class=""action"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($book->posts as $post) { ?>
                    <?php $post_details_id = "book-$book->bid-post-$post->pid-details"; ?>
                    <tr id="book-<?php print $book->bid; ?>-post-<?php print $post->pid; ?>">
                      <?php if ($logged_in) { ?>
                        <td class="more-info-button">
                          <?php if ($post->notes) { ?>
                            <a href="#<?php print $post_details_id;?>" data-toggle="collapse" data-target="#<?php print $post_details_id;?>"><i class="icon-plus"></i></a>
                          <?php } ?>
                        </td>
                        <td class="seller"><?php print $post->user->first_name; ?></td>
                      <?php } ?>
                      <td class="edition"><?php print htmlentities(($post->edition) ? $post->edition : $book->edition); ?></td>
                      <td class="price">$<?php print $post->price; ?></td>
                      <td class="condition"><?php print $book_conditions[$post->condition]; ?></td>
                      <td class="action">
                        <?php if ($logged_in) { ?>
                          <?php if ($post->uid == $user->uid) { ?>
                            <a class="btn" href="<?php print base_url('my-posts/' . $post->pid); ?>"><i class="icon-pencil"></i> Edit Post</a>
                          <?php } else { ?>
                            <a class="btn btn-success" href="mailto:<?php print $post->user->email; ?>?subject=<?php print $site_name; ?>: I'd like to buy your book!&body=Hi <?php print $post->user->first_name; ?>,%0D%0A%0D%0AI'd like to buy your copy of <?php print $book->title; ?>.%0D%0A%0D%0ACheers,%0D%0A%0D%0A<?php print $user->first_name; ?>"><i class="icon-envelope icon-white"></i> Contact Seller</a>
                          <?php } ?>
                        <?php } else { ?>
                          <a class="btn btn-info login" href="#"><i class="icon-user icon-white"></i> Log in for more info</a>
                        <?php } ?>
                      </td>
                    </tr>
                    <?php if ($logged_in && $post->notes) { ?>
                      <tr class="more-info">
                        <td></td>
                        <td class="more-info" colspan="5">
                          <div id="<?php print $post_details_id; ?>" class="accordion-body collapse">
                            <?php if ($post->notes) { ?>
                              <h4><?php print $post->user->first_name; ?> says:</h4>
                              <p class="notes"><?php print htmlentities($post->notes);?></p>
                            <?php } ?>
                          </div>
                        </td>
                      </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            <?php } else { ?>
              <div class="no-offers">
                <p>No students are selling this book right now.</p>
              </div>
            <?php }?>
          </div>
          <div id="book-<?php print $book->bid; ?>-store-offers" class="store-offers tab-pane">
            <table class="table">
              <thead>
                <tr>
                  <th class="seller">Seller</th>
                  <th class="new-price">New Price</th>
                  <th class="new-price">Used Price</th>
                  <th class="action"></th>
                </tr>
              </thead>
              <tbody>
                <tr class="bookstore-offer">
                  <td class="seller"><?php print $bookstore_name; ?></td>
                  <?php if ($book->bookstore_new_price) { ?>
                    <td class="new-price">$<?php print $book->bookstore_new_price; ?></td>
                  <?php } else { ?>
                    <td class="new-price not-available">(Not available)</td>
                  <?php } ?>
                  <?php if ($book->bookstore_used_price) { ?>
                    <td class="used-price">$<?php print $book->bookstore_used_price; ?></td>
                  <?php } else { ?>
                    <td class="used-price not-available">(Not available)</td>
                  <?php } ?>
                  <?php if ($book->bookstore_new_price || $book->bookstore_used_price) { ?>
                    <td class="action">
                      <a class="btn" href="<?php print $bookstore_url; ?>"><i class="icon-shopping-cart"></i> Buy from Bookstore</a>
                    </td>
                  <?php } else { ?>
                    <td class="action">
                      <a class="btn disabled" href="#"><i class="icon-shopping-cart"></i> Buy from Bookstore</a>
                    </td>
                  <?php } ?>
                </tr>
                <tr class="amazon-offer">
                  <td class="seller">Amazon.com</td>
                  <?php if ($book->amazon_new_price != 0) { ?>
                    <td class="new-price">$<?php print $book->amazon_new_price; ?></td>
                  <?php } else { ?>
                    <td class="new-price not-available">(Not available)</td>
                  <?php } ?>
                  <td class="used-price"></td>
                  <?php if ($book->amazon_new_price || $book->amazon_used_price) { ?>
                    <td class="action">
                      <a class="btn" href="<?php print $book->amazon_url; ?>"><i class="icon-shopping-cart"></i> Buy from Amazon</a>
                    </td>
                  <?php } else { ?>
                    <td class="action">
                      <a class="btn disabled" href="#"><i class="icon-shopping-cart"></i> Buy from Amazon</a>
                    </td>
                  <?php } ?>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

  </div>
<?php } ?>
