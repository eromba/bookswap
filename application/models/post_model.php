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
   * @return int affected_rows() The id of the inserted post, or false on error
   */
  public function add_post($options = array()) {
    // Check for required values.
    $requiredValues = array('uid', 'bid', 'price');
    foreach ($requiredValues as $column) {
      if ( ! isset($options[$column])) {
        return false;
      }
    }

    // Add new post values to the query.
    $qualificationArray = array('uid', 'bid', 'price', 'notes', 'edition');
    foreach ($qualificationArray as $qualifier) {
      if (isset($options[$qualifier])) {
        $this->db->set($qualifier, $options[$qualifier]);
      }
    }

    $this->db->insert('posts', $options);

    $pid = $this->db->insert_id();
    if ($pid) {
      // Increment the "stock" value for this book.
      $this->db->where('id', $options['bid']);
      $this->db->set('stock', 'stock+1', FALSE);
      $this->db->update('books');
    }

    // Return the ID of the inserted row,
    // or false if the row could not be inserted.
    return $pid;
  }

  /**
   * Retrieves posts from the database.
   *
   * @param array $options Array of query conditions (pid, uid, bid)
   * @return array result() Array of post objects
   */
  public function get_posts($options = array()) {
     // Add conditions to the query.
    $validColumns = array('pid', 'uid', 'bid', 'status');
    foreach ($validColumns as $column) {
      if (isset($options[$column])) {
        $this->db->where($column, $options[$column]);
      }
    }

    $query = $this->db->get('posts');

    if (isset($options['pid'])) {
      // If we know that we're returning a single record,
      // then just return the object.
      return $query->row(0);
    } else {
      // If we could be returning any number of records,
      // then return the array as-is.
      return $query->result();
    }
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
    // A pid must be specified to indicate which post to update.
    if ( ! isset($options['pid'])) {
      return false;
    }
    $this->db->where('pid', $options['pid']);

    // Add updated values to the query.
    $validColumns = array('price', 'notes', 'edition', 'condition', 'status');
    foreach ($validColumns as $column) {
      if (isset($options[$column])) {
        $this->db->set($column, $options[$column]);
      }
    }

    $this->db->update('posts');

    // Return the number of rows updated,
    // or false if the row could not be inserted.
    return $this->db->affected_rows();
  }

  public function deactivate_post($pid) {
    // A pid must be specified to indicate which post to deactivate.
    if ( ! isset($pid)) {
      return false;
    }

    return $this->update_post(array(
        'pid' => $pid,
        'status' => self::DEACTIVATED,
    ));
  }

}
