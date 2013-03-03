<?php

class Amazon_model extends CI_Model {

  /**
   * Exception Codes
   */
  const CONNECTION_ERROR = 404;
  const INVALID_REQUEST  = 400;

  public function __construct() {
    parent::__construct();
    $this->load->library('Amazon_API');
  }

  /**
   * Queries the Amazon Product Advertising API with the given parameters.
   *
   * @param array $parameters Query parameters
   * @return simpleXMLElement Query response
   */
  private function query_amazon($parameters) {
    $response = $this->amazon_api->query($parameters);
    if ($response === FALSE) {
      throw new Exception('Could not connect to Amazon', self::CONNECTION_ERROR);
    }
    $this->verify_response($response);
    return $response;
  }

  /**
   * Checks the given Amazon API response for errors.
   *
   * @param simpleXMLElement $response XML response to check
   */
  private function verify_response($response) {
    $request = $response->Items->Request;
    if ((string)$request->IsValid === 'False') {
      $message = 'Invalid Amazon API request:';
      $errors = $request->xpath('Errors/Error');
      foreach ($errors as $error) {
        $message .= ' (' . $error->Code . ') ' . $error->Message;
      }
      throw new Exception($message, self::INVALID_REQUEST);
    }
  }

  /**
   * Returns Amazon details for the books with the given ISBNs.
   *
   * @param array $isbn Array of 10 or fewer integer ISBNs
   * @return array Array of ISBN => book details, or false on error
   */
  public function look_up_isbns($isbns) {
    if (count($isbns) > 10) {
      return FALSE;
    }
    $parameters = array(
      'Operation' => 'ItemLookup',
      'IdType' => 'EAN', // Equivalent to ISBN-13
      'ItemId' => implode(',', $isbns),
      'SearchIndex' => 'Books',
      'ResponseGroup' => 'ItemAttributes,OfferSummary,Images',
    );
    $amazon_data = $this->query_amazon($parameters);
    $book_results = $amazon_data->xpath('Items/Item');
    $books = array();
    foreach($book_results as $book_result) {
      $isbn            = (string)$book_result->ItemAttributes->EAN;
      $title           = $book_result->ItemAttributes->Title;
      $image_url       = $book_result->MediumImage->URL;
      $amzn_link       = $book_result->DetailPageURL;
      $amzn_list_price = $book_result->ItemAttributes->ListPrice->Amount;
      $amzn_new_price  = $book_result->OfferSummary->LowestNewPrice->Amount;
      $amzn_used_price = $book_result->OfferSummary->LowestUsedPrice->Amount;
      $books[$isbn] = array(
        'isbn'            => $isbn,
        'title'           => ($title)           ? (string)$title                  : NULL,
        'image_url'       => ($image_url)       ? (string)$image_url              : NULL,
        'amzn_link'       => ($amzn_link)       ? (string)$amzn_link              : NULL,
        'amzn_list_price' => ($amzn_list_price) ? ((float)$amzn_list_price / 100) : NULL,
        'amzn_new_price'  => ($amzn_new_price)  ? ((float)$amzn_new_price  / 100) : NULL,
        'amzn_used_price' => ($amzn_used_price) ? ((float)$amzn_used_price / 100) : NULL,
      );
    }
    return $books;
  }

}
