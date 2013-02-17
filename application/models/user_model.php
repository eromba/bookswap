<?php

class User_model extends CI_Model {

  public function add_user($data) {
    return ($this->db->insert('users', $data));
  }

  /**
   * Retrieves users from the database.
   *
   * @param array $options Array of query conditions (uid, netid, email, first_name)
   * @return array result() Array of user objects
   */
  public function get_users($options = array()) {
     // Add where clauses to the query.
    $validColumns = array('uid', 'netid', 'email', 'first_name');
    foreach ($validColumns as $column) {
      if (isset($options[$column])) {
        $this->db->where($column, $options[$column]);
      }
    }

    $query = $this->db->get('users');

    if (isset($options['uid']) || isset($options['netid'])) {
      // If we know that we're returning a single record,
      // then just return the object.
      return $query->row(0);
    } else {
      // If we could be returning any number of records,
      // then return the array as-is.
      return $query->result();
    }
  }

}