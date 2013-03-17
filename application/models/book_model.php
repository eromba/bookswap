<?php

class Book_model extends CI_Model {

  /**
   * Retrieves books from the database.
   *
   * @param array $options Array of query conditions
   * @return array result() Array of book objects, or a single book object if
   *                        a bid or isbn is specified
   */
  public function get_books($options = array()) {
    $valid_columns = array('bid', 'isbn');
    foreach ($valid_columns as $column) {
      if (isset($options[$column])) {
        $this->db->where($column, $options[$column]);
      }
    }

    $query = $this->db->get('books');

    $books = $query->result();

    foreach ($books as &$book) {
      $book->user_pid = NULL;
      $book->posts = $this->post_model->get_posts(array(
        'bid' => $book->bid,
        'active' => TRUE,
        'order_by' => 'price asc',
      ));
      foreach ($book->posts as $post) {
        $post->user = $this->user_model->get_users(array('uid' => $post->uid));
        if ($this->user && ($post->user->uid == $this->user->uid)) {
          $book->user_pid = $post->pid;
        }
      }
      $book->num_posts = count($book->posts);
      $book->min_student_price = $this->post_model->get_min_price($book->bid);
      $book->min_store_price = min($book->bookstore_price, $book->amzn_new_price);
      $book->num_store_offers = 0;
      if ($book->bookstore_price != 0) {
        $book->num_store_offers++;
      }
      if ($book->amzn_new_price != 0) {
        $book->num_store_offers++;
      }
    }

    if (isset($options['bid']) || isset($options['isbn'])) {
      return $books[0];
    }
    else {
      return $books;
    }
  }

  /**
   * Retrieves books from the database that match the given string.
   *
   * @param string $string The string to search by
   * @return array Array of book objects
   */
  public function get_books_by_string($string) {
    $this->db->like('subj_class', $string);
    $this->db->or_like('title', $string);
    $this->db->or_like('subject', $string);
    return $this->get_books();
  }

  /**
   * Retrieves books from the database that match the given ISBN.
   *
   * @param string $isbn The ISBN to search by
   * @return array Array of book objects
   */
  public function get_books_by_isbn($isbn) {
    $query = $this->db->get_where('books', array('isbn' => $isbn));
    return $query->result();
  }

  /**
   * Returns the ISBNs of books whose Amazon data needs to be updated.
   *
   * @return array Array of ISBN strings (at most 10)
   */
  public function get_books_to_update() {
    $this->db->select('isbn');
    $this->db->distinct();
    $one_day_ago = time() - (24 * 60 * 60);
    $this->db->where("UNIX_TIMESTAMP(amzn_last_update) < $one_day_ago");
    $this->db->order_by('amzn_last_update', 'asc');
    $this->db->limit(10);
    $query = $this->db->get('books');
    $results = $query->result();
    $isbns = array();
    foreach ($results as $result) {
      $isbns[] = $result->isbn;
    }
    return $isbns;
  }

  /**
   * Batch updates Amazon data for multiple books in the database.
   *
   * At minimum, the $book_details array must specify the ISBNs of the books to update.
   *
   * @param array $book_details Array of columns => values arrays to be saved to the database
   * @return int affected_rows() Number of rows updated, or false on error
   */
  public function update_amazon_data($book_details) {
    $data = array();
    $valid_columns = array('isbn', 'title', 'image_url', 'amzn_link', 'amzn_list_price', 'amzn_new_price', 'amzn_used_price');
    foreach ($book_details as $amazon_book) {
      $row = array();
      foreach ($valid_columns as $column) {
        if (isset($amazon_book[$column])) {
          $row[$column] = $amazon_book[$column];
        }
      }
      $row['amzn_last_update'] = date('Y-m-d H:i:s');
      $data[] = $row;
    }
    $this->db->update_batch('books', $data, 'isbn');
    return $this->db->affected_rows();
  }

}
