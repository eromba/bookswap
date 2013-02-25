<?php

class Posts extends BS_Controller {

  public function my_posts() {
    if ($this->user) {
      $data['user'] = $this->user;
      $data['posts'] = $this->post_model->get_posts(array('uid' => $this->user->uid));
      foreach ($data['posts'] as $post) {
        $post->book = $this->book_model->get_books_by_id($post->bid);
        $post->book = $post->book[0];
      }
    }
    $data['title'] = 'My Posts';
    $this->render_page('my_posts', $data);
  }

  public function post_book() {
    if ($this->user) {
      // Ensure price is an integer.
      // (e.g. Turn "$10.25" into 10.)
      $price = intval(preg_replace('/[^\d^\.]/', '', $this->input->post('price')));

      $data = array(
          'bid' => $this->input->post('bid'),
          'uid' => $this->user->uid,
          'price' => $price,
          'notes' => $this->input->post('notes'),
          'edition' => $this->input->post('edition'),
      );
      $this->post_model->add_post($data);
    }
    $data['title'] = 'Posted';
    $data['notice'] = "Succesfully posted your book!";
    $this->render_page('notice', $data);
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
    $data['notice'] = "Succesfully updated your book!";
    $this->render_page('notice', $data);
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