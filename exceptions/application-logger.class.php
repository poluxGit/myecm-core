<?php

namespace MyDocs\Application;

/**
 * Application Logger class definition
 */
class ApplicationLogger {

  static $_aLogs = array();

  public static function log($pStrMessage)
  {
    array_push(static::$_aLogs,$pStrMessage);
  }
}

?>
