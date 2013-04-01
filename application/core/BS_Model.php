<?php

/**
 * Base Model Class
 *
 * Based on MY_Model by Jamie Rumbelow
 * @link http://github.com/jamierumbelow/codeigniter-base-model
 */
class BS_Model extends CI_Model {

  /**
   * The type of entities this model represents.
   * @var string
   */
  public $entity_type;

  /**
   * This model's default database table.
   * Automatically guessed by pluralising the model name.
   * @var string
   */
  protected $table;

  /**
   * The name of the database table's primary key.
   * @var string
   */
  protected $primary_key;

  /**
   * Protected, non-modifiable columns
   * @var array
   */
  protected $protected_columns = array();

  /**
   * Columns that must be specified when creating a new entity.
   * @var array
   */
  protected $required_columns = array();

  /**
   * Column to be used for keys in the result array of the next "get" query.
   * @var string
   */
  protected $result_key = NULL;

  public function __construct() {
    parent::__construct();

    $this->load->helper('inflector');

    if ($this->entity_type == NULL) {
      $this->entity_type = preg_replace('/(_m|_model)?$/', '', strtolower(get_class($this)));
    }

    if ($this->table == NULL) {
      $this->table = plural($this->entity_type);
    }

    if ($this->protected_columns == NULL) {
      $this->protected_columns = array(
        'created',
      );
    }
  }

  /* --------------------------------------------------------------
   * CRUD INTERFACE
   * ------------------------------------------------------------ */

  /**
   * Inserts a new entity into the table.
   *
   * @param array $entity Entity object or array to save to the database
   * @return int insert_id() The ID of the inserted entity, or FALSE on error
   */
  public function insert($entity) {
    return $this->insert_many(array($entity));
  }

  /**
   * Inserts new entities into the table.
   *
   * @param array $entities Array of entity objects or arrays to save to the database
   * @return int insert_id() The ID of the last-inserted entity, or FALSE on error
   */
  public function insert_many($entities) {
    $valid_entities = $this->validate($entities, TRUE, TRUE);
    if ($valid_entities) {
      $this->db->insert_batch($this->table, $valid_entities);
      return $this->db->insert_id();
    }
    else {
      return FALSE;
    }
  }

  /**
   * Fetches a single record based on the primary key.
   *
   * @param integer $primary_value
   * @return stdClass An entity object
   */
  public function get($primary_value) {
    $entity = $this->db->where($this->primary_key, $primary_value)
                       ->get($this->table)
                       ->row();

    if (method_exists($this, 'prepare_entity')) {
      $this->prepare_entity($entity);
    }

    return $entity;
  }

  /**
   * Fetches a single record based on an arbitrary WHERE call.
   *
   * Accepts any valid parameters to $this->db->where().
   *
   * @param mixed $where Column name OR array of (column => value) conditions
   * @param string $where_value (optional) Column value
   * @return stdClass An entity object
   */
  public function get_by() {
    $where = func_get_args();
    $this->_set_where($where);

    $entity = $this->db->get($this->table)
                       ->row();

    if (method_exists($this, 'prepare_entity')) {
      $this->prepare_entity($entity);
    }

    return $entity;
  }

  /**
   * Fetches an array of records based on an array of primary values.
   *
   * @param array $values Array of primary values
   * @return array Array of entity objects
   */
  public function get_many($values) {
    $this->db->where_in($this->primary_key, $values);
    return $this->get_all();
  }

  /**
   * Fetches an array of entities based on an arbitrary WHERE call.
   *
   * @param mixed $where Column name OR array of (column => value) conditions
   * @param string $where_value (optional) Column value
   * @return array Array of entity objects
   */
  public function get_many_by() {
    $where = func_get_args();
    $this->_set_where($where);
    return $this->get_all();
  }

  /**
   * Fetches all the entities in the table.
   *
   * Can be used as a generic call to $this->db->get() with scoped methods.
   *
   * @return array Array of entity objects
   */
  public function get_all() {
    $entities = $this->db->get($this->table)
                                ->result();

    if (method_exists($this, 'prepare_entity')) {
      array_walk($entities, array($this, 'prepare_entity'));
    }

    if ($this->result_key) {
      $result = array();
      foreach ($entities as $entity) {
        $result[$entity->{$this->result_key}] = $entity;
      }
      $this->result_key = NULL;
      return $result;
    }
    else {
      return $entities;
    }
  }

  /**
   * Updates entities in the table.
   *
   * @param array $entities Array of updated entity objects or arrays to save
   * @param string $where_key (optional) The unique key by which the entity is
   * mapped to an existing entity in the database
   * @return int affected_rows() Number of affected rows, or FALSE on error
   */
  public function update($entity, $where_key = NULL) {
    return $this->update_many(array($entity), $where_key);
  }

