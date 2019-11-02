<?php
class DB {
    private $servername = 'localhost';
    private $username = 'root';
    private $password = 'root';
    private $dbname = 'test_apples';
    
    private $conn = null;
    public $status = '';
    public $data = null;
    public $message = '';

    public function __construct () {
        // Create connection
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        // Check connection
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }
    }

    public function growApples ($tree = null) {
        // // create table if not exists
        // $stmt = $this->conn->prepare("SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";");
        // // $stmt->execute();
        // $stmt = $this->conn->prepare("SET time_zone = \"+00:00\";");
        // // $stmt->execute();
        // $stmt = $this->conn->prepare("CREATE DATABASE IF NOT EXISTS `" . $this->dbname . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
        // // $stmt->execute();
        // $stmt = $this->conn->prepare("USE `" . $this->dbname . "`;");
        // $stmt->execute();

        // grow new apples on the tree
        $stmt = $this->conn->prepare("DROP TABLE IF EXISTS apples");
        $stmt->execute();

        $stmt = $this->conn->prepare("
            CREATE TABLE IF NOT EXISTS `apples` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `Color` varchar(255) NOT NULL,
            `CreationDate` timestamp DEFAULT CURRENT_TIMESTAMP,
            `DropDate` timestamp NULL DEFAULT NULL,
            `Dropped` boolean,
            `EatedPercentage` float(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
        ");
        $stmt->execute();

        // add apples on the tree
        $stmt = $this->conn->prepare("INSERT INTO `apples` (`Color`) VALUES (?)");
        $stmt->bind_param("s", $color);
        foreach ($tree->apples as $key => $value) {
            $color = $value->color;
            $stmt->execute();
        }
        $stmt->close();

        $this->status = 'ok';
        $this->message = 'the apples was added on the tree';
    }

    public function getExistedApple ($id = 0) {
        // get exited apple by id
        $stmt = $this->conn->prepare("SELECT ID, Color, UNIX_TIMESTAMP(CreationDate), UNIX_TIMESTAMP(DropDate), Dropped, EatedPercentage FROM apples WHERE id = " . $id);

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        $response = false;
        if ($r = $result->fetch_array(MYSQLI_NUM)) {
            $response = array (
                'ID'                => $r[0],
                'Color'             => $r[1],
                'CreationDate'      => $r[2],
                'DropDate'          => $r[3],
                'Dropped'           => $r[4],
                'EatedPercentage'   => $r[5]
            );
        } else {
            $this->status = 'error';
            $this->message = 'not found an apple by the id ' . $id;
        }

        return $response;
    }

    public function editApple ($apple = null) {
        if (!$apple->deleted) {
            $addToSql = '';
            if ($apple->dropped) {
                $addToSql .= "DropDate = FROM_UNIXTIME(" . $apple->dropDate . ")";
                $addToSql .= ",Dropped = '" . $apple->dropped . "'";
            }
            // var_dump($apple);
            // exit;
            if ($apple->eatedPercentage) {
                $addToSql .= ",EatedPercentage = '" . $apple->eatedPercentage . "'";
            }
            if ($addToSql) {
                // update the apple
                $stmt = $this->conn->prepare("
                UPDATE apples 
                SET 
                    " . $addToSql . "
                WHERE
                    id = " . $apple->id . ";
                "); 
                $stmt->execute();
                $stmt->close();

                $this->status = 'ok';
                $this->data = $apple;
                $this->message = 'the apple ' . $apple->id . ' was updated';
            }
        } else {
            // delete the apple
            $stmt = $this->conn->prepare("DELETE FROM apples WHERE id = " . $apple->id); 
            $stmt->execute();
            $stmt->close();

            $this->status = 'ok';
            $this->data = $apple;
            $this->message = 'the apple ' . $apple->id . ' was deleted';
        }
    }

    public function __destruct () {
        // Close connection
        $this->conn->close();
    }
}