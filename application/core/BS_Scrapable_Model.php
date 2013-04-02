<?php

abstract class BS_Scrapable_Model extends BS_Model {

  /**
   * Values for the "scrape_status" column.
   */
  const SCRAPE_COMPLETED   = 0;
  const HAS_STALE_CHILDREN = 1;
  const ENTITY_IS_STALE    = 2;

  /**
   * This model's parent model.
   * @var BS_Scrapable_Model
   */
  protected $parent;

  /**
   * This model's child model.
   * @var BS_Scrapable_Model
   */
  protected $children;

  /**
   * The $entity_type of the model associated with this model's children.
   * @var string
   */
  protected $child_entity_type;

  public function __construct() {
    parent::__construct();

    $child_model = $this->child_entity_type . '_model';
    $this->load->model($child_model);
    $this->children = $this->$child_model;
    $this->children->parent = $this;

    $this->load->library('BN_College', NULL, 'bookstore');
  }

  /**
   * Performs the next scrape of the bookstore website.
   *
   * @param integer $parent_id ID of the parent entity that is currently being
   *                           scraped
   * @param array $ancestor_ids Array of bookstore IDs for the ancestor entities
   *                            that are currently being scraped
   * @return bool TRUE if all entities of this type (and their descendants)
   *              have been scraped completely
   */
  public function scrape_next($parent_id = NULL, $ancestor_ids = array()) {
    // If entities of this type have not been scraped completely,
    // recur to scrape the children of those entities.
    // Otherwise, scrape the entities of this type.
    $entities_to_scrape = $this->get_many_by('scrape_status', self::HAS_STALE_CHILDREN);
    if ($entities_to_scrape) {
      return $this->scrape_children($entities_to_scrape, $ancestor_ids);
    }
    else {
      $this->mark_stale_entities($parent_id);
      $scraped_entities = $this->scrape($ancestor_ids);
      $scrape_is_complete = $this->save_scraped_entities($scraped_entities, $parent_id);
      $this->expire_stale_entities($parent_id);
      return $scrape_is_complete;
    }
  }

  /**
   * Scrapes entities from the bookstore website.
   *
   * @param array $ancestor_ids Array of bookstore IDs for the ancestor entities
   *                            that are currently being scraped
   * @return array Array of entity arrays
   */
  abstract protected function scrape($ancestor_ids);

  /**
   * Scrapes the children of the first entity in the given array of entities.
   *
   * @param array $entities Array of entities with children to scrape
   * @param array $ancestor_ids Array of bookstore IDs for the given entities' ancestors
   * @return boolean TRUE if all descendants of the given entities have been scraped
   */
  protected function scrape_children($entities, $ancestor_ids) {
    $entity = reset($entities);
    $entity_id = $entity->{$this->primary_key};
    $ancestor_ids[$this->entity_type] = $entity->bookstore_id;
    $entity_scraped_completely = $this->children->scrape_next($entity_id, $ancestor_ids);
    if ($entity_scraped_completely) {
      $this->mark_scrape_completed(array($entity));
    }
    return ($entity_scraped_completely && (count($entities) == 1));
  }

  /**
   * Saves scraped entities to the database.
   *
   * @param array $scraped_entities The scraped entity arrays to save
   * @param integer $parent_id ID of the parent of the scraped entities
   * @return bool TRUE if the scraped entities have no children to scrape
   */
  protected function save_scraped_entities($scraped_entities, $parent_id) {
    $this->with_result_key('bookstore_id');
    if (isset($this->parent)) {
      $existing_entities = $this->get_many_by($this->parent->primary_key, $parent_id);
    }
    else {
      $existing_entities = $this->get_all();
    }

    $new_entities = array();
    $updated_entities = array();
    $stale_children = FALSE;
    foreach ($scraped_entities as $scraped_entity) {
      // Assume that each entity has children that need to be scraped,
      // unless specified otherwise in self::scrape().
      if ( ! isset($scraped_entity['scrape_status'])) {
        $scraped_entity['scrape_status'] = self::HAS_STALE_CHILDREN;
      }
      if ($scraped_entity['scrape_status'] == self::HAS_STALE_CHILDREN) {
        $stale_children = TRUE;
      }
      $bookstore_id = $scraped_entity['bookstore_id'];
      if (isset($existing_entities[$bookstore_id])) {
        $existing_id = $existing_entities[$bookstore_id]->{$this->primary_key};
        $scraped_entity[$this->primary_key] = $existing_id;
        $scraped_entity['updated'] = date('Y-m-d h:i:s');
        $updated_entities[] = $scraped_entity;
      }
      else {
        if (isset($this->parent)) {
          $scraped_entity[$this->parent->primary_key] = $parent_id;
        }
        $new_entities[] = $scraped_entity;
      }
    }

    $this->insert_many($new_entities);
    $this->update_many($updated_entities);

    return ( ! $stale_children);
  }

  /**
   * Marks the entities that have the given parent as stale.
   *
   * @param integer $parent_id
   */
  protected function mark_stale_entities($parent_id) {
    $this->update_by(array(
        $this->parent->primary_key => $parent_id,
        'scrape_status' => self::SCRAPE_COMPLETED,
      ),
      array(
        'scrape_status' => self::ENTITY_IS_STALE,
      ));
  }

  /**
   * Marks the given entities' as being completely scraped.
   *
   * @param array $entities Array of entity objects
   */
  protected function mark_scrape_completed($entities) {
    foreach ($entities as &$entity) {
      $entity->scrape_status = self::SCRAPE_COMPLETED;
      $entity->scraped = date('Y-m-d h:i:s');
    }
    $this->update_many($entities);
  }

  /**
   * Deletes stale entities that have the given parent.
   *
   * @param integer $parent_id
   */
  protected function expire_stale_entities($parent_id) {
    $this->db->where($this->parent->primary_key, $parent_id);
    $this->db->where('scrape_status', self::ENTITY_IS_STALE);
    $this->db->delete($this->table);
  }

  public function get_scrape_stats() {
    $this->db->select(array(
      '(UNIX_TIMESTAMP() - UNIX_TIMESTAMP(MAX(scraped))) as time_since_last_scrape',
      '(MAX(scrape_status) > 0) as scrape_in_progress',
      'COUNT(' . $this->primary_key . ') as num_entities',
    ));
    $query = $this->db->get($this->table);
    $result = $query->result();
    return (array)($result[0]);
  }

}
