<?php

/**
 * Settings Manager Static Class file definition
 *
 * @author polux
 * @package     Core
 * @subpackage  Settings
 */
namespace Core\Settings;

use Core\Logs\Logger as Logger;

/**
 * Settings Manager Static Class Definition
 * @static
 *
 */
class SettingsManager {

  /**
   * Default Settings filename
   *
   * @var string
   */
  private static $_sDefaultSettingsFilename = 'applications.settings.json';

  /**
   * Databases connections settings
   * @var array(db settings)
   */
  private static $_aDatabasesConnections = [];

  /**
   * Loggers settings
   * @var array(Logger)
   */
  private static $_aLoggers = [];

  /**
   * loadSettingsFromJsonFile
   *
   * Load settings from a json file.
   * File existance checked.
   *
   * @param string $filepath Relative filepath to Json Settings  file to load.
   * @throws \Exception if file not exists
   */
  public static function loadSettingsFromJsonFile($filepath){

    // File existance checks!
    if(!file_exists($filepath)){
      throw new \Exception(sprintf("Settings file can't be reached ! (filepath:'%s').",$filepath));
    }

    // JSON decoding.
    $str = file_get_contents($filepath);
    $json = json_decode($str, true);

    // TODO Gérer erreur de chargement  ....

    /**************************************************************************/
    /* Databases Settings Management                                          */
    /**************************************************************************/
    // Databases connections loading!
    if(!array_key_exists('databases',$json) || count($json['databases']) == 0)
    {
      throw new \Exception("No databases connections defined !");
    }

    // Adding Databases informations into static variable.
    foreach($json['databases'] as $laDBConn)
    {
      static::$_aDatabasesConnections[$laDBConn['id']] = $laDBConn;
    }

    /**************************************************************************/
    /* Logger Settings Management                                             */
    /**************************************************************************/
    // Databases connections loading!
    if(!array_key_exists('loggers',$json) || count($json['loggers']) == 0)
    {
      throw new \Exception("No Loggers defined !");
    }

    // Adding Databases informations into static variable.
    foreach($json['loggers'] as $laDBConn)
    {
      static::$_aLoggers[$laDBConn['id']] = $laDBConn;
    }

  }//end loadSettingsFromJsonFile()

  /**
   * Returns all DB informations
   */
  public static function getAllDatabaseConnectionSettings()
  {
      return static::$_aDatabasesConnections;
  }//end getAllDatabaseConnectionSettings()

  /**
   * Returns all Loggers Settings
   */
  public static function getAllLoggersSettings()
  {
      return static::$_aLoggers;
  }//end getAllLoggersSettings()

}//end class

?>
