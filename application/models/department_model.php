<?php

require_once(APPPATH . 'core/BS_Scrapable_Model.php');

class Department_model extends BS_Scrapable_Model {

  protected $primary_key = 'did';

  protected $required_columns = array(
    'tid',
    'code',
    'bookstore_id',
    'scrape_status',
  );

  protected $child_entity_type = 'course';

  public function scrape($ancestor_ids) {
    return $this->bookstore->get_departments($ancestor_ids);
  }

}
