<?php
require_once 'Controller.php';
require_once './m/Tree.php';

class GrowApples extends Controller {
    protected function request () {
        $request = $_GET;
        $quantity = 0;

        // check the arguments
        if ($request['quantity'] > 0 && preg_match('/^[0-9]+$/', $request['quantity'])) {
            $quantity = (int)$request['quantity'];
        }

        // ini_set('display_errors', 1);

        // get the data
        $tree = new Tree($quantity);
        DB::growApples($tree);

        // prepare the json response
        $response = array (
            'status'    => 'ok'
        );
        if ($tree) {
            $response = array_merge($response, array (
                'payload'   => $tree->apples
            ));
        }

        return $response;
    }
}

new GrowApples();