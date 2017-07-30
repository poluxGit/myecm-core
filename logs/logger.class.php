<?php

/**
 * Logger Class Definition file
 * @package Core
 * @subpackage Logs
 */

namespace Core\Logs;

/**
 * Logger Class Definition
 */
class Logger {

    protected $_loggerID = null;

    protected $_filename = null;

    protected $_filepath = null;

    private $_filehandler = null;

    /**
     * Default Constructor of a Logger Object
     *
     * @return Logger
     */
    public function __construct($loggerID,$outputpath=null,$dateprefix=true){
      $this->_loggerID = $loggerID;

      if(!is_null($outputpath))
      {
        $this->_filepath = $outputpath;
      }
      else {
        // TODO dev si output directory not OK, not defined
      }

      $this->_updateFilenameCompletePath($dateprefix);

      try{
        $this->_filehandler = fopen($this->_filename,'a');
        // TODO Gestion erreur d'ouverture ...
      }catch(\Exception $ex){

      }

    }//end __construct()


    public function logMessage($message)
    {
      if(is_null($this->_filehandler))
      {
        throw new \Exception("Message can't be log because the logger isn't correctly initiliazed.");
      }

      fwrite($this->_filehandler,$this->getMessageFormatedToLog($message));
    }//end logMessage()

    protected function getMessageFormatedToLog($message)
    {
      return '[ '.date('Ymd-H:i:s').' ] - '.$message.PHP_EOL;
    }

    /**
     * _updateFilenameCompletePath
     *
     * Update complete filename to used.
     * @param boolean $dateprefix if TRUE - DateHourMinute will prefix the fiename returs.
     *
     *
     */
    private function _updateFilenameCompletePath($dateprefix)
    {
      $lFilename =  $this->_filepath.'/';
      if($dateprefix==true)
      {
        $lFilename .= date('Ymd-H')."_";
      }
      $lFilename .=  strtolower($this->_loggerID).'.log';
      $this->_filename = $lFilename;
    }//end _updateFilenameCompletePath()

/*
$data = 'some data'.PHP_EOL;
$fp = fopen('somefile', 'a');
fwrite($fp, $data);
*/
    /**
     * Destructor of a Logger Object
     */
    public function __destruct(){
      if(!is_null($this->_filehandler))
      {
        fclose($this->_filehandler);
      }
    }//end __construct()


}//end class
?>
