<?php

/**
 * Provides access to Amazon's Product Advertising API.
 */
class Amazon_API {

  /**
   * The version of the API to use.
   */
  const API_VERSION = '2011-08-01';

  /**
   * The XML namespace used for API responses.
   */
  const XML_NAMESPACE = 'http://webservices.amazon.com/AWSECommerceService/2011-08-01';

  /**
   * Your Amazon Access Key Id
   * @access private
   * @var string
   */
  private $public_key;

  /**
   * Your Amazon Secret Access Key
   * @access private
   * @var string
   */
  private $private_key;

  /**
   * Your Amazon Associate Tag
   * Now required, effective from 25th Oct. 2011
   * @access private
   * @var string
   */
  private $associate_tag;

  /**
   * @param array $params Array with the following keys:
   *   access_key_id: Amazon "Access Key ID"
   *   secret_access_key: Amazon "Secret Access Key"
   *   associate_tag: Amazon "Associate Tag"
   */
  public function __construct($params) {
    $this->public_key = $params['access_key_id'];
    $this->private_key = $params['secret_access_key'];
    $this->associate_tag = $params['associate_tag'];
  }

  /**
   * Queries the Amazon Product Advertising API with the given parameters.
   *
   * See the Amazon Product Advertising API Developer Guide for parameter info:
   * @see http://docs.aws.amazon.com/AWSECommerceService/2011-08-01/DG/CHAP_OperationListAlphabetical.html
   *
   * @param array $params Array of parameters
   * @return simpleXMLElement The XML response object, or false on error
   */
  public function query($params) {
    $request_url = $this->getRequestUrl($params);
    $xml_response = $this->makeThrottledRequest($request_url);
    if ($xml_response !== FALSE) {
      return $this->parseResponse($xml_response);
    }
    return FALSE;
  }

  /**
   * Returns a signed URL for an Amazon API request.
   *
   * Based on aws_signed_request() by Ulrich Mierendorff.
   * @see http://www.ulrichmierendorff.com/software/aws_hmac_signer.html
   *
   * @param array $params Array of parameters
   * @return string
   */
  private function getRequestUrl($params) {
    $method = 'GET';
    $host = 'webservices.amazon.com';
    $uri = '/onca/xml';

    $params['Service'] = 'AWSECommerceService';
    $params['AWSAccessKeyId'] = $this->public_key;
    $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
    $params['Version'] = self::API_VERSION;
    $params['AssociateTag'] = $this->associate_tag;

    ksort($params);

    $canonicalized_query = array();
    foreach ($params as $param => $value) {
      $param = str_replace('%7E', '~', rawurlencode($param));
      $value = str_replace('%7E', '~', rawurlencode($value));
      $canonicalized_query[] = $param . '=' . $value;
    }
    $canonicalized_query = implode('&', $canonicalized_query);

    $string_to_sign = $method . "\n" . $host . "\n" . $uri . "\n" . $canonicalized_query;

    // Calculate HMAC with SHA256 and base64-encoding
    $signature = base64_encode(hash_hmac('sha256', $string_to_sign, $this->private_key, TRUE));

    // Encode the signature for the request
    $signature = str_replace('%7E', '~', rawurlencode($signature));

    return 'http://' . $host . $uri . '?' . $canonicalized_query . '&Signature=' . $signature;
  }

  /**
   * Sends an HTTP request to the given URL and returns the response.
   *
   * Requests are sent with a 1-second delay to accomodate Amazon's per-second
   * request limits. See this page for more information:
   * @see https://affiliate-program.amazon.com/gp/advertising/api/detail/faq.html
   *
   * @param string $request_url The URL to request
   * @return string The raw XML response, or false on error
   */
  private function makeThrottledRequest($request_url) {
    sleep(1);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $request_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    return curl_exec($ch);
  }

  /**
   * Parses the given response string into an XML object.
   *
   * @param string $xml_response Raw XML string
   * @return simpleXmlObject
   */
  private function parseResponse($xml_response) {
    // Remove namespace declaration for easier XPath querying via SimpleXML.
    $xml_response = str_replace('xmlns="' . self::XML_NAMESPACE . '"', '', $xml_response);
    return simplexml_load_string($xml_response);
  }

}
