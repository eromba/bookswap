<?php

class User_model extends CI_Model {

  public function add_user($data) {
    return ($this->db->insert('users', $data));
  }

  /**
   * Retrieves users from the database.
   *
   * @param array $options Array of query conditions (uid, netid, email, first_name)
   * @return array result() Array of user objects, or a single post object if
   *                        a uid or netid is specified
   */
  public function get_users($options = array()) {
    $validColumns = array('uid', 'netid', 'email', 'first_name');
    foreach ($validColumns as $column) {
      if (isset($options[$column])) {
        $this->db->where($column, $options[$column]);
      }
    }

    $query = $this->db->get('users');

    if (isset($options['uid']) || isset($options['netid'])) {
      return $query->row(0);
    } else {
      return $query->result();
    }
  }

}