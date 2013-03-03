<?php

class Book_model extends CI_Model {

  /**
   * Retrieves books from the database that match the given string.
   *
   * @param string $string The string to search by
   * @return array Array of book objects
   */
  public function get_books_by_string($string) {
    $this->db->from('books');
    $query = $this->db->like('subj_class', $string);
    $query = $this->db->or_like('title', $string);
    $query = $this->db->or_like('subject', $string);
    $query = $this->db->get();
    return $query->result();
  }

  /**
   * Retrieves books from the database that match the given ISBN.
   *
   * @param integer $isbn The ISBN to search by
   * @return array Array of book objects
   */
  public function get_books_by_isbn($isbn) {
    $query = $this->db->get_where('books', array('isbn' => $isbn));
    return $query->result();
  }

  public function update_amazon_data($book, $uptitle) {
    $this->db->where('isbn', $book->isbn);
    if ($book->image_url)
      $this->db->set('image_url', $book->image_url, FALSE);
    if ($book->amzn_used_price)
      $this->db->set('amzn_used_price', $book->amzn_used_price, FALSE);
    if ($book->amzn_new_price)
      $this->db->set('amzn_new_price', $book->amzn_new_price, FALSE);
    if ($book->amzn_list_price)
      $this->db->set('amzn_list_price', $book->amzn_list_price, FALSE);
    $this->db->set('amzn_updated_at', 'NOW()', FALSE);
    if ($uptitle)
      $this->db->set('title', "$book->title");
    $this->db->set('amzn_link', "$book->amzn_link");
    $this->db->update('books');
  }

}
