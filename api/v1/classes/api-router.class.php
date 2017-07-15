<?php

namespace Core\API;
use Externals\CsvImporter as CsvImp;
use Core\Database\DatabaseManager as CoreDB;

/**
 * APIRouter Class file definition
 *
 * @package Core
 * @subpackage API
 */


/**
 * APIRouter
 *
 * Routes Management of all API.
 */
class APIRouter
{
    /**
     * Property: $_routes
     *
     * @static
     * @var array()
     */
    protected static $_routes = [];

    /**
     * Property: $routes_filename
     */
    private static $routes_filename = './conf/api-routes.csv';

    /**
     * runRequest
     *
     * Run a request
     * @todo decompose SQL Management into separate class
     *
     * @return integer Number of lines founded.
     */
    public static function runRequest(APIRequest $poRequest){

      // First call management!
      if(count(static::$_routes) == 0)
      {
        static::loadRoutesFromCSVFile();
      }

      // Step 01 - Identification into routes known!
      $lArrRoute = static::findARoute($poRequest->getHTTPMethod(),$poRequest->getEndpoint(),count($poRequest->getArgs()));

      // Step 02 - SQL Order existance check !
      if(array_key_exists('SQLOrder',$lArrRoute)){
        static::runSQLRequest($poRequest,$lArrRoute);
      }
      else {
        throw new \Exception('No valid SQLOrder defined!');
      }

    }//end runRequest()

    /**
     * loadRoutesFromCSVFile
     *
     * Load Routes in memory from a CSV file.
     */
    protected static function loadRoutesFromCSVFile($csvfilepath = null){

      // API Routes loading from CSV  !
      $lsCSVFileToLoad = (is_null($csvfilepath))?static::$routes_filename:$csvfilepath;
      $lObjCSVFile = New CsvImp($lsCSVFileToLoad,true,';');
      $lArrData = $lObjCSVFile->get();

      // Routes formatting & Storage !
      foreach($lArrData as $lArrRow )
      {
        $liNbParams = 0;
        $row_param = [];

        while(!empty($lArrRow['param'.($liNbParams+1)]) && $liNbParams < 5) {
          $row_param['params']['param'.($liNbParams+1)] = $lArrRow['param'.($liNbParams+1)];
          $liNbParams++;
        }
        $row_param['SQLOrder'] =  $lArrRow['SQLOrder'];
        $row_param['nbParams'] =  $liNbParams;
        static::$_routes[$lArrRow['http_method']][$lArrRow['endpoint']][] = $row_param;
      }

    }//end loadRoutesFromCSVFile()

    /**
     * findARoute
     *
     * Seek a route from his method, entrypoint and numberOfParams
     *
     * @param string  $http_method  HTTP Method.
     * @param string  $entrypoint   Entrypoint asked.
     * @param integer $nbParams     Number of params.
     *
     * @throws Exception if route can't be identified.
     *
     * @return array()  Route definition founded.
     */
    protected static function findARoute($http_method, $entrypoint, $nbParams)
    {
      $key = -1;
      if(!array_key_exists($http_method,static::$_routes)) {
        throw new \Exception(
          sprintf('No route defined with "%s" method into API Routes.',$http_method)
        );
      }
      elseif(!array_key_exists($entrypoint,static::$_routes[$http_method])) {
        throw new \Exception(
          sprintf('No route defined with "%s" entrypoint for "%s" method into API Routes.',$entrypoint,$http_method)
        );
      }
      else {
          $key = array_search($nbParams, array_column(static::$_routes[$http_method][$entrypoint], 'nbParams'));
      }

      // No route founded!
      if($key == -1 || !@array_key_exists($key,static::$_routes[$http_method][$entrypoint]))
      {
        throw new \Exception('No route founded.');
      }
      return static::$_routes[$http_method][$entrypoint][$key];
    }//end findARoute()

    // GENERIC REQUEST MANAGEMENT
    // -------------------------------------------------------------------------
    // Case 1 : POST (INSERT)
    // Case 2 : GET (SELECT)
    // Case 3 : PUT (UPDATE)
    // Case 4 : DELETE (DELETE)
    // -------------------------------------------------------------------------
    private static function runSQLRequest(APIRequest $poRequest,$route){

      $lxResult = null;

      // Exception MANAGEMENT
      //   $this->status = 'FAILED';
      switch($poRequest->getHTTPMethod()){
        case 'GET':
          static::runSQLDataSelectRequest($poRequest,$route);
          break;
        case 'POST':
          static::runSQLDataUpdateRequest($poRequest,$route);
          break;
        case 'PUT':
          static::runSQLDataUpdateRequest($poRequest,$route);
          break;
        case 'DELETE':
          static::runSQLDataUpdateRequest($poRequest,$route);
          break;
        default:
          break;
      }
    }//end runSQLRequest

    /**
     * runSQLDataSelectRequest
     *
     * Set the result of SQL Query to Request Object in parameters.
     *
     * @param APIRequest $poRequest SELECTR equest Object (GET HTTP-Method)
     *
     * @return integer  Number of rows.
     */
    protected static function runSQLDataSelectRequest(APIRequest $poRequest,$route)
    {
      // get Common Database!
      $dbh = CoreDB::getPDODatabaseHandler();

      // Without Parameters
      if(count($poRequest->getArgs()) == 0) {
          $sth = $dbh->query($route['SQLOrder']);
      }
      else {
        $sth = $dbh->prepare($route['SQLOrder'], array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $lArrParamsValues = [];
        $lArrArgs = $poRequest->getArgs();

        foreach($route['params'] as $lStrKey => $lStrValue){
          $lArrParamsValues[':'.$lStrKey] = array_shift($lArrArgs);
        }

        $sth->execute($lArrParamsValues);
      }
      $lArrData = $sth->fetchAll(\PDO::FETCH_ASSOC);
      $poRequest->setData($lArrData);
      return count($lArrData);
    }//end runSQLDataSelectRequest()


    /**
     * runSQLDataUpdateRequest
     *
     * Returns last ID
     *
     * @param APIRequest $poRequest SELECTR equest Object (GET HTTP-Method)
     *
     * @return integer  Number of rows.
     */
    protected static function runSQLDataUpdateRequest(APIRequest $poRequest,$route)
    {
      // get Common Database!
      $dbh = CoreDB::getPDODatabaseHandler();

      // Without Parameters
      if(count($poRequest->getArgs()) == 0) {
          $liNbRows = $dbh->exec($route['SQLOrder']);
      }
      else {
        $sth = $dbh->prepare($route['SQLOrder'], array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $lArrParamsValues = [];
        $lArrArgs = $poRequest->getArgs();

        foreach($route['params'] as $lStrKey => $lStrValue){
          $lArrParamsValues[':'.$lStrKey] = array_shift($lArrArgs);
        }
        $liNbRows =  $sth->execute($lArrParamsValues);
      }
      $poRequest->setData($liNbRows);
      return $liNbRows;
    }//end runSQLDataUpdateRequest()


}//end class
