<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "my_voices";
$route['logout'] = "logout";

$route['merger/(:any)'] = "merger/$1";
$route['notification'] = "notification";
$route['invitation'] = "invitation";
$route['messages'] = "messages";
$route['messages/(:any)'] = "messages/$1";

$route['my-votes'] = "my_vote_voices";
$route['my-votes/(:any)'] = "my_vote_voices/$1";
$route['my-voices'] = "my_voices";
$route['my-voices/(:any)'] = "my_voices/$1";
$route['voices/(:any)'] = "voices/$1";
$route['voice/(:num)'] = "voices/single_voice/$1";
$route['create-voice'] = "create_voice";
$route['check-voice'] = "voice_checker";

$route['my-streams'] = "my_streams";
$route['my-streams/(:any)'] = "my_streams/$1";
$route['stream/(:num)'] = "stream/single_stream/$1";
$route['stream/(:any)'] = "stream/$1";

$route['my-rivers/create'] = "create_river/create";
$route['my-rivers/create/(:any)'] = "create_river/index/$1";
$route['river/(:num)'] = "river/single_river/$1";
$route['my-rivers'] = "my_rivers";
$route['my-rivers/(:any)'] = "my_rivers/$1";

$route['my-oceans/create'] = "create_ocean/create";
$route['my-oceans/create/(:any)'] = "create_ocean/index/$1";
$route['ocean/(:num)'] = "ocean/single_ocean/$1";
$route['my-oceans'] = "my_oceans";
$route['my-oceans/(:any)'] = "my_oceans/$1";


$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */