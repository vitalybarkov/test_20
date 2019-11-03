<?php
require_once 'Controller.php';
require_once './m/Apple.php';

class EditApple extends Controller {
    protected function request () {
        $request = $_POST;
        if ($request['id'] > 0 && $request['drop'] > 0 || $request['eat']) {
            $params = array (
            'id'     => 0,
            'drop'   => false,
            'eat'    => 0.0
            );

            // check the arguments
            if (strpos($request['eat'], ',') !== false) {
                $request['eat'] = str_replace(",", ".", $request['eat']);
            }
            // var_dump($request['eat']);
            // exit;
            if ($request['id'] > 0 && preg_match('/^[0-9]+$/', $request['id'])) {
                $params['id'] = (int)$request['id'];
            }
            if ($request['drop'] > 0 && preg_match('/^[01]+$/', $request['drop'])) {
                $params['drop'] = (boolean)$request['drop'];
            }
            if ($request['eat'] > 0 && preg_match('/[+-]?\d+(\.\d+)?([Ee][+-]?\d+)?/', $request['eat'])) {
                $params['eat'] = (float)$request['eat'];
            }

            // get the data
            $db = new DB();
            if ($existedApple = $db->getExistedApple($params['id'])) {
                $apple = new Apple($params['id'], $existedApple['Color'], $existedApple['CreationDate'], $existedApple['DropDate'], $existedApple['Dropped'], $params['drop'], $existedApple['EatedPercentage'], $params['eat']);
                $db->editApple($apple);
            }

            // prepare the json response
            $response = array (
                'status'    => $db->status,
                'payload'   => $db->data
            );
            if ($db->message) {
                $response = array_merge($response, array (
                    'message'   => $db->message
                ));
            }

            return $response;
        } else {
            $response = array (
                'status'    => 'error',
                'message'   => 'Nothing to change',
            );
        
            return json_encode($response);
        }
    }
}

new EditApple();