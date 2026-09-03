<?php

/**
 *
 * @author Guilherme
 */
interface IDao {
    public function get($id);
    public function getall();
    public function save();    
    public function delete($id);
}
