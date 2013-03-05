<?php

class Cron extends CI_Controller {

  public function __construct() {
    parent::__construct();

    // Allow only command-line execution.
    if ( ! $this->input->is_cli_request()) {
      redirect(base_url());
    }

    $this->config->load('bookswap');
    $this->load->model('book_model');
    $this->load->model('amazon_model');
  }

  public function update_amazon_data() {
    try {
      $request_limit = $this->config->item('amazon_requests_per_cron');
      for ($i = 0; $i < $request_limit; $i++) {
        $isbns = $this->book_model->get_books_to_update();
        if (count($isbns) > 0) {
          $book_details = $this->amazon_model->look_up_isbns($isbns);
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
          $this->book_model->update_amazon_data($book_details);
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

}
