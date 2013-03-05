<?php

class BS_Controller extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->config->load('bookswap');
    $this->user = $this->session->userdata('bookswap_user');
  }

  public function get_last_page() {
    if ($this->session->userdata('last_page')) {
      return $this->session->userdata('last_page');
    }
    else {
      return base_url();
    }
  }

  public function render_page($view, $data) {
    $data['user'] = $this->user;
    $data['logged_in'] = ($this->user != NULL);

    $this->load->view('header', $data);
    $this->load->view($view, $data);
    $this->load->view('footer', $data);
  }

  /**
   * Saves the current URL in session data before echoing the page output.
   *
   * @see http://ellislab.com/codeigniter/user-guide/general/controllers.html#output
   */
  public function _output($output) {
    $this->session->set_userdata('last_page', current_url());
    echo $output;
  }

}