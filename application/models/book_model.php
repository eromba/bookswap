<?php

class Book_model extends CI_Model {

  /**
   * Your Amazon Access Key Id
   * @access private
   * @var string
   */
  private $public_key = "";

  /**
   * Your Amazon Secret Access Key
   * @access private
   * @var string
   */
  private $private_key = "";

  /**
   * Your Amazon Associate Tag
   * Now required, effective from 25th Oct. 2011
   * @access private
   * @var string
   */
  private $associate_tag = "";

  public function get_books() {
    $query = $this->db->get('books');
    return $query->result();
  }

  public function get_books_by_all($q) {
    $this->db->from('books');
    $query = $this->db->like('subj_class', $q);
    $query = $this->db->or_like('title', $q);
    $query = $this->db->or_like('subject', $q);
    $query = $this->db->get();
    return $query->result();
  }

  public function get_books_by_isbn($isbn) {
    $query = $this->db->get_where('books', array('isbn' => $isbn));
    return $query->result();
  }

  public function get_min_price($bid) {
    $this->db->select_min('price');
    $query = $this->db->get_where('posts', array('bid' => $bid));
    // Produces: SELECT MIN(age) as age FROM members
    //$query = $this->db->get_where('posts', array('bid' => $bid,'price <'=>$price));
    return $query->result();
  }

  public function have_cover($isbn) {
    $this->db->where('isbn', $isbn);
    $this->db->set('have_cover', 1);
    $this->db->update('books');
  }

  public function get_amazon_from_isbn($isbn) {
    require('aws_signed_request.php');
    //require('amazon_api_class.php');
    $parameters = array("Operation" => "ItemLookup",
        "ItemId" => $isbn,
        "SearchIndex" => "Books",
        "IdType" => "EAN",
        "ResponseGroup" => "Medium");
    try {
      //$obj = new AmazonProductAPI();
      $result = aws_signed_request("com", $parameters, $this->public_key, $this->private_key, $this->associate_tag);
      return $result;
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function update_amazon_data($book, $uptitle) {
    $this->db->where('isbn', $book->isbn);
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