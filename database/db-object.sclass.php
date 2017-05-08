<?php

/**
 * Database Static Object
 *
 * Provide access to SQL Database.
 *
 * @author polux <polux@poluxfr.org>
 */
namespace Core\Database;

use MyDocs\Application\ApplicationException as AppException;

/**
 * DatabaseObject Classe
 *
 * Methods allowing Database Storage of herited class.
 */
 class DatabaseObject {

   /**
    * Common Static Database PDO Statement Object
    * Common Static Database \PDO Statement Object
    *
    * @static
    * @var PDOStatement
    */
   static $_commonDatabasePdoStatement = null;

   /**
    * Current Database PDO Statement Object
    *
    * @var PDOStatement
    * @access protected
    */
   protected $objDBPdo            = null;
   protected $arrFields           = array();
   protected $strTablename        = null;
   protected $arrPrimaryKeyFields = null;
   protected $arrForeignKeyFields = null;
   protected $arrDbFieldsValues   = array();
   protected $arrNewFieldsValues  = array();

   /**
    * DatabaseObject class constructor
    *
    * @internal if no PDO Connection is set through params, static default object will be used.
    *
    * @param PDOStatement $pObjTargetDBPDOStatement PDO statement [OPTIONAL]
    */
   public function __construct($pObjTargetDBPDOStatement=null)
   {
     if(!is_null($pObjTargetDBPDOStatement))
     {
       $this->objDBPdo = $pObjTargetDBPDOStatement;
     }
     else {
       $this->objDBPdo = self::$_commonDatabasePdoStatement;
     }
   }//end __construct()

   /**
    * Set Common PDO Statement DB Handler Object
    */
   static function setCommonPDOStatementObject($pObjDBPDOStatement)
   {
     if(is_null($pObjDBPDOStatement))
     {
       throw new AppException('DATABASE-PDO-INVALID',null);
     }else {
       self::$_commonDatabasePdoStatement = $pObjDBPDOStatement;
     }
   }//end setCommonPDOStatementObject()

   /**
    * Reset to null all Fields value of object (Not in DB)
    *
    * @access protected
    */
   protected function resetFieldsValueInternalArrays()
   {
     unset($this->arrDbFieldsValues);
     unset($this->arrNewFieldsValues);

     $this->arrDbFieldsValues = array();
     $this->arrNewFieldsValues = array();

     foreach( $this->arrFields as $lStrFieldName)
     {
       $this->arrDbFieldsValues[$lStrFieldName] = null;
       $this->arrNewFieldsValues[$lStrFieldName] = null;
     }
   }//end resetFieldsValueInternalArrays()

   /**
    * Add new fieldname into the array of Fields definition
    *
    * @access protected
    */
   protected function addField($pStrFieldName,$pMixedInitialValue=null)
   {
     if(!in_array($pStrFieldName,$this->arrFields))
     {
       array_push($this->arrFields,$pStrFieldName);
       $this->arrFieldsValues[$pStrFieldName] = $pMixedInitialValue;
     }
   }//end addField()

   /**
    * Set a Field value of Object
    *
    * @param string $pStrFieldName  Field name to set.
    * @param mixed  $pXValue        Value to set.
    */
   protected function setFieldValue($pStrFieldName,$pXValue)
   {
     $this->arrNewFieldsValues[$pStrFieldName] = $pXValue;
   }

   /**
    * Get a Field value
    *
    * @param string $pStrFieldName  Field name to get.
    * @return mixed  Field value
    */
   protected function getFieldValue($pStrFieldName)
   {
     if(!empty($this->arrNewFieldsValues[$pStrFieldName]))
     {
       return $this->arrNewFieldsValues[$pStrFieldName];
     }
     else
     {
       return $this->arrDbFieldsValues[$pStrFieldName];
     }
   }

   /**
    * Load Object from his UID
    *
    * @param string $pStrUID  Unique Identifier of Object
    */
   public function loadObjectFromDBbyUID($pStrUID)
   {
     try {
       // SQL Query definition!
       $lStrCodeObject = $pStrCode;
       $lStrSQLQuery .= "SELECT ";
       $lStrSQLQuery .= implode(', ',$this->arrFields);
       $lStrSQLQuery .= " FROM ".$this->strTablename;
       $lStrSQLQuery .= " WHERE id = :id";

       // SQL Query Execution!
       $lObjSth = $this->objDBPdo->prepare($lStrSQLQuery,array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
       $lObjSth->execute(array('id' => $pStrUID));

       // 1 row returned ?
       if($lObjSth->rowcount() == 1)
       {
         $lIntIDXField = 1;
         $this->resetFieldsValueInternalArrays();

         foreach($this->arrFields as $lStrFieldname)
         {
           $lObjSth->bindcolumn($lIntIDXField,$this->arrDbFieldsValues[$lStrFieldname]);
           $lIntIDXField++;
         }

         // Fetch SQL result to object!
         $lArrResutObject = $lObjSth->fetch(\PDO::FETCH_BOUND);
       }
       else {
         throw new \Exception(sprintf("Invalid rows count (%d) returned by query '%s'.",$lObjSth->rowcount(),$lStrSQLQuery));
       }

       return $lArrResutObject;
     }
     catch(\Exception $e)
     {
       $lArrExParam = array($pStrCode,$e->getMessage());
       throw new ApplicationException('DATABASE-LOAD-OBJECT',$lArrExParam);
     }
   }//end loadObjectFromDBbyUID()


   /**
    * Load Object from his UID
    *
    * @param string $pStrUID  Unique Identifier of Object
    */
   public function loadAllObjectFromDB()
   {
     try {
       // SQL Query definition!
       $lStrSQLQuery .= "SELECT ";
       $lStrSQLQuery .= implode(', ',$this->arrFields);
       $lStrSQLQuery .= " FROM ".$this->strTablename;

       //print_r($lStrSQLQuery);

       // SQL Query Execution!
       $lObjSth = $this->objDBPdo->prepare($lStrSQLQuery,array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
       $lObjSth->execute();

       // 1 row returned ?
       if($lObjSth->rowcount() > 0)
       {
         $lArrResutObject = $lObjSth->fetchAll(\PDO::FETCH_ASSOC);
       }
       else {
         throw new \Exception(sprintf("Invalid rows count (%d) returned by query '%s'.",$lObjSth->rowcount(),$lStrSQLQuery));
       }

       return $lArrResutObject;
     }
     catch(\Exception $e)
     {
       $lArrExParam = array($pStrCode,$e->getMessage());
       throw new ApplicationException('DATABASE-LOAD-OBJECT',$lArrExParam);
     }
   }//end loadObjectFromDBbyUID()


   /**
    * Load Object from criteria
    *
    * @param array $pArrCriteria  Criterias
    */
   public function loadObjectFromDBbyCriteria($pArrCriteria)
   {
     try {
       // SQL Query definition!
       $lArrSQLWhereConditions =array();

       foreach($pArrCriteria as $lStrFieldName => $lStrFieldValue)
       {
         if(!is_null($lStrFieldValue))
         {
           if(is_numeric($lStrFieldValue)) {
             array_push($lArrSQLWhereConditions,"$lStrFieldName=".$lStrFieldValue);
           }
           else {
             array_push($lArrSQLWhereConditions,"$lStrFieldName='$lStrFieldValue'");
           }
         }
       }

       $lStrSQLQuery = sprintf(
          "SELECT %s FROM %s WHERE %s",
          implode(', ',$this->arrFields),
          $this->strTablename,
          implode(' AND ',$lArrSQLWhereConditions)
       );

       // SQL Query Execution!
       $lObjSth = $this->objDBPdo->prepare($lStrSQLQuery,array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
       $lObjSth->execute();

       // 1 row returned ?
       if($lObjSth->rowcount() == 1)
       {
         $lIntIDXField = 1;
         $this->resetFieldsValueInternalArrays();

         foreach($this->arrFields as $lStrFieldname)
         {
           $lObjSth->bindcolumn($lIntIDXField,$this->arrDbFieldsValues[$lStrFieldname]);
           $lIntIDXField++;
         }

         // Fetch SQL result to object!
         $lArrResutObject = $lObjSth->fetch(\PDO::FETCH_BOUND);
       }
       else {
         throw new \Exception(sprintf("Invalid rows count (%d) returned by query '%s'.",$lObjSth->rowcount(),$lStrSQLQuery));
       }

       return $lArrResutObject;
     }
     catch(\Exception $e)
     {
       $lArrExParam = array($pStrCode,$e->getMessage());
       throw new ApplicationException('DATABASE-LOAD-OBJECT',$lArrExParam);
     }
   }//end loadObjectFromDBbyCriteria()

   /**
    * Load last version and iteration of object
    *
    * @param string $pStrCode   UID of Object to load
    */
   public function loadObjectFromCode($pStrCode)
   {
     $lStrSQLQuery = "";
     try{

       // SQL Query definition!
       $lStrCodeObject = $pStrCode;
       $lStrSQLQuery .= "SELECT ";
       $lStrSQLQuery .= implode(', ',$this->arrFields);
       $lStrSQLQuery .= " FROM ".$this->strTablename;
       $lStrSQLQuery .= " WHERE code = :code";

       // BusinessObjectVersioned => Last Version / Iteration !
       if($this instanceof BusinessObjectVersioned)
       {
           $lStrSQLQuery .= " GROUP BY ".implode(', ',$this->arrFields);
           $lStrSQLQuery .= " HAVING ".BusinessObjectVersioned::$strVersionDefaultFieldname."=MAX(".BusinessObjectVersioned::$strVersionDefaultFieldname.") AND ".BusinessObjectVersioned::$strIterationDefaultFieldname."=MAX(".BusinessObjectVersioned::$strIterationDefaultFieldname.")";
       }

       // SQL Query Execution!
       $lObjSth = $this->objDBPdo->prepare($lStrSQLQuery,array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
       $lObjSth->execute(array('code' => $pStrCode));

       // 1 row returned ?
       if($lObjSth->rowcount() == 1)
       {
         $lIntIDXField = 1;
         $this->resetFieldsValueInternalArrays();

         foreach($this->arrFields as $lStrFieldname)
         {
           $lObjSth->bindcolumn($lIntIDXField,$this->arrDbFieldsValues[$lStrFieldname]);
           $lIntIDXField++;
         }

         // Fetch SQL result to object!
         $lArrResutObject = $lObjSth->fetch(\PDO::FETCH_BOUND);
      }
       else {
         throw new \Exception(sprintf("Invalid rows count (%d) returned by query '%s'.",$lObjSth->rowcount(),$lStrSQLQuery));
       }

       return $lArrResutObject;
     }
     catch(\Exception $e)
     {
       $lArrExParam = array($pStrCode,$e->getMessage());
       throw new AppException('DATABASE-LOAD-OBJECT',$lArrExParam);
     }
   }//end loadObjectFromCode()



   public function deleteObjectFromDB()
   {

   }

   /**
    * Store current Object Into DB.
    *
    * Auto determinate if needs an INSERT Or an UPDATE.
    * @throws \Exception If error during execution of query
    */
   public function saveObjectIntoDB($pBoolReload=true)
   {
     // SQL Query to execute!
     $lStrSQLQuery    = null;
     $lStrCodeObject  = null;
     $lStrIdObject    = null;

     try {

       if($this->isObjectInsertable()){
         $lStrSQLQuery = $this->generateInsertOrderFromObject();
         $lStrCodeObject = $this->arrNewFieldsValues['code'];
       }
       elseif ($this->isObjectUpdatable()) {
         $lStrSQLQuery = $this->generateUpdateOrderFromObject();
         $lStrIdObject = $this->arrDbFieldsValues['id'];
       }
       else {
         throw new AppException('DATABASE-STORE-OBJECT',array('Impossible to choose a query mode (i.e. UPDATE or INSERT)'));
       }

       // SQL Query Execution!
       $lIntCount = $this->objDBPdo->exec($lStrSQLQuery);

       // 1 row returned ?
       if($lIntCount == 1)
       {
         if($pBoolReload)
         {
         if(is_null($lStrIdObject) && !is_null($lStrCodeObject)){
           $this->loadObjectFromCode($lStrCodeObject);
         }
         else {
           $this->loadObjectFromDBbyUID($lStrIdObject);
         }
       }
       }
       else {
         throw new \Exception(
           sprintf(
             "Invalid rows count (%d) returned by query '%s' (PDOMsg:'Code %s => %s').",
             $lIntCount,
             $lStrSQLQuery,
             $this->objDBPdo->errorCode(),
             $this->objDBPdo->errorInfo()[2]
           )
         );
       }
       return $lIntCount;
     }
     catch(AppException $e)
     {
       throw $e;
     }
     catch(\Exception $e)
     {
       $lArrExParam = array($e->getMessage());
       throw new AppException('DATABASE-STORE-OBJECT',$lArrExParam);
     }
   }

   /**
    * Generate Insert SQL Order from current Object
    *
    * @return string  Insert SQL Order
    */
   private function generateInsertOrderFromObject()
   {
     $lArrFields = array();
     $lArrValues = array();

     foreach($this->arrNewFieldsValues as $lStrFieldname => $lStrFieldValue)
     {
       if(!is_null($lStrFieldValue))
       {
         array_push($lArrFields,$lStrFieldname);
         if(is_numeric($lStrFieldValue)) {
           array_push($lArrValues,$lStrFieldValue);
         }
         else {
           array_push($lArrValues,"'$lStrFieldValue'");
         }
       }
     }

     $lStrSQL = sprintf(
       "INSERT INTO %s(%s) VALUES (%s)",
       $this->strTablename,
       implode(', ',$lArrFields),
       implode(', ',$lArrValues)
     );

     return $lStrSQL;
   }//end generateInsertOrderFromObject()

   /**
    * Generate Update SQL Order from current Object
    *
    * @return string  Update SQL Order
    */
   private function generateUpdateOrderFromObject()
   {
      $lArrSQLSetOrders = array();

      foreach($this->arrNewFieldsValues as $lStrFieldname => $lStrFieldValue)
      {
        if(!is_null($lStrFieldValue))
        {
          if(is_numeric($lStrFieldValue)) {
            array_push($lArrSQLSetOrders,"$lStrFieldname=".$lStrFieldValue);
          }
          else {
            array_push($lArrSQLSetOrders,"$lStrFieldname='$lStrFieldValue'");
          }
        }
      }

      $lStrSQL = sprintf(
        "UPDATE %s SET %s WHERE id='%s'",
        $this->strTablename,
        implode(', ',$lArrSQLSetOrders),
        $this->arrDbFieldsValues['id']
      );

      return $lStrSQL;
   }//end generateUpdateOrderFromObject()


   /**
    * Returns TRUE if Object is valid for an Insertion into Database.
    *
    * @internal check if object wasn't load and if at least one value is defined ...
    */
   private function isObjectInsertable()
   {
    return  (!$this->_isObjectLoadedFromDB() && $this->_isObjectHaveNewFieldValue());
   }//end isObjectInsertable()

   /**
    * Returns TRUE if Object is valid for an Update into Database.
    *
    * @internal check if object was load and if at least one value is defined ...
    */
   private function isObjectUpdatable()
   {
     return ($this->_isObjectLoadedFromDB() && $this->_isObjectHaveNewFieldValue());
   }//end isObjectUpdatable()

   /**
    * Returns TRUE if Object is loaded from DB
    */
   private function _isObjectLoadedFromDB()
   {
     $lBoolObjectLoadedFromDB = false;
     // At least if a field value is defined into internal array DB fields value => means UPDATE Mode!
     foreach($this->arrDbFieldsValues as $lStrKey => $lStrDBValue)
     {
        if(!is_null($lStrDBValue))
        {
          $lBoolObjectLoadedFromDB = true;
        }
     }
     return $lBoolObjectLoadedFromDB;
   }//end _isObjectLoadedFromDB()

   /**
    * Returns TRUE if Object is loaded from DB
    */
   private function _isObjectHaveNewFieldValue()
   {
     $lBoolObjectHaveNewFieldValue = false;
     // Object well-defined => At least one field value must be defined (!= null) !
     foreach($this->arrNewFieldsValues as $lStrKey => $lStrNewValue)
     {
        if(!is_null($lStrNewValue))
        {
          $lBoolObjectHaveNewFieldValue = true;
        }
     }

     return $lBoolObjectHaveNewFieldValue;
   }//end _isObjectLoadedFromDB()


   /**
    * Return TRUE is current object is well defined for SQL querying
    * @return bool
    */
   public function isObjectValidForSQL()
   {
     return boolval(count($this->arrFields) > 0 && !empty($this->strTablename) && count($this->arrPrimaryKeyFields)>0 );
   }

   /**
    * Execute a SQL Query
    *
    * @param string $pStrSQLQuery   SQL Query to execute
    *
    * @return int   Rows impacted
    */
   protected function execSQL($pStrSQLQuery)
   {
     // SQL Query to execute!
     try {
       // SQL Query Execution!
       $lIntCount = $this->objDBPdo->exec($pStrSQLQuery);

       return $lIntCount;

     }
     catch(AppException $e)
     {
       throw $e;
     }
     catch(\Exception $e)
     {
       $lArrExParam = array($e->getMessage());
       throw new AppException('DATABASE-STORE-OBJECT',$lArrExParam);
     }
   }//end execSQL()

   /* Return all Objects of current class */
   public function getAllObj()
   {
     return $this->loadAllObjectFromDB();
   }


 }//end class
 ?>
