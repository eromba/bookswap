<?php

class Search extends BS_Controller {

  public function index() {
    $data['title'] = 'Home';
    $this->render_page('index', $data);
  }

  public function need_update($book) {
    if ($book->amzn_link == NULL) {
      return true;
    }
    if ($book->amzn_updated_at == NULL) {
      return true;
    }
    $curr = getcwd();
    $imgFile = $curr . "/img/book-covers/" . $book->isbn . ".jpg";
    if (!(file_exists($imgFile))) {
      return true;
    }
    if (filesize($imgFile) == 0) {
      return true;
    }
    return false;
  }

  public function results($q = "") {
    if ($this->input->post('q') != NULL) {
      header('Location: ' . base_url() . 'search/' . $this->input->post('q'));
    }
    $data['q'] = $q;
    $data['books'] = $this->fetch_results($q);
    $data['title'] = "Results";
    foreach ($data['books'] as $book) {
      if ($this->need_update($book)) {
        $result = $this->book_model->get_amazon_from_isbn($book->isbn);
        if (property_exists($result, "Error"))
          break;
        if (($result != NULL) && ($result->Items->Item != NULL) && ($result->Items->Item->ItemAttributes != NULL) && ($result->Items->Item->ItemAttributes->ListPrice != NULL)) {
          $url = $result->Items->Item->DetailPageURL;
          $listPrice = $result->Items->Item->ItemAttributes->ListPrice->FormattedPrice;
          $lowestNewPrice = $result->Items->Item->OfferSummary->LowestNewPrice->FormattedPrice;
          $lowestUsedPrice = $result->Items->Item->OfferSummary->LowestUsedPrice->FormattedPrice;
          $imgURL = $result->Items->Item->MediumImage->URL;
          $title = $result->Items->Item->ItemAttributes->Title;
          $curr = getcwd();
          $imgFile = $curr . "/img/book-covers/" . $book->isbn . ".jpg";
          //echo filesize($imgFile);
          if ((!(file_exists($imgFile))) || (filesize($imgFile) == 0)) {
            ini_set('allow_url_fopen', 1);
            $ch = curl_init($imgURL);
            $fp = fopen($imgFile, 'w');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            $this->book_model->have_cover($book->isbn);
          }
          $book->amzn_link = "$url";
          if (!($lowestUsedPrice == NULL)) {
            $book->amzn_used_price = substr($lowestUsedPrice, 1);
          } else {
            //echo("has no used price");
            //var_dump($book->isbn);
            //var_dump($result);
            // object(SimpleXMLElement)#97 (2) { ["Error"]=> object(SimpleXMLElement)#105 (2) { ["Code"]=> string(16) "RequestThrottled" ["Message"]=> string(41) "Request from A1X3VU29WDDDXL is throttled." } ["RequestId"]=> string(36) "bbf4e67f-e591-4859-809f-2863eb18e0c9" }
          }
          if (!($lowestNewPrice == NULL)) {
            $book->amzn_new_price = substr($lowestNewPrice, 1);
          } else {
            //echo "has no new price";
          }
          if (!($listPrice == NULL)) {
            $book->amzn_list_price = substr($listPrice, 1);
          } else {
            //echo "has no list price";
          }
          if (!($title == NULL)) {
            $book->title = $title;
            $this->book_model->update_amazon_data($book, true);
          }
          $this->book_model->update_amazon_data($book, false);
        } else {
          //echo($book->isbn);
          //var_dump($result);
        }
      }
    }
    foreach ($data['books'] as $book) {
      if ($book->stock >= 1) {
        $book->posts = $this->post_model->get_posts(array(
            'bid' => $book->id,
            'status' => Post_model::ACTIVE,
        ));
        foreach ($book->posts as $post) {
          $post->sellerdata = $this->user_model->get_users(array('uid' => $post->uid));
        }

        $book->from = $this->book_model->get_min_price($book->id);
      } else {
        $book->posts = array();
      }
    }
    $this->render_page('search_results', $data);
  }

  public function fetch_results($q) {
    if (intval($q) > 999) {
      $results = $this->book_model->get_books_by_isbn($q);
    } else {
      $results = $this->book_model->get_books_by_all($q);
    }

    if ($q == NULL) {
      $results = $this->book_model->get_books();
    }
    return $results;
  }

}