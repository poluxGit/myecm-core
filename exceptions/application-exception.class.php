<?php

namespace Core\Exceptions;

use MyDocs\MyApp as Application;
/**
 * Application Exception Classe definition
 *
 * @link app-exceptions.json
 */
class ApplicationException extends \Exception {

  private static $_fileExceptionDefinitions = null;

  /**
   * Default constructor
   *
   * @param string        $pStrAppExUniqueCode  Unique code of exceptions
   * @param array(mixed)  $pArrParameters       Values to inject into Exception message
   */
  public function __construct($pStrAppExUniqueCode,$pArrParameters=null)
  {
      $lStrExceptionMessage = static::getExceptionMessageFromCode($pStrAppExUniqueCode);
      $lStrExceptionFinalMessage = "";

      if(!is_null($lStrExceptionMessage))
      {
        if(!is_null($pArrParameters) && count($pArrParameters) > 0)
        {
          $lStrExceptionFinalMessage = vsprintf($lStrExceptionMessage,$pArrParameters);
        }
        else {
          $lStrExceptionFinalMessage = $lStrExceptionMessage;
        }
      }
      else {
        $lStrExceptionFinalMessage = sprintf(
          "An Exception with an unknow code '%s' was throwed ! (Parameters:'%s').",
          $pStrAppExUniqueCode,
          implode(', ',$pArrParameters)
        );
      }
      parent::__construct($lStrExceptionFinalMessage."\n<BR/>");
  }//end __construct()

  /**
   * Defines Exception Messages defintion file
   *
   * @param file $pStrJSONFilePath  Filepath of JSON Exception Messages defintion file.
   * @throws \Exception
   */
  static function setExceptionDefinitionFile($pStrJSONFilePath)
  {
     if(file_exists($pStrJSONFilePath))
     {
       self::$_fileExceptionDefinitions = $pStrJSONFilePath;
     }
     else {
       throw new \Exception("FATAL ERROR - Internal Exception Messages definition file can't be load ! (file:'%s'). Please contact your administrator.");
     }
  }//end setExceptionDefinitionFile()

  /**
   * Returns Exception Message Definition
   *
   * @static
   * @param string $pStrExceptionUniqueCode   Unique internal code of exception
   *
   * @return string NULL if not found.
   */
  static function getExceptionMessageFromCode($pStrExceptionUniqueCode)
  {
    // Exception Messages file defined ?
    if(is_null(static::$_fileExceptionDefinitions))
    {
      throw new \Exception("FATAL ERROR - Exception Messages file not defined !.");
    }

    $lArrMessages = json_decode(file_get_contents(static::$_fileExceptionDefinitions),true);
    $lStrResult   = "";

    echo ((Application::isDebugMode())?sprintf("DEBUG => Exception code reader '%s'.",$pStrExceptionUniqueCode):"")."<BR/>";
    if(array_key_exists($pStrExceptionUniqueCode,$lArrMessages))
    {
      $lStrResult = strval($lArrMessages[$pStrExceptionUniqueCode]);
    }
    return ($lStrResult==""?null:$lStrResult);
  }//end getExceptionMessageFromCode()

}//end class

?>
