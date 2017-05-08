<?php

/**
 * MyECM API Restfull - Main EntryPoint
 *
 * Route Requests
 *
 * @author polux <polux@poluxfr.org>
 */

echo "MyECM - API RESTFULL <BR/><BR/>";

echo "Requests => <BR/>";
print_r($_REQUEST);




//
// require_once '../../appli.inc.php';
//
// use MyGED\Core\Application as Application;
// use MyGED\Vault as Vault;
// use MyGED\Application\AppAPIRouter as AppAPI;
// use MyGED\Application\App as App;
//
// // Initialise Application!
// App::initApplication();
//
// // Requests from the same server don't have a HTTP_ORIGIN header
// if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
//     $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
// }
//
// // Main Processing !
// try {
//     $API = new AppAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
//     echo $API->processAPI();
//     // TODO Gérer le retour avec code de retour en json et msg + data... aplha-02
// } catch (Exception $e) {
//     echo json_encode(Array('error' => $e->getMessage()));
// }

exit;

 ?>
