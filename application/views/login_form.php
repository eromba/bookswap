<?php
	echo form_open('login',array('class'=> 'navbar-form pull-right','id'=>'login-form'),array('login'=>"TRUE",'current_page'=> current_url()));

	$options = array (
	  'name'  => 'username',
	'class'    => 'span2',
	'placeholder'=>'NetID'
	);
	echo form_input($options);
	
	$options = array (
	  'name'  => 'password',
	'id'    => 'password',
	'class'=>'span2',
	'placeholder'=>'Password',
	);
	echo form_password($options);

	$options = array (
          'class'  => 'btn',
          'value' =>'Login',
          'name' =>'submit'
    );
	echo form_submit($options);

	echo form_close();
?>
