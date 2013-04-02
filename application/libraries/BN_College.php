<?php

/**
 * Based on the BnCollege class from GetchaBooks:
 * https://github.com/getchabooks/getchabooks
 */
class BN_College {

  /**
   * The subdomain for this school's BN College website.
   * @var string
   */
  protected $subdomain;

  /**
   * The BN-College campusId URL parameter for this school's campus.
   * @var string
   */
  protected $campus_id;

  /**
   * The BN-College storeId URL parameter for this school's bookstore.
   * @var string
   */
  protected $store_id;

  public function __construct($params) {
    $this->subdomain = $params['subdomain'];
    $this->campus_id = $params['campus_id'];
    $this->store_id = $params['store_id'];

    $this->ci =& get_instance();
    $this->ci->load->driver('cache', array(
      'adapter' => 'apc',
      'backup' => 'file',
    ));
  }

  /**
   * @param array $section_ids Array of section IDs
   * @return string The bookstore URL for $sections
   */  
  public function get_sections_url($section_ids) {
    $url = "http://$this->subdomain.bncollege.com/webapp/wcs/stores/servlet/"
      . "TBListView?catalogId=10001&clearAll=&langId=-1&mcEnabled=N"
      . "&numberOfCourseAlready=0&removeSectionId=&savedListAdded=true"
      . "&sectionList=newSectionNumber&storeId=$this->store_id&viewName=TBWizardView";
    $i = 1;
    foreach ($section_ids as $section_id) {
      $url .= '&section_'.($i++).'='.$section_id;
    }
    return $url;
  }  

  /**
   * @param array $book
   * @return string The bookstore URL for the given book
   */  
  public function get_book_url($book) {
    $productId = $book['ProductId'];
    $partNumber = $book['PartNumber'];
    return "http://$this->subdomain.bncollege.com/webapp/wcs/stores/servlet/"
      . "BNCB_TextbookDetailView?catalogId=10001&storeId=$this->store_id"
      . "&langId=-1&productId=$productId&partNumber=$partNumber&"
      . "item=Y&displayStoreId=$this->store_id";
  }

  protected function make_bn_request($view, $params) {
    $base = "http://$this->subdomain.bncollege.com/webapp/wcs/stores/servlet/";
    $referer = $base . "TBWizardView?catalogId=10001&storeId=$this->store_id&langId=-1";

    $num_requests = $this->ci->cache->get('bn_college_num_requests');
    if ($num_requests === FALSE) {
      $num_requests = 0;
    }
    if ($num_requests % 10 == 0) {
      log_message('info', 'Requesting BN-College referer page');
      $this->ci->cache->delete('bn_college_user_agent');
      file_put_contents($this->get_cookie(), '');
      $this->make_request($referer);
    }
    $num_requests++;
    $this->ci->cache->save('bn_college_num_requests', $num_requests);

    if ($view == 'TBListView') {
      $defaults = array(
        'storeId'       => $this->store_id,
        'langId'      => '-1',
        'catalogId'     => '10001',
        'savedListAdded'  => 'true',
        'clearAll'      => '',
        'viewName'      => 'TBWizardView',
        'removeSectionId'   => '',
        'mcEnabled'     => 'N',
        'numberOfCourseAlready' => '0',
        'viewTextbooks.x'   => rand(0, 115),
        'viewTextbooks.y'   => rand(0, 20),
        'sectionList'     => 'newSectionNumber'
      );
    }
    else {
      $defaults = array(
        'campusId'      => $this->campus_id,
        'termId'      => '',
        'deptId'      => '',
        'courseId'      => '',
        'sectionId'     => '',
        'catalogId'     => '10001',
        'storeId'       => $this->store_id,
        'langId'      => '-1',
        'dojo.transport'  => 'xmlhttp',
        'dojo.preventCache' => intval(microtime(TRUE)*1000),
      );
    }

    $params = array_merge($defaults, $params);

    return $this->make_request($base . $view, $params, array(CURLOPT_REFERER => $referer));
  }

  protected function make_request($url, $params = array(), $options = array()) {
    if ($params) {
      $url .= '?' . http_build_query($params);
    }

    $cookie = $this->get_cookie();

    $defaults = array(
      CURLOPT_URL => $url,
      CURLOPT_HEADER => FALSE,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_FOLLOWLOCATION => TRUE,
      CURLOPT_SSL_VERIFYPEER => FALSE,
      CURLOPT_VERBOSE => FALSE,
      CURLOPT_ENCODING => 'gzip,deflate',
      CURLOPT_USERAGENT => $this->get_user_agent(),
      CURLOPT_COOKIEFILE => $cookie,
      CURLOPT_COOKIEJAR => $cookie,
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options + $defaults);

    $result = curl_exec($ch);
    if ( ! $result || curl_error($ch)) {
      throw new Exception('Curl failed: ' . curl_error($ch));
    }

    curl_close($ch);
    return $result;
  }

