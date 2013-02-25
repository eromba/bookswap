<?php

class Post_model extends CI_Model {

  // Values for "status" column in posts table.
  const ACTIVE = 1;
  const DEACTIVATED = 0;

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
    $requiredColumns = array('uid', 'bid', 'price');
    foreach ($requiredColumns as $column) {
      if ( ! isset($options[$column])) {
        return false;
      }
    }

    $validColumns = array('uid', 'bid', 'price', 'notes', 'edition', 'condition');
    foreach ($validColumns as $column) {
      if (isset($options[$column])) {
        $this->db->set($column, $options[$column]);
      }
    }

    $this->db->insert('posts', $options);

    $pid = $this->db->insert_id();
    if ($pid) {
      // Increment the "stock" value for this book.
      $this->db->where('bid', $options['bid']);
      $this->db->set('stock', 'stock+1', FALSE);
      $this->db->update('books');
    }

    return $pid;
  }

  /**
   * Retrieves posts from the database.
   *
   * @param array $options Array of query conditions (pid, uid, bid)
   * @return array result() Array of post objects, or a single post object if
   *                        a pid is specified
   */
  public function get_posts($options = array()) {
    $validColumns = array('pid', 'uid', 'bid', 'status');
    foreach ($validColumns as $column) {
      if (isset($options[$column])) {
        $this->db->where($column, $options[$column]);
      }
    }

    $query = $this->db->get('posts');

    if (isset($options['pid'])) {
      return $query->row(0);
    } else {
      return $query->result();
    }
  }

  /**
   * Retrieves active posts from the database.
   *
   * @param array $options Array of query conditions (pid, uid, bid)
   * @return array result() Array of post objects
   */
  public function get_active_posts($options = array()) {
    $options['status'] = self::ACTIVE;
    return $this->get_posts($options);
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

    $validColumns = array('price', 'notes', 'edition', 'condition', 'status');
    foreach ($validColumns as $column) {
      if (isset($options[$column])) {
        $this->db->set($column, $options[$column]);
      }
    }

    $this->db->update('posts');

    return $this->db->affected_rows();
  }

  public function deactivate_post($pid) {
    if ( ! isset($pid)) {
      return false;
    }

    return $this->update_post(array(
        'pid' => $pid,
        'status' => self::DEACTIVATED,
    ));
  }

}
