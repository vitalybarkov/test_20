<?php
require_once 'DB.php';

abstract class Controller {
    function __construct () {
        echo $this->getResponse();
    }

    protected function getResponse () {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST') {
            $response = $this->request();

            return json_encode($response);
        } else {
            $response = array (
                'status'    => 'error',
                'message'   => 'Available GET or POST requests only',
            );
        
            return json_encode($response);
        }
    }
}