  protected function get_cookie() {
    return realpath(APPPATH . 'cache/cookie');
  }

  protected function get_user_agent() {
    $user_agent = $this->ci->cache->get('bn_college_user_agent');
    if ($user_agent === FALSE) {
      require(APPPATH . 'libraries/Random_UA.php');
      $user_agent = Random_UA::generate();
      $this->ci->cache->save('bn_college_user_agent', $user_agent);
    }
    return $user_agent;
  }

  protected function scrape_dropdown($ancestor_ids, $regex) {
    $params = array();
    $param_map = array(
      'termId'  => 'term',
      'deptId'  => 'department',
      'courseId'  => 'course',
    );
    foreach ($param_map as $param => $type) {
      if ( ! isset($ancestor_ids[$type])) {
        break;
      }
      $params[$param] = $ancestor_ids[$type];
    }
    $response = $this->make_bn_request('TextBookProcessDropdownsCmd', $params);

    if (preg_match_all($regex, $response, $match_sets, PREG_SET_ORDER)) {
      // Remove extra elements returned by preg_match_all().
      foreach ($match_sets as &$match_set) {
        foreach ($match_set as $key => $value) {
          if (is_int($key)) {
            unset($match_set[$key]);
          }
        }
      }
      return $match_sets;
    }
    else if (strpos($response, 'select name') !== FALSE) {
      return FALSE;
    }
    else {
      $ancestors = serialize($ancestor_ids);
      throw new Exception("Error scraping dropdown ($ancestors).\n\nResponse text:\n$response");
    }
  }

  /**
   * Retrieves terms from the BN College website.
   *
   * @return array Arrays with 'bookstore_id', 'name'
   */
  public function get_terms($ancestor_ids) {
    $regex = "/(?P<bookstore_id>\d+)'\s*>(?P<name>[^>]+)</";
    return $this->scrape_dropdown($ancestor_ids, $regex);
  }

  /**
   * Retrieves departments from the BN College website.
   *
   * @return Array of arrays with 'bookstore_id', 'code'
   */
  public function get_departments($ancestor_ids) {
    $regex = "/(?P<bookstore_id>\d+)'\s*>\s*(?P<code>.+?)\s*</";
    return $this->scrape_dropdown($ancestor_ids, $regex);
  }

  /**
   * Retrieves courses from the BN College website.
   *
   * @return array Arrays with 'bookstore_id', 'code'
   */
  public function get_courses($ancestor_ids) {
    $regex = "/(?P<bookstore_id>\d+)'\s*>\s*(?P<code>.+?)\s*</";
    return $this->scrape_dropdown($ancestor_ids, $regex);
  }

  /**
   * Retrieves sections from the BN College website.
   *
   * @return array Arrays with 'bookstore_id', 'no_books', 'code'
   */
  public function get_sections($ancestor_ids) {
    $regex = "/(?P<bookstore_id>\d+)(?P<no_books>[A-Z])_\d*'\s*>(?P<code>.+?)</";
    $sections = $this->scrape_dropdown($ancestor_ids, $regex);
    foreach ($sections as &$section) {
      $section['no_books'] = ($section['no_books'] == 'Y') ? TRUE : FALSE;
    }
    return $sections;
  }

  /**
   * Retrieves books from the BN College website.
   *
   * @param array $section_ids Array of section IDs
   * @return array Array of scraped books. Each book array will have at least
   *               2 keys: "section_id" and "bookstore_id".
   */
  public function get_books($section_ids) {
    $params = array();
    $i = 1;
    foreach ($section_ids as $section_id) {
      $params['section_' . $i++] = $section_id;
    }

    $result = $this->make_bn_request('TBListView', $params);

    if ( ! $result) {
      throw new Exception('Empty response for sections ' . implode(' ', $section_ids));
    }

    $book_divs = explode('tbListHolding', $result);
    unset($book_divs[0]);

    $books = array();
    foreach ($book_divs as $div) {
      $book = $this->parse_book_details($div);
      if ($book && isset($book['section_id']) && isset($book['bookstore_id'])) {
        $books[] = $book;
      }
    }

    return $books;
  }

