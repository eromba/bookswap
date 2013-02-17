<?php

class Post_model extends CI_Model {

  // Values for "status" column in posts table.
  const ACTIVE = 1;
  const DEACTIVATED = 0;

  public function add_post($uid) {
    //https://plus.google.com/+LilPeck/posts/bLm9S75srcm
    $value = strval(strip_tags($this->input->post('price'))); //figured it couldn't hurt to make it string
    $myval = htmlentities($value); //important part
    $price = intval(preg_replace('/[\$\,]/', '', $myval)); //remove $ and comma
    $notes = $this->input->post('notes');
    $bid = $this->input->post('bid');
    $edition = $this->input->post('edition');
    $data = array(
        'bid' => $bid,
        'uid' => $uid,
        'price' => $price,
        'notes' => $notes,
        'edition' => $edition
    );
    //var_dump($data);
    $this->db->insert('posts', $data);

    $this->db->where('id', $bid);
    $this->db->set('stock', 'stock+1', FALSE);
    $this->db->update('books');
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