  /**
   * Updates entities in the table.
   *
   * @param array $entities Array of updated entity objects or arrays to save
   * @param string $where_key (optional) The unique key by which each entity in
   * $entities is mapped to an existing entity in the database
   * @return int Number of affected rows, or FALSE on error
   */
  public function update_many($entities, $where_key = NULL) {
    if ( ! $where_key) {
      $where_key = $this->primary_key;
      $protect_primary_key = FALSE;
    }
    else {
      $protect_primary_key = TRUE;
    }
    $valid_entities = $this->validate($entities, FALSE, $protect_primary_key);
    if ($valid_entities) {
      $this->db->update_batch($this->table, $valid_entities, $where_key);
      return $this->db->affected_rows();
    }
    else {
      return FALSE;
    }
  }

  /**
   * Updates entities based on an arbitrary WHERE clause.
   *
   * @param mixed $where Column name OR array of (column => value) conditions
   * @param mixed $where_value (optional) Column value
   * @param mixed $data Array or object of (column => value) data to save
   * @return int Number of affected rows, or FALSE on error
   */
  public function update_by() {
    $args = func_get_args();
    $data = array_pop($args);
    $this->_set_where($args);
    return $this->update_all($data);
  }

  /**
   * Updates all entities.
   *
   * @param mixed $data Array or object of (column => value) data to save
   * @return int Number of affected rows, or FALSE on error
   */
  public function update_all($data) {
   $valid_data = $this->validate(array($data));
   $result = $this->db->set($valid_data[0])
                      ->update($this->table);
   if ($result) {
     return $this->db->affected_rows();
   }
   else {
     return FALSE;
   }
  }

  /* --------------------------------------------------------------
   * GLOBAL SCOPES
   * ------------------------------------------------------------ */

  /**
   * Sets the column to be used for keys in the result array of the next "get" query.
   *
   * By default we return multiple results in an indexed array.
   * Use this method to return an associative array instead,
   * with the values in the $result_key column as the array keys.
   *
   * @param string $result_key
   * @return \BS_Model
   */
  public function with_result_key($result_key) {
    $this->result_key = $result_key;
    return $this;
  }

  /* --------------------------------------------------------------
   * QUERY BUILDER DIRECT ACCESS METHODS
   * ------------------------------------------------------------ */

  /**
   * Wraps $this->db->order_by().
   *
   * @return \BS_Model
   */
  public function order_by($criteria, $order = 'ASC') {
    if (is_array($criteria)) {
      foreach ($criteria as $key => $value) {
        $this->db->order_by($key, $value);
      }
    }
    else {
      $this->db->order_by($criteria, $order);
    }
    return $this;
  }

  /**
   * Wraps $this->db->limit().
   *
   * @return \BS_Model
   */
  public function limit($limit, $offset = 0) {
    $this->db->limit($limit, $offset);
    return $this;
  }

  /* --------------------------------------------------------------
   * INTERNAL METHODS
   * ------------------------------------------------------------ */

  /**
   * Validates entity parameters prior to running insert/update queries.
   *
   * @param array $entities Entities to validate
   * @param boolean $check_required Whether or not to check for required fields
   * @param boolean $unset_primary_key Whether or not to protect the primary key
   * @return array The given entities with protected columns removed,
   *               or FALSE if required columns are not specified
   */
  protected function validate($entities, $check_required = FALSE, $protect_primary_key = FALSE) {
    $valid_entities = array();
    foreach ($entities as $entity) {
      $entity = (array)$entity;
      // Ensure required columns are specified.
      if ($check_required && array_diff($this->required_columns, array_keys($entity))) {
        return FALSE;
      }
      // Remove protected columns.
      if ($protect_primary_key) {
        unset($entity[$this->primary_key]);
      }
      foreach($this->protected_columns as $column) {
        unset($entity[$column]);
      }
      $valid_entities[] = $entity;
    }
    return $valid_entities;
  }

  /**
   * Sets WHERE parameters, cleverly.
   */
  protected function _set_where($params) {
    if (count($params) == 1) {
      foreach ($params[0] as $column => $value) {
        if (is_array($value)) {
          $this->db->where_in($column, $value);
        }
        else {
          $this->db->where($column, $value);
        }
      }
    }
    else {
      $column = $params[0];
      $value = $params[1];
      if (is_array($value)) {
        $this->db->where_in($column, $value);
      }
      else {
        $this->db->where($column, $value);
      }
    }
  }

}
