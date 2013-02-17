<?php

class Post_model extends CI_Model {

  public function add_post($seller) {
    //https://plus.google.com/+LilPeck/posts/bLm9S75srcm
    $value = strval(strip_tags($this->input->post('price'))); //figured it couldn't hurt to make it string
    $myval = htmlentities($value); //important part
    $price = intval(preg_replace('/[\$\,]/', '', $myval)); //remove $ and comma
    $notes = $this->input->post('notes');
    $bid = $this->input->post('bid');
    $edition = $this->input->post('edition');
    $data = array(
        'bid' => $bid,
        'seller' => $seller,
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

  public function get_posts_by_bid($bid) {
    $query = $this->db->get_where('posts', array('bid' => $bid));
    return $query->result();
  }

  public function get_posts_by_pid($pid) {
    $query = $this->db->get_where('posts', array('pid' => $pid));
    return $query->result();
  }

  public function get_posts_by_seller($netid) {
    $query = $this->db->get_where('posts', array('seller' => $netid));
    return $query->result();
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
    $netid = $this->session->userdata('bookswap_user');
    $netid = $netid->netid;
    $pid = $this->input->post('post_id');
    $post = $this->get_posts_by_pid($pid);
    if ($post) {
      $post = $post[0];
      if ($post->seller == $netid) {
        $this->db->insert('removed_posts', $post);
        $this->db->delete('posts', array('pid' => $pid));
        return $pid;
      }
    }
    return "Error";
  }

}
