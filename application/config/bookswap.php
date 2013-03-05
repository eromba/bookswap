<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
