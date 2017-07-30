<?php

namespace Core\Database;

/**
 * Main Application Database Class File Definition
 *
 * @package     Core
 * @subpackage  Database
 */

class DatabaseManager {

  /**
   * Array of PDO DB Handler
   *
   * @var array(\PDO)
   */
  private static $_aDBPdoDBHanlder = [];

  /**
   * registerDbPDODBHandler
   *
   * Register a new  PDO DB Handler
   * @static
   * @param \PDO    $pObjPDODBHandler   PDO DB Handler Object.
   * @param string  $dbID               UID of Database Connection
   */
  public static function registerDbPDOHandler(\PDO $pObjPDODBHandler,$dbID=null)
  {
    // TODO Check existence dans le tableau avant allocation!
    $ldbID = $dbID;
    if($dbID === null)
    {
      $ldbID = 'DEFAULT';
    }
    static::$_aDBPdoDBHanlder[$ldbID] = $pObjPDODBHandler;
  }//end registerDbPDODBHandler()

  /**
   * initDatabaseHandler
   *
   * Returns  a PDO Database Handler initialized.
   * @static
   *
   */
  public static function initDatabaseHandler($dsn,$login,$pass,$dbID=null){
      $dbh = new \PDO($dsn,$login,$pass);

      if(!is_null($dbID))
      {
        static::registerDbPDOHandler($dbh,$dbID);
      }
      return $dbh;
  }//end initDatabaseHandler()

  /**
   * getPDODatabaseHandler
   *
   * Returns Application Default Database Handler Object
   * @static
   * @deprecated
   *
   * @return \PDO   DB Handler Object
   */
  public static function getPDODatabaseHandler_OLD(){
    return static::getSpecificPDODatabaseHandler('DEFAULT');
  }//end getPDODatabaseHandler()

  /**
   * getSpecificPDODatabaseHandler
   *
   * Returns Specific  Database Handler Object
   * @static
   * @param string $dbInternalID  Internal ID of Database PDO Handler
   * @throws \Exception If DB not founded or null.
   *
   * @return \PDO   DB Handler Object
   */
  public static function getPDODatabaseHandler($dbInternalID){
    if(count(static::$_aDBPdoDBHanlder) === 0 || !array_key_exists($dbInternalID,static::$_aDBPdoDBHanlder) || static::$_aDBPdoDBHanlder[$dbInternalID] === null)
    {
      throw new \Exception(sprintf("DB Handler '%s' not founded or not initialized !.",$dbInternalID));
    }
    return static::$_aDBPdoDBHanlder[$dbInternalID];
  }//end getSpecificPDODatabaseHandler()

}//end class

 ?>
