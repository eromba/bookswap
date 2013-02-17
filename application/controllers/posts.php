<?php

class Posts extends BS_Controller {

  public function my_posts() {
    $user = $this->get_current_userdata();
    if ($user) {
      $data['user'] = $user;
      $data['posts'] = $this->post_model->get_posts_by_uid($user->uid);
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
    $data['title'] = 'Updated';
    $this->post_model->update_post();
    $this->load->view('header', $data);
    $this->load->view('footer', $data);
  }

  public function remove_post() {
    $data['title'] = 'Updated';
    echo $this->post_model->remove_post();
  }

}