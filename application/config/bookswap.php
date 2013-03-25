<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Google Analytics Tracking ID
|--------------------------------------------------------------------------
*/
$config['google_analytics_id'] = '';

/*
|--------------------------------------------------------------------------
| UI Strings
|--------------------------------------------------------------------------
*/
$config['ui_strings'] = array(
  'site_name'         => 'BookSwap',
  'university_name'   => 'Your University',
  'university_url'    => 'http://www.example.edu/',
  'university_abbr'   => 'YU',
  'organization_name' => 'Your Student Government',
  'organization_url'  => 'http://www.example.com/',
  'bookstore_name'    => 'Campus Bookstore',
  'bookstore_url'     => 'http://www.example.com/',
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

/*
|--------------------------------------------------------------------------
| Book Conditions
|--------------------------------------------------------------------------
|
| Indexed array of strings that users can use to specify the condition of
| their books. The indices of this array are used as values for the
| "condition" column in the "posts" database table.
|
*/
$config['book_conditions'] = array(
  1 => 'Like New',
  2 => 'Very Good',
  3 => 'Good',
  4 => 'Acceptable',
);
