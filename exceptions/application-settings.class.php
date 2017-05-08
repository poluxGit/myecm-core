<?php

namespace MyDocs\Application;
/**
 * Default Application Classes definition
 */
class ApplicationSettings {

	# Common behaviour only
	private $aSettingsValues = array();
	private $sSettingsFilepath = null;

	/**
	 * Constructor of ApplicationSettings Class.
	 *
	 * @param filepath	$pStrSettingsFilepath	File path about Settings values
	 */
	public function __construct($pStrSettingsFile)
	{
		$this->sSettingsFilepath = $pStrSettingsFile;
		$this->loadSettingsFromFile($pStrSettingsFile);
	}//end __construct()

	/**
	 * Load settings in memory from a file
	 *
	 * @access protected
	 *
	 * @param filepath	$pStrSettingsFilepath	File path about Settings values
	 * @throws \Exception If file can be reached
	 *
	 * @return boolean TRUE if load is OK
	 */
	protected function loadSettingsFromFile($pStrSettingsFile)
	{
		// check if file is reacheable ?
		if(is_file($pStrSettingsFile))
		{
			$lStrJSONContent = file_get_contents($pStrSettingsFile);
			$this->aSettingsValues = json_decode($lStrJSONContent, true);
		}
		else {
			// File not valid!
			$lStrMessage = sprintf("** ERROR ** : Settings file '%s' is not valid or reacheable.",$pStrSettingsFile);
			throw new \Exception($lStrMessage);
		}

		return true;
	}//end loadSettingsFromFile()

	/**
	 * Instanciate an ApplicationSettings Object from a settings File
	 *
	 * @param filepath $pStrSettingsFile Settings filepath
	 *
	 * @return MyDocs\Application\ApplicationSettings
	 */
	public static function loadApplicationSettingsFromFile($pStrSettingsFile)
	{
		return new ApplicationSettings($pStrSettingsFile);
	}// end loadApplicationSettingsFromFile()

	/**
	 * Returns a Database setting value.
	 *
	 * @param string $pStrSettingName	DB Setting name to get.
	 * @throws \Exception if seetings aren't loaded in memory.
	 *
	 * @return mixed DB Setting value.
	 */
	public function getDatabaseSettings($pStrSettingName)
	{
		if(count($this->aSettingsValues)==0){
			throw new \Exception("** ERROR - Settings must be loaded before can be used.");
		}

		if(array_key_exists($pStrSettingName,$this->aSettingsValues['database']))
		{
			return $this->aSettingsValues['database'][$pStrSettingName];
		}
		else
		{
			return null;
		}
	}//end getDatabaseSettings()

	/**
	 * Returns a Path setting value.
	 *
	 * @param string $pStrSettingName	Path Setting name to get.
	 * @throws \Exception if seetings aren't loaded in memory.
	 *
	 * @return mixed Path Setting value.
	 */
	public function getPathSettings($pStrSettingName)
	{
		if(count($this->aSettingsValues)==0){
			throw new \Exception("** ERROR - Settings must be loaded before can be used.");
		}

		if(array_key_exists($pStrSettingName,$this->aSettingsValues['paths']))
		{
			return $this->aSettingsValues['paths'][$pStrSettingName];
		}
		else
		{
			return null;
		}
	}//end getPathSettings()

	/**
	 * Returns an Application setting value.
	 *
	 * @param string $pStrSettingName	Application Setting name to get.
	 * @throws \Exception if seetings aren't loaded in memory.
	 *
	 * @return mixed Application Setting value.
	 */
	public function getSettings($pStrSettingName)
	{
		if(count($this->aSettingsValues)==0){
			throw new \Exception("** ERROR - Settings must be loaded before can be used.");
		}

		if(array_key_exists($pStrSettingName,$this->aSettingsValues['app-specs']))
		{
			return $this->aSettingsValues['app-specs'][$pStrSettingName];
		}
		else
		{
			return null;
		}
	}//end getSettings()

}//end class

?>
