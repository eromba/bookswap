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

  public function update_post() {
    $this->db->where('pid', $this->input->post('pid'));
    $this->db->set('price', $this->input->post('price'));
    $this->db->set('notes', $this->input->post('notes'));
    $this->db->set('edition', $this->input->post('edition'));
    $this->db->set('condition', $this->input->post('condition'));
    $this->db->update('posts');
  }

  public function remove_post() {
    $pid = $this->input->post('post_id');
    $post = $this->get_posts(array('pid' => $pid));
    if ($post) {
      $user = $this->session->userdata('bookswap_user');
      if ($post->uid == $user->uid) {
        $this->db->where('pid', $pid);
        $this->db->set('status', self::DEACTIVATED);
        $this->db->update('posts');
        return $pid;
      }
    }
    return "Error";
  }

}
