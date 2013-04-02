<?php

require_once(APPPATH . 'core/BS_Scrapable_Model.php');

class Course_model extends BS_Scrapable_Model {

  protected $primary_key = 'cid';

  protected $required_columns = array(
    'did',
    'code',
    'name',
    'bookstore_id',
    'scrape_status',
  );

  protected $child_entity_type = 'section';

  public function scrape($ancestor_ids) {
    $courses = $this->bookstore->get_courses($ancestor_ids);
    $department = $this->parent->get_by('bookstore_id', $ancestor_ids['department']);
    foreach ($courses as &$course) {
      $course['name'] = $department->code . ' ' . $course['code'];
    }
    return $courses;
  }

}
