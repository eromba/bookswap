<?php

class User_model extends CI_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database();
  }

  public function add_user($data) {
    return ($this->db->insert('users', $data));
  }

  public function get_user($netid) {
    $query = $this->db->get_where('users', array('netid' => $netid));
    return $query->result();
  }

}