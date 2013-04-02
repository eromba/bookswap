<?php

require_once(APPPATH . 'core/BS_Scrapable_Model.php');

class Section_model extends BS_Scrapable_Model {

  protected $primary_key = 'sid';

  protected $required_columns = array(
    'cid',
    'code',
    'bookstore_id',
    'scrape_status',
  );

  protected $child_entity_type = 'book';

  /**
   * Scrapes sections from the bookstore website.
   *
   * @return array Array of section objects cast as arrays
   */
  protected function scrape($ancestor_ids) {
    $sections = $this->bookstore->get_sections($ancestor_ids);
    foreach ($sections as &$section) {
      // Scraped sections include a "no_books" field that indicates whether the
      // corresponding section needs to be scraped for books.
      if ($section['no_books']) {
        $existing_section = $this->get_by(array('bookstore_id' => $section['bookstore_id']));
        if ($existing_section) {
          $this->remove_books_from_section($existing_section->sid);
        }
        $section['scrape_status'] = self::SCRAPE_COMPLETED;
      }
      else {
        $section['scrape_status'] = self::HAS_STALE_CHILDREN;
      }
      unset($section['no_books']);
    }
    return $sections;
  }

  /**
   * Scrapes the books associated with the given sections.
   *
   * Books differ from the other scrapable entities in a few ways:
   *  - Books have no child entities.
   *  - Books can be scraped in batch (i.e. one HTTP request can fetch books
   *    for multiple sections).
   *  - When previously-scraped books are updated, only their bookstore prices
   *    are modified (other book details are updated separately via Amazon).
   *  - There is a many-to-many relationship between sections and books
   *    (as opposed to the one-to-many parent-child relationship between other
   *    scrapable entities, e.g. courses and sections). This requires the
   *    sections_books table to be updated when books are scraped.
   *
   * @param array $sections Array of section objects for which to scrape books
   * @return boolean TRUE
   */
  protected function scrape_children($sections) {
    $section_bookstore_ids = array();
    foreach ($sections as $section) {
      $section_bookstore_ids[] = $section->bookstore_id;
    }
    $scraped_books = $this->children->scrape($section_bookstore_ids);

    $saved_books = $this->children->save_scraped_entities($scraped_books);

    // Group books by section.
    $books = array_fill_keys($section_bookstore_ids, array());
    foreach ($saved_books as $book) {
      $books[$book->section_id][] = $book;
    }

    foreach ($sections as $section) {
      $this->set_books_for_section($section->sid, $books[$section->bookstore_id]);
    }

    $this->mark_scrape_completed($sections);

    return TRUE;
  }

  /**
   * Saves the associations between the given section and the given books.
   *
   * The new associations will replace all existing book associations for the
   * given section.
   *
   * @param integer $section_id
   * @param array $books Array of book entities. Each entity must specify
   *                     at least its "bid" and its "required_status".
   */
  public function set_books_for_section($section_id, $books) {
    $this->remove_books_from_section($section_id);
    if ($books) {
      $data = array();
      foreach ($books as $book) {
        $book = (array)$book;
        $data[] = array(
          'sid' => $section_id,
          'bid' => $book['bid'],
          'required_status' => $book['required_status'],
        );
      }
      $this->db->insert_batch('sections_books', $data);
    }
  }

  /**
   * Removes all existing book associations for the given section.
   *
   * @param integer $section_id
   */
  public function remove_books_from_section($section_id) {
    $this->db->where('sid', $section_id);
    $this->db->delete('sections_books');
  }

}
