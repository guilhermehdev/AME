<?php

/**
 * Classe para conexão com o banco
 *
 * @author Guilherme
 */
class Conn {
   private static $cnx;
   
   private static function open(){
       $host = HOST;
       $port = PORT;
       $dbName = DB_NAME;
       $userName = USER_NAME;
       $pass = PASSWORD;
       
       try {
            self::$cnx = new PDO("mysql:host={$host}; port={$port}; dbname={$dbName}; ",$userName,$pass,
            array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
            self::$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
       } catch (Exception $exc) {
           //echo "Falha na conexão! Verifique os dados de acesso ao banco...";
           echo $exc->getMessage();
       }

   }
   
   public static function getConn(){
       if(!self::$cnx){
           self::open();
       }
       return self::$cnx;
   }   
   
}
