<?php

class Search extends BS_Controller {

  public function index() {
    $data['title'] = 'Home';
    $this->render_page('index', $data);
  }

  public function results($q = "") {
    if ($this->input->post('q') != NULL) {
      redirect(base_url() . 'search/' . $this->input->post('q'));
    }
    $q = urldecode($q);
    $data['q'] = $q;
    $data['books'] = $this->fetch_results($q);
    $data['title'] = "Results";
    foreach ($data['books'] as $book) {
      if ($book->stock >= 1) {
        $book->posts = $this->post_model->get_active_posts(array('bid' => $book->bid));
        foreach ($book->posts as $post) {
          $post->sellerdata = $this->user_model->get_users(array('uid' => $post->uid));
        }

        $book->from = $this->post_model->get_min_price($book->bid);
      } else {
        $book->posts = array();
      }
    }
    $this->render_page('search_results', $data);
  }

  public function fetch_results($q) {
    if (intval($q) > 999) {
      $results = $this->book_model->get_books_by_isbn($q);
    } else {
      $results = $this->book_model->get_books_by_string($q);
    }

    if ($q == NULL) {
      $results = $this->book_model->get_books();
    }
    return $results;
  }

}