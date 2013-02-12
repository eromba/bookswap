<div id = "postform">
	<?
		echo form_open('post_book','',array('submit'=>true));
	?>
	<div id = "priceInputDiv"><?
		$options = array (
		  'name'  => 'price',
		'id'    => 'price'
		);

		// Create our label for user_name.
		echo form_label('Price:  ', 'price');
		echo form_input($options);
	?></div>
	<div id = "conditionInputDiv"><?
		echo form_label('Condition:  ', 'condition');
		$options = array(
		                  'Very Good'  => 'Very Good',
		                  'Good'    => 'Good',
		                  'Ok'   => 'Ok',
		                  'Poor' => 'Poor',
		                );
		echo form_dropdown('condition', $options, 'Good');
	?></div>
	<div id = "notesInputDiv"><?
		$options = array (
		  'name'  => 'notes',
		'id'    => 'notes',
		'cols'=>'10',
		'rows'=>'5'
		);
		echo form_label('Notes:  ', 'notes');
		echo form_textarea($options);
	?></div>
	<div id = "editionInputDiv"><?

		echo form_label('Edition:  ', 'edition');
		if (isset($book)){
			$edition = $book->edition;
		}else{
			$edition = '0';
		}
		$options = array (
		  'name'  => 'edition',
		'id'    => 'edition',
		'value'=>$edition,
		'maxlength'=>'2',
		'size'=>'2'
		);
		echo form_input($options);
		// Close the form.
	?></div>
	<div id = "hiddenFields"><?
		if (isset($book)){
			$bid = $book->id;
		}else{
			$bid = '99999';
		}
		echo form_hidden("bid",$bid);
	?></div>
	<div id="submitInputDiv"><? 
	echo form_submit('submit', 'Submit');
	echo form_close();
	?></div>
</div>