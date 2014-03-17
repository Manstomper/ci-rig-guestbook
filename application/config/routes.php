<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = 'guestbook';
$route['api/(:any)'] = 'guestbook_api/$1';
$route['user/(:any)'] = 'user/$1';
$route['(:num)'] = 'guestbook/index/$1';
$route['(:any)'] = 'guestbook/$1';
$route['(:any)/(:num)'] = 'guestbook/$1/$2';

/* End of file routes.php */
/* Location: ./application/config/routes.php */