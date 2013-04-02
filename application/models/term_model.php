<?php

require_once(APPPATH . 'core/BS_Scrapable_Model.php');

class Term_model extends BS_Scrapable_Model {

  protected $primary_key = 'tid';

  protected $required_columns = array(
    'name',
    'bookstore_id',
    'scrape_status',
  );

  protected $child_entity_type = 'department';

  protected function scrape($ancestor_ids) {
    return $this->bookstore->get_terms($ancestor_ids);
  }

  /**
   * Marks active terms to be scraped.
   */
  public function mark_stale_entities() {
    $this->update_by('active', TRUE, array('scrape_status' => self::ENTITY_IS_STALE));
  }

  /**
   * Deactivates old terms.
   */
  public function expire_stale_entities() {
    $this->update_by('scrape_status', self::ENTITY_IS_STALE, array(
      'active' => FALSE,
      'scrape_status' => self::SCRAPE_COMPLETED,
    ));
  }

}
