<?php

class Posts extends BS_Controller {

  public function my_posts() {
    $user = $this->get_current_userdata();
    if ($user) {
      $data['user'] = $user;
      $data['posts'] = $this->post_model->get_posts(array('uid' => $user->uid));
      foreach ($data['posts'] as $post) {
        $post->book = $this->book_model->get_books_by_id($post->bid);
        $post->book = $post->book[0];
      }
    }
    $data['title'] = 'My Posts';
    $this->load->view('header', $data);
    $this->load->view('my_posts', $data);
    $this->load->view('footer', $data);
  }

  public function post_book() {
    $user = $this->get_current_userdata();
    $this->post_model->add_post($user->uid);
    $data['title'] = 'Posted';
    $data['notice'] = "Succesfully posted your book!";
    $this->load->view('header', $data);
    $this->load->view('notice', $data);
    $this->load->view('footer', $data);
  }

  public function update_post() {
    $this->post_model->update_post(array(
        'pid' => $this->input->post('pid'),
        'price' => $this->input->post('price'),
        'notes' => $this->input->post('notes'),
        'edition' => $this->input->post('edition'),
        'condition' => $this->input->post('condition'),
    ));
    $data['title'] = 'Updated';
    $this->load->view('header', $data);
    $this->load->view('footer', $data);
  }

  public function deactivate_post() {
    $pid = $this->input->post('post_id');
    $post = $this->post_model->get_posts(array('pid' => $pid));
    // Verify that the post exists
    if ($post) {
      $user = $this->session->userdata('bookswap_user');
      // Verify that the current user owns this post
      if ($post->uid == $user->uid) {
        echo $this->post_model->deactivate_post($pid);
      }
    }
  }

}