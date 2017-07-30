<?php

/**
 * Log Manager Static Class file definition
 *
 * @author polux
 * @package     Core
 * @subpackage  Logs
 */
namespace Core\Logs;

/**
 * Log Manager Static Class Definition
 * @static
 *
 */
class LogsManager {

  /**
   * Logs files Handler
   *
   * @var array(files handler)
   */
  private static $_aLoggersHandler = [];

  /**
   * Default Output Path to store logs files
   * @var string
   */
  protected static $_sOutputPath = "";

  public static function getLogger($idLogger){
    if(array_key_exists($idLogger,static::$_aFilesHandler)){
      return static::$_aFilesHandler[$idLogger];
    }
    else {
      throw new \Exception("Errueur Loggger non trouvé");
    }
  }//end getLogger()

  public static function addLogger($idLogger,$filepath){
    if(!array_key_exists($idLogger,static::$_aLoggersHandler)){
      static::$_aLoggersHandler[$idLogger] = new Logger($idLogger,$filepath,true);
    }
    else {
      throw new \Exception(sprintf("Logger with ID '%s' already registered!",$idLogger));
    }
  }//end addLogger()



}//end class

 ?>
