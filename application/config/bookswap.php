<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| UI Strings
|--------------------------------------------------------------------------
*/
$config['ui_strings'] = array(
  'site_name'         => 'BookSwap',
  'university_name'   => 'Your University',
  'university_url'    => 'http://www.example.edu/',
  'organization_name' => 'Your Student Government',
  'organization_url'  => 'http://www.example.com/',
);

/*
|--------------------------------------------------------------------------
| Amazon API Requests Per Cron Run
|--------------------------------------------------------------------------
|
| The number of requests to make to the Amazon Product Advertising API each
| time the update_amazon_data cron task is run. Each request updates 10
| books in the database, and the requests are automatically throttled to
| 1 request per second to accomodate Amazon's request limits.
|
*/
$config['amazon_requests_per_cron'] = 5;
