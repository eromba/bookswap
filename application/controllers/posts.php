<?php

class Posts extends BS_Controller {

  public function __construct() {
    parent::__construct();
    if ( ! $this->user) {
      show_error('Please <a href="' . base_url('login') . '">log in</a> to access this page.', 401);
    }
  }

  public function user_posts() {
    $active_posts = array();
    $deactivated_posts = array();
    $posts = $this->post_model->get_posts(array('uid' => $this->user->uid));
    foreach ($posts as $post) {
      $post->book = $this->book_model->get_books(array('bid' => $post->bid));
      if ($post->active) {
        $active_posts[] = $post;
      }
      else {
        $deactivated_posts[] = $post;
      }
    }
    $data = array(
      'title' => 'My Posts',
      'active_posts' => $this->load->view('posts', array('posts' => $active_posts), TRUE),
      'deactivated_posts' => $this->load->view('posts', array('posts' => $deactivated_posts), TRUE),
    );
    $modals = array('deactivate_post');
    $this->render_page('user_posts', $data, $modals);
  }

  public function create_post($bid) {
    if ( ! $this->validate_post_form()) {
      $data = array(
        'title' => 'Sell Your Copy',
        'button_label' => 'Submit Post',
      );
      $this->render_post_form($bid, $data);
    }
    else {
      $this->post_model->add_post(array(
        'bid'       => $bid,
        'uid'       => $this->user->uid,
        'edition'   => $this->input->post('edition'),
        'condition' => $this->input->post('condition'),
        'price'     => $this->input->post('price'),
        'notes'     => $this->input->post('notes'),
      ));
      $ui_strings = $this->config->item('ui_strings');
      $site_name = $ui_strings['site_name'];
      $this->set_message("Your book has been posted. Thanks for using $site_name!");
      redirect(base_url('my-posts'));
    }
  }

  public function update_post($pid) {
    $post = $this->post_model->get_posts(array('pid' => $pid));
    if ( ! $post) {
      show_error('The post you are trying to edit does not exist.', 404);
    }
    if ($post->uid != $this->user->uid) {
      show_error('You do not have permission to edit this post.', 403);
    }
    if ( ! $this->validate_post_form()) {
      $data = array(
        'title' => 'Edit Post',
        'button_label' => 'Update Post',
      );
      $this->render_post_form($post->bid, $data, $post);
    }
    else {
      $this->post_model->update_post(array(
        'pid'       => $pid,
        'edition'   => $this->input->post('edition'),
        'condition' => $this->input->post('condition'),
        'price'     => $this->input->post('price'),
        'notes'     => $this->input->post('notes'),
      ));
      $this->set_message('Your post has been updated.');
      redirect(base_url('my-posts'));
    }
  }

  /**
   * Validates condition input values.
   *
   * @param string $str
   * @return bool
   */
  public function validate_condition($str) {
    if ( ! in_array(intval($str), array_keys($this->config->item('book_conditions')))) {
      $this->form_validation->set_message('validate_condition', 'You entered an invalid value for the %s field.');
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Validates price input values.
   *
   * @param string $str
   * @return bool
   */
  public function validate_price($str) {
    if (preg_match('/^[0-9]*[05]$/', $str) !== 1) {
      $this->form_validation->set_message('validate_price', 'Please enter a price that is a multiple of $5.');
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Validates the create/update post form.
   */
  private function validate_post_form() {
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('', '');
    $this->form_validation->set_rules(array(
      array(
        'field' => 'edition',
        'label' => 'Edition',
        'rules' => 'trim|xss_clean',
      ),
      array(
        'field' => 'condition',
        'label' => 'Condition',
        'rules' => 'trim|required|callback_validate_condition',
      ),
      array(
        'field' => 'price',
        'label' => 'Price',
        'rules' => 'trim|required|callback_validate_price',
      ),
      array(
        'field' => 'notes',
        'label' => 'Notes',
        'rules' => 'trim|xss_clean',
      ),
    ));
    return $this->form_validation->run();
  }

  /**
   * Renders the create/update post form.
   *
   * @param int $bid The ID of the book that this post is associated with
   * @param array $data Associative array of view variables
   * @param stdClass $post (Optional) The post being edited
   */
  private function render_post_form($bid, $data, $post = NULL) {
    $book =  $this->book_model->get_books(array('bid' => $bid));
    if ( ! $book) {
      show_error('The book you are trying to sell does not exist.', 404);
    }
    $data['book'] = $book;

    $fields = array('edition', 'condition', 'price', 'notes');
    foreach ($fields as $field) {
      // Define a view variable for each field's default value.
      $data[$field] = ($post) ? $post->$field : '';

      // Display each field's form-validation errors in the messages area.
      $error_message = form_error($field);
      if ($error_message) {
        $this->set_message($error_message, 'error');
      }
    }

    $this->render_page('post_form', $data);
  }

}