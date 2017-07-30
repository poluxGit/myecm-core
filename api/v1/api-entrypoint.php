<?php

/**
 * MyECM API Restfull - Main EntryPoint
 *
 * @author polux <polux@poluxfr.org>
 */

 // Mandatory dependencies
 require_once './api.inc.php';
  require_once './../../core.inc.php';

 use Core\API as API;

 Core\Application::initApplication();

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
      $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

// API Request Main Management!
try{
  // Valid request !
  if (array_key_exists('request',$_REQUEST)){
    $lORequest = new API\APIRequest($_REQUEST['request'],$_SERVER['HTTP_ORIGIN']);
    API\APIRouter::runRequest($lORequest);
    echo $lORequest->_response(200);
    exit;
  }
  elseif (array_key_exists('debug',$_REQUEST)) {
    // Implements Diagnose mode!
    echo "MyECM - API RESTFULL <BR/>";
    echo "<HR/>";

    echo "HTTP Request Method :".$_SERVER['REQUEST_METHOD'];

    echo "<BR/>";
    echo "<BR/>";
    echo "<HR/>";
    echo "REQUEST Object :<BR/>";

    echo "<BR/>";
    print_r($_REQUEST);
    echo "<HR/>";
    echo "SERVER Object :<BR/>";

    echo "<BR/><p><pre>";
    print_r($_SERVER);
    echo "</pre></p>";
  }
  else {
    throw new Exception('Invalid request. Please refers to API Entrypoints documentation.');
  }
} catch(Exception $ex){

  $laResult = [];
  $laResult['type'] = 'error' ;
  $laResult['data'] = null ;
  $laResult['msg'] = $ex->getMessage();
  $laResult['infos'] = null;

  echo json_encode($laResult);
}

exit;

 ?>
