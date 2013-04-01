<?php

class User_model extends BS_Model {

  public $primary_key = 'uid';

  public $required_columns = array(
    'netid',
    'email',
    'first_name',
  );

}
