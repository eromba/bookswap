<?php

class Cron extends CI_Controller {

  public function __construct() {
    parent::__construct();

    // Allow only command-line execution.
    if ( ! $this->input->is_cli_request()) {
      redirect(base_url());
    }

    $this->config->load('bookswap');
  }

  /**
   * Updates book details using the Amazon Product Advertising API.
   */
  public function update_amazon_data() {
    $this->load->model('book_model', 'books');
    $this->load->model('amazon_model', 'amazon');

    try {
      $request_limit = $this->config->item('amazon_requests_per_cron');
      for ($i = 0; $i < $request_limit; $i++) {
        $isbns = $this->books->get_books_to_update();
        if (count($isbns) > 0) {
          $book_details = $this->amazon->look_up_isbns($isbns);
          foreach ($isbns as $isbn) {
            if (isset($book_details[$isbn])) {
              // Update the book's title only if Amazon's title is not empty.
              if (empty($book_details[$isbn]['title'])) {
                unset($book_details[$isbn]['title']);
              }
            }
            else {
              // If the Amazon API did not return any data for this book,
              // we still pass it to update_amazon_data() to set its
              // last-update time in the database. This removes the book from the
              // update "queue", giving other books the chance to be updated.
              $book_details[$isbn]['isbn'] = $isbn;
            }
          }
          $this->books->update_amazon_data($book_details);
        }
        else {
          break;
        }
      }
    }
    catch (Exception $e) {
      log_message('error', $e->getMessage());
    }
  }

  /**
   * Scrapes the bookstore website for course data and textbook requirements.
   */
  public function update_bookstore_data() {
    $this->load->model('term_model', 'terms');

    try {
      $num_requests = $this->config->item('bookstore_requests_per_minute');
      $time_division = (60 / $num_requests); // seconds
      $time1 = microtime(true);
      for ($i = 0; $i < $num_requests; $i++) {
        $scrape_stats = $this->terms->get_scrape_stats();

        $scrape_in_progress = $scrape_stats['scrape_in_progress'];
        $bookstore_data_ttl = $this->config->item('bookstore_data_ttl');
        $scrape_is_due = ($scrape_stats['time_since_last_scrape'] > $bookstore_data_ttl);
        $db_is_empty = ($scrape_stats['num_entities'] == 0);

        if ($scrape_in_progress || $scrape_is_due || $db_is_empty) {
          $this->terms->scrape_next();
        }
        else {
          break;
        }

        $time2 = microtime(true);
        $difference = ($time2 - $time1); // seconds
        if ($difference >= $time_division) {
          // Scraping took longer than the allotted time.
          // Loop immediately.
          $time1 = $time2;
        }
        else if (($i + 1) < $num_requests) {
          // Scraping finished before the time division was reached.
          // Sleep to make up the difference.
          usleep(($time_division - $difference) * 1000000);
          $time1 = microtime(true);
        }
      }
    }
    catch (Exception $e) {
      log_message('error', $e->getMessage());
    }
  }

}
