<?php

class Post_model extends CI_Model {

  /**
   * Creates a new post in the database.
   *
   * At minimum, the $options array must specify values for the following columns:
   *  - uid
   *  - bid
   *  - price
   *
   * @param array $options Array of columns => values to be saved to the database
   * @return int insert_id() The ID of the inserted post, or false on error
   */
  public function add_post($options = array()) {
    $required_columns = array('uid', 'bid', 'price');
    foreach ($required_columns as $column) {
      if ( ! isset($options[$column])) {
        return false;
      }
    }

    $valid_columns = array('uid', 'bid', 'price', 'notes', 'edition', 'condition');
    foreach ($valid_columns as $column) {
      if (isset($options[$column])) {
        $this->db->set($column, $options[$column]);
      }
    }

    $this->db->insert('posts', $options);

    return $this->db->insert_id();
  }

  /**
   * Retrieves posts from the database.
   *
   * @param array $options Array of query conditions (pid, uid, bid, active)
   * @return array result() Array of post objects, or a single post object if
   *                        a pid is specified
   */
  public function get_posts($options = array()) {
    $valid_columns = array('pid', 'uid', 'bid', 'active');
    foreach ($valid_columns as $column) {
      if (isset($options[$column])) {
        $this->db->where($column, $options[$column]);
      }
    }

    if (isset($options['order_by'])) {
      $this->db->order_by($options['order_by']);
    }

    $query = $this->db->get('posts');

    if (isset($options['pid'])) {
      return $query->row(0);
    } else {
      return $query->result();
    }
  }

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

  /**
   * Updates a post in the database.
   *
   * At minimum, the $options array must specify the pid of the post to update.
   *
   * @param array $options Array of columns => values to be saved to the database
   * @return int affected_rows() Number of rows updated, or false on error
   */
  public function update_post($options = array()) {
    if ( ! isset($options['pid'])) {
      return false;
    }
    $this->db->where('pid', $options['pid']);

    $valid_columns = array('price', 'notes', 'edition', 'condition', 'active');
    foreach ($valid_columns as $column) {
      if (isset($options[$column])) {
        $this->db->set($column, $options[$column]);
      }
    }

    $this->db->update('posts');

    return $this->db->affected_rows();
  }

}
