<?php

class Book_model extends CI_Model {

  const BOOKSTORE_RECOMMENDED = 0;
  const GO_TO_CLASS_FIRST = 1;
  const RECOMMENDED = 2;
  const REQUIRED = 3;

  /**
   * Retrieves books from the database.
   *
   * @param array $options Array of query conditions
   * @return array result() Array of book objects, or a single book object if
   *                        a bid or isbn is specified
   */
  public function get_books($options = array()) {
    $valid_columns = array('bid', 'isbn');
    foreach ($valid_columns as $column) {
      if (isset($options[$column])) {
        $this->db->where($column, $options[$column]);
      }
    }

    $query = $this->db->get('books');

    $books = $query->result();

    foreach ($books as &$book) {
      $book->user_pid = NULL;


      $book->courses = array(
        'Bookstore Recommended' => array(),
        'Go To Class First' => array(),
        'Recommended' => array(),
        'Required' => array(),
      );
      $courses = $this->get_courses($book->bid);
      foreach($courses as $course) {
        switch ($course->required_status) {
          case self::BOOKSTORE_RECOMMENDED:
            $book->courses['Bookstore Recommended'][] = $course;
            break;
          case self::GO_TO_CLASS_FIRST:
            $book->courses['Go To Class First'][] = $course;
            break;
          case self::RECOMMENDED:
            $book->courses['Recommended'][] = $course;
            break;
          case self::REQUIRED:
            $book->courses['Required'][] = $course;
            break;
        }
      }

      $book->posts = $this->post_model->get_posts(array(
        'bid' => $book->bid,
        'active' => TRUE,
        'order_by' => 'price asc',
      ));
      foreach ($book->posts as $post) {
        $post->user = $this->user_model->get_users(array('uid' => $post->uid));
        if ($this->user && ($post->user->uid == $this->user->uid)) {
          $book->user_pid = $post->pid;
        }
      }

      $book->num_posts = count($book->posts);
      $book->min_student_price = $this->post_model->get_min_price($book->bid);
      $book->min_store_price = min($book->bookstore_new_price, $book->amazon_new_price);
      $book->num_store_offers = 0;
      if ($book->bookstore_new_price != 0) {
        $book->num_store_offers++;
      }
      if ($book->amazon_new_price != 0) {
        $book->num_store_offers++;
      }
    }

    if (isset($options['bid']) || isset($options['isbn'])) {
      return $books[0];
    }
    else {
      return $books;
    }
  }

  private function get_courses($bid) {
    $this->db->select('name, sections.code as section, sections_books.required_status');
    $this->db->distinct();
    $this->db->from('courses');
    $this->db->join('sections', 'sections.cid = courses.cid');
    $this->db->join('sections_books', 'sections_books.sid = sections.sid');
    $this->db->where('sections_books.bid', $bid);
    $query = $this->db->get();
    return $query->result();
  }

  /**
   * Retrieves books from the database that match the given string.
   *
   * @param string $string The string to search by
   * @return array Array of book objects
   */
  public function get_books_by_string($string) {
    $this->db->like('name', $string);
    $query = $this->db->get('courses');
    $courses = $query->result();
    if (count($courses) > 0) {
      $this->db->select('books.*');
      $this->db->distinct();
      $this->db->join('sections_books', 'sections_books.bid = books.bid');
      $this->db->join('sections', 'sections.sid = sections_books.sid');
      $this->db->where('sections.cid', $courses[0]->cid);
      return $this->get_books();
    }
    else {
      $this->db->or_like('title', $string);
      return $this->get_books();
    }
  }

  /**
   * Returns the ISBNs of books whose Amazon data needs to be updated.
   *
   * @return array Array of ISBN strings (at most 10)
   */
  public function get_books_to_update() {
    $this->db->select('isbn');
    $this->db->where('isbn IS NOT NULL');
    $one_day_ago = time() - (24 * 60 * 60);
    $this->db->where("UNIX_TIMESTAMP(amazon_updated) < $one_day_ago OR amazon_updated IS NULL");
    $this->db->order_by('amazon_updated', 'asc');
    $this->db->limit(10);
    $query = $this->db->get('books');
    $results = $query->result();
    $isbns = array();
    foreach ($results as $result) {
      if ($result->isbn) {
        $isbns[] = $result->isbn;
      }
    }
    return $isbns;
  }

  /**
   * Batch updates Amazon data for multiple books in the database.
   *
   * At minimum, the $book_details array must specify the ISBNs of the books to update.
   *
   * @param array $book_details Array of columns => values arrays to be saved to the database
   * @return int affected_rows() Number of rows updated, or false on error
   */
  public function update_amazon_data($book_details) {
    $data = array();
    $valid_columns = array('isbn', 'title', 'edition', 'publisher', 'publication_date', 'binding', 'image_url', 'amazon_url', 'amazon_list_price', 'amazon_new_price', 'amazon_used_price');
    foreach ($book_details as $amazon_book) {
      $row = array();
      foreach ($valid_columns as $column) {
        if (isset($amazon_book[$column])) {
          $row[$column] = $amazon_book[$column];
        }
      }
      $row['amazon_updated'] = date('Y-m-d H:i:s');
      $data[] = $row;
    }
    $this->db->update_batch('books', $data, 'isbn');
    return $this->db->affected_rows();
  }

}
