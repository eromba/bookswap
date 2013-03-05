<?php

class User_model extends CI_Model {

  /**
   * Creates a new user in the database.
   *
   * At minimum, the $options array must specify values for the following columns:
   *  - netid
   *  - email
   *
   * @param array $options Array of columns => values to be saved to the database
   * @return int insert_id() The ID of the inserted user, or false on error
   */
  public function add_user($options = array()) {
    $requiredColumns = array('netid', 'email');
    foreach ($requiredColumns as $column) {
      if ( ! isset($options[$column])) {
        return false;
      }
    }

    $valid_columns = array('netid', 'email', 'first_name');
    foreach ($valid_columns as $column) {
      if (isset($options[$column])) {
        $this->db->set($column, $options[$column]);
      }
    }

    $this->db->insert('users', $options);

    return $this->db->insert_id();
  }

  /**
   * Retrieves users from the database.
   *
   * @param array $options Array of query conditions (uid, netid, email, first_name)
   * @return array result() Array of user objects, or a single post object if
   *                        a uid or netid is specified
   */
  public function get_users($options = array()) {
    $valid_columns = array('uid', 'netid', 'email', 'first_name');
    foreach ($valid_columns as $column) {
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

  /**
   * Updates a user in the database.
   *
   * At minimum, the $options array must specify the uid of the post to update.
   *
   * @param array $options Array of columns => values to be saved to the database
   * @return int affected_rows() Number of rows updated, or false on error
   */
  public function update_user($options = array()) {
    if ( ! isset($options['uid'])) {
      return false;
    }
    $this->db->where('uid', $options['uid']);

    $valid_columns = array('netid', 'email', 'first_name');
    foreach ($valid_columns as $column) {
      if (isset($options[$column])) {
        $this->db->set($column, $options[$column]);
      }
    }

    $this->db->update('users');

    return $this->db->affected_rows();
  }

}
