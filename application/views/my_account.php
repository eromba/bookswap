<div id = "accountInfo" class ="offset1 account-settings">
	<?php
		echo form_open('updateaccount','',array('submit'=>true));
		//have:netid, email, nuemail, phone, major
		//if we can pull: name and nuemail (major?)
		//should show: netid, name, email- text box, phone- textbox
	?>
	<div id = "usernetid"><?php
		// Create our label for user_name.
		//print net id and name
	?></div>
	<div id = "userEmail"><?php
		echo form_label('Email:  ', 'email');
		//get phone
		$options = array (
		  'name'  => 'userEmail',
		'id'    => 'userEmail',
		'value'=>$userdata->email,
		);

		echo form_input($options);
	?></div>
	<div id = "userPhone"><?php
		//get email
		$options = array (
		  'name'  => 'userPhone',
		'id'    => 'userPhone',
		'value'=>$userdata->phone,
		);
		echo form_label('Phone:  ', 'userPhone');
		echo form_input($options);
	?></div>
	<div id="submitInputDiv"><?php  
	echo form_submit('submit', 'Submit');
	echo form_close();
	?></div>
</div>