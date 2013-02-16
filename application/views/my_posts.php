
<div id="main">
    <div id="results">
    	<?php  $my_posts_id_count=0;
    	if (count($posts)==0){?>
    		<div class="result span8 offset2">
    			You have no posts! Search for a book to post now.
    		</div>
    	<?php }
    	foreach ($posts as $post){
        $book = $post->book;?>
        <div id = "postid_<?php echo $post->id;?>" class="result span8 offset2">
        	<button class="btn-danger btn-mini btn post-del-btn" href="#deleteconfirm-modal" role="button" data-toggle="modal"> <i class="icon-remove-sign"></i> </button>
      	<div class="book">
    	    <h3 class="title"><?php echo($book->title);?></h3>
    		<div class="cover">
            	<img src="<?php echo base_url();?>img/book-covers/<?php echo($book->isbn);?>.jpg" class="img-polaroid" alt="" />
          	</div>
			<div class="info">
	            <div class="book-info span3">
	              <div class="by-line">By <span class="author"><?php echo($book->authors);?></span></div>
	              <div class="edition">ISBN: <?php echo($book->isbn);?></div>
	              <div class="edition"><?php echo($book->edition);?> Edition</div>
	              <div class="book-course">Required for: <?php echo($book->subject . " " . $book->course);?></div>
	            </div>
	            <div class="span3 price-info">
	              <div class="store-price">Norris Bookstore Price: <?php echo($book->bookstore_price);?></div>
	              <div class="amzn-list">Amazon List Price: <?php echo($book->amzn_list_price);?></div>
	              <div class="amzn-new">Amazon New Price: <?php echo($book->amzn_new_price);?></div>
	              <div class="amzn-used">Amazon Used Price: <?php echo($book->amzn_used_price);?></div>
	              <div class="amzn-link"><a href="<?php echo($book->amzn_link);?>">Buy on Amazon</a></div>
	            </div>
			</div>
	        <div class="edit-yours button-box">
	            <span>Want to edit your post?</span>
	            <button class="btn-success btn" data-toggle="collapse" data-target="#postform-container-<?php echo($my_posts_id_count);?>">Click Here</button>
	            <div id="postform-container-<?php echo($my_posts_id_count);?>"  class="accordion-body collapse">
	             	<?php populated_post_form($post);?>
	            </div>
	        </div>
	    </div>
	    </div>
    	<?php $my_posts_id_count+=1;}?>
  </div>
</div>


<!-- Modal -->
<div id="deleteconfirm-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Confirm Deletion</h3>
  </div>
  <div class="modal-body">
    <p>Are you sure you want to delete this posting?</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
    <button id = "delete-confirm-button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Yes</button>
  </div>
</div>


<?php  
function populated_post_form($post){
?><div id = "postform">
	<?php
		echo form_open('update_post','',array('submit'=>true));
	?>
	<div id = "priceInputDiv"><?php
		$options = array (
		  'name'  => 'price',
		'id'    => 'price',
		'value'=> $post->price
		);

		// Create our label for user_name.
		echo form_label('Price:  ', 'price');
		echo form_input($options);
	?></div>
	<div id = "conditionInputDiv"><?php
		echo form_label('Condition:  ', 'condition');
		$options = array(
		                  'Very Good'  => 'Very Good',
		                  'Good'    => 'Good',
		                  'Ok'   => 'Ok',
		                  'Poor' => 'Poor',
		                );
		echo form_dropdown('condition', $options, $post->condition);
	?></div>
	<div id = "notesInputDiv"><?php
		$options = array (
		  'name'  => 'notes',
		'id'    => 'notes',
		'cols'=>'10',
		'rows'=>'5',
		'value'=>$post->notes
		);
		echo form_label('Notes:  ', 'notes');
		echo form_textarea($options);
	?></div>
	<div id = "editionInputDiv"><?php

		echo form_label('Edition:  ', 'edition');
		$options = array (
		  'name'  => 'edition',
		'id'    => 'edition',
		'value'=>$post->edition,
		'maxlength'=>'2',
		'size'=>'2'
		);
		echo form_input($options);
		// Close the form.
	?></div>
	<div id = "hiddenFields"><?php
		$pid = $post->id;
		echo form_hidden("pid",$pid);
	?></div>
	<div id="submitInputDiv"><?php  
	echo form_submit('submit', 'Update');
	echo form_close();
	?></div>
</div><?php
  }
?>