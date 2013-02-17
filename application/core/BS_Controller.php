<?php

class BS_Controller extends CI_Controller {

  public function __construct() {
    parent::__construct();

    $this->load->model('book_model');
    $this->load->model('post_model');
    $this->load->model('user_model');
    $this->load->helper('form');
    $this->load->helper('url');
    $this->load->library('session');

    $this->seller = $this->get_current_userdata();
    if ($this->seller) {
      $this->seller = $this->seller->netid;
    }
  }

  public function get_current_userdata() {
    $user = $this->session->userdata('bookswap_user');
    if (is_array($user)) {
      return $user[0];
    } else {
      return $user;
    }
  }

}