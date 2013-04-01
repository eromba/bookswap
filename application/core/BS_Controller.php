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

    $this->load->model('book_model', 'books');
    $this->load->model('post_model', 'posts');
    $this->load->model('user_model', 'users');

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

  /**
   * Loads the given partial view.
   * 
   * @param string $modal The name of the partial view to load
   * @param array $data (optional) Data to pass to the view
   * @return string The rendered partial view
   */
  public function load_partial($partial, $data = array()) {
    return $this->load->view('partials/' . $partial, $data, TRUE);
  }

  public function render_page($view, $data = array(), $modals = array()) {
    $this->load->helper('form');

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

    $data['search_bar'] = $this->load_partial('search_bar', $data);
    $data['navbar']     = $this->load_partial('navbar', $data);
    $data['header']     = $this->load_partial('header', $data);
    $data['footer']     = $this->load_partial('footer', $data);

    $data['modals'] = '';
    $modals[] = 'about';
    $modals[] = 'login';
    foreach ($modals as $modal) {
      $data['modals'] .= $this->load->view('modals/' . $modal, $data, TRUE);
    }

    $messages = $this->session->userdata('messages');
    if ($messages) {
      $data['messages'] = $this->load_partial('messages', array('messages' => $messages));
    }
    else {
      $data['messages'] = '';
    }
    $this->session->unset_userdata('messages');

    $data['content'] = $this->load->view('pages/' . $view, $data, TRUE);

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
