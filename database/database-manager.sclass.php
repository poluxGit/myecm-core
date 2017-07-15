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
   * PDO DB Handler
   *
   * @var \PDO
   */
  private static $_oPdoDBHanlder = null;

  /**
   * setPDODBHandler
   *
   * Set application default Database Handler
   * @static
   * @param \PDO $pObjPDODBHandler  PDO DB Handler Object.
   */
  public static function setPDODBHandler(\PDO $pObjPDODBHandler)
  {
    static::$_oPdoDBHanlder = $pObjPDODBHandler;
  }//end setPDODBHandler()

  /**
   * initDatabaseHandler
   *
   * Initialize Application default Database Handler
   * @static
   *
   */
  public static function initDatabaseHandler(){
      $dbh = new \PDO('mysql:host=polux-nas;port=3306;dbname=myecm','polux','Odomzhzf31');
      static::setPDODBHandler($dbh);
  }//end initDatabaseHandler()

  /**
   * getPDODatabaseHandler
   *
   * Returns Application Default Database Handler Object
   * @static
   *
   * @return \PDO   DB Handler Object
   */
  public static function getPDODatabaseHandler(){
      if(static::$_oPdoDBHanlder === null)
      {
        static::initDatabaseHandler();
      }
      return static::$_oPdoDBHanlder;
  }//end getPDODatabaseHandler()

}//end class

 ?>
