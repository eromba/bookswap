<?php

class Post_model extends BS_Model {

  public $primary_key = 'pid';

  public $required_columns = array(
    'uid',
    'bid',
    'price',
    'condition',
  );

  /**
   * Returns the lowest selling price for the given book.
   *
   * @param integer $bid
   * @return integer
   */
  public function get_min_price($bid) {
    $this->db->select_min('price');
    $query = $this->db->get_where('posts', array('bid' => $bid));
    return $query->row(0)->price;
  }

}
