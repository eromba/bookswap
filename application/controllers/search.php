<?php

class Search extends BS_Controller {

  public function index() {
    $data['title'] = 'Home';
    $this->render_page('index', $data);
  }

  public function results($query = "") {
    if ($this->input->post('q') != NULL) {
      redirect(base_url() . 'search/' . $this->input->post('q'));
    }
    $query = urldecode($query);
    if (empty($query)) {
      redirect($this->get_last_page());
    }

    $books = $this->fetch_books($query);
    foreach ($books as $book) {
      if ($book->stock >= 1) {
        $book->posts = $this->post_model->get_active_posts(array('bid' => $book->bid));
        foreach ($book->posts as $post) {
          $post->user = $this->user_model->get_users(array('uid' => $post->uid));
        }
        $book->min_price = $this->post_model->get_min_price($book->bid);
      }
      else {
        $book->posts = array();
      }
    }

    $data = array(
      'query' => $query,
      'books' => $books,
      'title' => 'Results',
    );
    $this->render_page('search_results', $data);
  }

  /**
   * Fetches books from the database that match the given query.
   *
   * @param string $query The search query
   * @return array Array of book objects
   */
  private function fetch_books($query) {
    $isbn = $this->get_isbn($query);
    if ($isbn) {
      $results = $this->book_model->get_books_by_isbn($isbn);
    }
    else {
      $results = $this->book_model->get_books_by_string($query);
    }
    return $results;
  }

  /**
   * Determines if the given query is a valid ISBN.
   *
   * @param string $query The query to be evaluated
   * @return integer The integer value of the ISBN, or FALSE
   */
  private function get_isbn($query) {
    $isbn = str_replace(array(' ', '-'), '', $query);
    if (preg_match('/^(97(8|9))?\d{9}(\d|X)$/', $isbn)) {
      return intval($isbn);
    }
    return FALSE;
  }

}