  /**
   * Parses book details from the given HTML book description.
   *
   * The resulting book-details array will have a subset of the following keys:
   *  - isbn
   *  - title
   *  - authors
   *  - edition
   *  - publisher
   *  - section_id
   *  - bookstore_id
   *  - bookstore_part_number
   *  - bookstore_used_price
   *  - bookstore_new_price
   *  - type
   *  - required_status
   *
   * @param string $html HTML for a book description on the bookstore website
   * @return array An array of book details, or FALSE on error
   */
  protected function parse_book_details($html) {
    if (strpos($html, 'Currently no textbook') || strpos($html, 'Pre-Order')) {
      return FALSE;
    }

    $details = array();

    if (preg_match("/value\='(\d{8})'/", $html, $matches)) {
      $details['section_id'] = $matches[1];
    }
    if (preg_match("/ISBN\:\<\/span\>.+?(\d+).+?\</s", $html, $matches)) {
      $isbn = ISBN::to13(trim($matches[1]), TRUE);
      if ($isbn) {
        $details['isbn'] = $isbn;
      }
    }
    if (preg_match("/\d{5}'\stitle\=\"(.+?)\"\>.+?\<img/s", $html, $matches)) {
      $details['title'] = $this->title_case(trim(htmlspecialchars_decode($matches[1], ENT_QUOTES)));
    }
    if (preg_match("/\<span\>Author:.*?\<\/span\>(.+?)\<\/li/s", $html, $matches)) {
      $author = $this->ucname(trim($matches[1]));
      $details['authors'] = is_numeric($author) ? "" : $author;
    }
    if (preg_match("/Edition:\<\/span\>(.+?)\<br/", $html, $matches)) {
      $details['edition'] = strtolower(trim($matches[1]));
    }
    if (preg_match("/Publisher:\<\/span\>(.+?)\<br/", $html, $matches)) {
      $details['publisher'] = $this->title_case(trim($matches[1]));
    }
    if (preg_match("/productId\=(.+?)&/", $html, $matches)) {
      $details['bookstore_id'] = trim($matches[1]);
    }
    if (preg_match("/partNumber\=(.+?)&/", $html, $matches)) {
      $details['bookstore_part_number'] = rtrim($matches[1], "&amp;");
    }
    if (preg_match("/Used.+?(\d{1,3}\.\d{2})/s", $html, $matches)) {
      $details['bookstore_used_price'] = $matches[1];
    }
    if (preg_match("/New.+?(\d{1,3}\.\d{2})/s", $html, $matches)) {
      $details['bookstore_new_price'] = $matches[1];
    }

    $options = array("REQUIRED\sPACKAGE", "RECOMMENDED\sPACKAGE", "REQUIRED", "RECOMMENDED",
      "PACKAGE\sCOMPONENT", "GO\sTO\sCLASS\sFIRST", "BOOKSTORE\sRECOMMENDED");

    preg_match("/".implode('|', $options)."/", $html, $matches);
    $required = trim($matches[0]);

    if ($required == "REQUIRED PACKAGE" || $required == "RECOMMENDED PACKAGE") {
      $type = Book_model::PACKAGE;
    }
    else if ($required == "PACKAGE COMPONENT") {
      $type = Book_model::PACKAGE_COMPONENT;
    }
    else {
      $type = Book_model::BOOK;
    }
    $details['type'] = $type;

    if ($required == "REQUIRED PACKAGE" || $required == "REQUIRED") {
      $required = Book_model::REQUIRED;
    }
    else if ($required == "RECOMMENDED PACKAGE" || $required == "RECOMMENDED") {
      $required = Book_model::RECOMMENDED;
    }
    else if ($required == "GO TO CLASS FIRST") {
      $required = Book_model::GO_TO_CLASS_FIRST;
    }
    else if ($required == "BOOKSTORE RECOMMENDED") {
      $required = Book_model::BOOKSTORE_RECOMMENDED;
    }
    else {
      $required = Book_model::REQUIRED;
    }
    $details['required_status'] = $required;

    return $details;
  }

  /**
   * Converts a string to title case (e.g. "Of Mice and Men").
   *
   * @param string $string
   * @return string
   */
  protected function title_case($string) {
    $titleCase = array("The", "A", "An", "And", "But", "Or", "Not", "As",
      "At", "By", "For", "In", "From", "Of", "To", "With");

    $name = trim(strtolower($string));
    $answer = preg_replace_callback("/\s[ivx]{1,4}(\Z|\W)/", function ($match) {
              return strtoupper($match[0]);
            }, $name);
    $answer = rtrim(ucwords($answer));
    $answer = preg_replace("/W\//", "w/", $answer);

    foreach ($titleCase as $word) {
      $answer = preg_replace("/\s$word\s/", " " . strtolower($word) . " ", $answer);
    }
    return ucfirst(htmlspecialchars($answer));
  }

  /**
   * Upper-cases a person's name.
   *
   * @link http://php.net/manual/en/function.ucwords.php
   *
   * @param string $string
   * @return string
   */
  protected function ucname($string) {
    $string = ucwords(strtolower($string));

    foreach (array('-', '\'', '/') as $delimiter) {
      if (strpos($string, $delimiter) !== false) {
        $string = implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
      }
    }
    return $string;
  }

}
