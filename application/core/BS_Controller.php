<?php

class BS_Controller extends CI_Controller {

  /**
   * Whether the current request should be saved as the last-visited page in
   * the user's session data.
   * @access protected
   * @var bool
   */
  protected $set_last_page = TRUE;

  public function __construct() {
    parent::__construct();
    $this->config->load('bookswap');
    $this->user = $this->session->userdata('bookswap_user');

    // Give all views access to user-related variables
    // and configured UI strings.
    $this->load->vars(array(
      'user' => $this->user,
      'logged_in' => ($this->user != NULL),
      'book_conditions' => $this->config->item('book_conditions'),
    ));
    $this->load->vars($this->config->item('ui_strings'));
  }

  public function get_last_page() {
    if ($this->session->userdata('last_page')) {
      return $this->session->userdata('last_page');
    }
    else {
      return base_url();
    }
  }

  public function render_page($view, $data = array(), $modals = array()) {
    $ui_strings = $this->config->item('ui_strings');
    $title_prefix = $ui_strings['site_name'] . ' | ';
    if (isset($data['head_title'])) {
      $data['head_title'] = $title_prefix . $data['head_title'];
    }
    elseif (isset($data['title'])) {
      $data['head_title'] = $title_prefix . $data['title'];
    }
    else {
      $data['head_title'] = $title_prefix . $ui_strings['university_name'];
    }

    $body_classes = ($this->user != NULL) ? 'logged-in' : 'logged-out';
    $body_classes .= ' ' . str_replace('_', '-', $view);
    $data['body_classes'] = $body_classes;

    $data['is_front_page'] = ($view == 'front');

    $data['search_bar'] = $this->load->view('search_bar', $data, TRUE);
    $data['navbar']     = $this->load->view('navbar', $data, TRUE);
    $data['header']     = $this->load->view('header', $data, TRUE);
    $data['footer']     = $this->load->view('footer', $data, TRUE);

    $data['modals'] = '';
    $modals[] = 'about';
    $modals[] = 'login';
    foreach ($modals as $modal) {
      $data['modals'] .= $this->load->view('modals/' . $modal, $data, TRUE);
    }

    $messages = $this->session->userdata('messages');
    if ($messages) {
      $data['messages'] = $this->load->view('messages', array('messages' => $messages), TRUE);
    }
    else {
      $data['messages'] = '';
    }
    $this->session->unset_userdata('messages');

    $data['content'] = $this->load->view($view, $data, TRUE);

    $this->load->view('html', $data);
  }

  public function set_message($message, $type = 'success') {
    $messages = $this->session->userdata('messages');
    if ( ! $messages) { 
      $messages = array();
    }
    $messages[] = array(
      'text' => $message,
      'type' => $type,
    );
    $this->session->set_userdata('messages', $messages);
  }

  /**
   * Saves the current URL in session data before echoing the page output.
   *
   * @see http://ellislab.com/codeigniter/user-guide/general/controllers.html#output
   */
  public function _output($output) {
    if ($this->set_last_page) {
      $url = current_url();
      if ($_SERVER['QUERY_STRING']) {
        $url = $url . '?' . $_SERVER['QUERY_STRING'];
      }
      $this->session->set_userdata('last_page', $url);
    }
    echo $output;
  }

}
