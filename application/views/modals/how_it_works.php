<div id="how-it-works-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="How It Works" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Welcome to <?php print $site_name; ?></h3>
  </div>
  <div class="modal-body">
    <p><?php print $site_name; ?> connects you with other students on campus to help you save money on textbooks.</p>
    <h4>Buy a Book</h4>
    <ol>
      <li>Search for the book you want to buy.</li>
      <li>Compare book prices from other students, the university bookstore, and Amazon.com.</li>
      <li>To buy a book from another student, <a href="<?php print base_url('login'); ?>">log in with your NetID</a> and click on the "Contact Seller" button to send that student an email.</li>
      <li>Start a conversation with the seller, arrange the details, and get your book!</li>
    </ol>
    <h4>Sell a Book</h4>
    <ol>
      <li><a href="<?php print base_url('login'); ?>">Log in with your NetID</a>.</li>
      <li>Search for the book you want to sell.</li>
      <li>Click the "Sell Your Copy" button next to the appropriate book.</li>
      <li>Enter the required information and click "Submit".</li>
      <li>That's it! Other students can now see your post.</li>
    </ol>
    <p>You can modify or remove your posts at any time by visiting the <a href="<?php print base_url('myposts'); ?>">My Posts</a> page.</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
