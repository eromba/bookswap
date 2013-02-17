<?php
	echo form_open('search',array('class'=>"navbar-form pull-left"),array('submit'=>true));
	$options = array (
		'name'=>'q',
		'id'    => 'query',
		'class'=>'search-query span2',
		'Placeholder'=>'Search'
	);

		// Create our label for user_name.
		echo form_input($options); 
	$options = array(
		'class' => 'btn btn-small',
		'id' => 'searchsubmit',
		'name'=>'search',
		'value'=>'Search'
	);
	echo form_submit($options);
	echo form_close();
?>