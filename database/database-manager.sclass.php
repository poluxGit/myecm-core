<?php

namespace Core\Database;


class DatabaseManager {

  private static _oPdoDBHanlder = null;

  public static function setPDODBHandler(\PDOStatement $pObjPDODBHandler)
  {
    static::$_oPdoDBHanlder = $pObjPDODBHandler;
  }

  public static function exec($pStrSQLQuery)
  {
    static::$_oPdoDBHanlder = $pObjPDODBHandler;
  }

  public static function query($pStrSQLQuery)
  {
    static::$_oPdoDBHanlder = $pObjPDODBHandler;
  }

}








 ?>
