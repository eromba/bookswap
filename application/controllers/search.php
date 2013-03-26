<?php

class Search extends BS_Controller {

  public function index() {
    $modals = array('how_it_works');
    $this->render_page('front', array(), $modals);
  }

  public function results() {
    $query = $this->input->get('q', TRUE);
    if (empty($query)) {
      redirect($this->get_last_page());
    }

    $books = $this->fetch_books($query);

    $query_html = htmlentities($query);
    $data = array(
      'head_title' => $query_html,
      'query' => $query_html,
      'books' => $books,
      'num_results' => count($books),
    );
    $data['results'] = $this->load_partial('search_results', $data);

    $this->render_page('search', $data);
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
      $results = array( $this->book_model->get_books(array('isbn' => $isbn)) );
    }
    else {
      $results = $this->book_model->get_books_by_string($query);
    }
    return $results;
  }

  /**
   * Determines if the given query string is a valid ISBN.
   *
   * @param string $query The query string to be evaluated
   * @return string The ISBN, or FALSE if the ISBN is invalid
   */
  private function get_isbn($query) {
    $isbn = str_replace(array(' ', '-'), '', $query);
    if (preg_match('/^(97(8|9))?\d{9}(\d|X)$/', $isbn)) {
      return $isbn;
    }
    return FALSE;
  }

}
