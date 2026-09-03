<?php
/**
 * Description of Logs
 *
 * @author Guilherme
 */
class Logs {
    private $action;
    private $id_usuario;
    
    function __construct($action) {
        $mc = new Maincontroller();
        
        try {
            
            if (isset($_SESSION['usuarioObj'])): $user = $_SESSION['usuarioObj']; endif;
            $idUser = $user->getId_cargo();
        
            $this->action = $action;
            $this->id_usuario = $idUser;

            $sql = "INSERT INTO logs (action,id_usuario) VALUES (:ACTION,:USER)";
            $mc->doQuery($sql,array('ACTION'=> $this->action,'USER'=> $this->id_usuario));
            
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
        
    }
    
}
