<?php
class DB {
    static $servername = 'localhost';
    static $username = 'root';
    static $password = 'root';
    static $dbname = 'test_apples';
    
    static $conn = null;
    static $status = '';
    static $data = null;
    static $message = '';

    static function connect () {
        // Create connection
        static::$conn = new mysqli(static::$servername, static::$username, static::$password, static::$dbname);
        // Check connection
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }
    }

    static function growApples ($tree = null) {
        static::connect();

        // grow new apples on the tree
        $stmt = static::$conn->prepare("DROP TABLE IF EXISTS apples");
        $stmt->execute();

        $stmt = static::$conn->prepare("
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
        $stmt = static::$conn->prepare("INSERT INTO `apples` (`Color`) VALUES (?)");
        $stmt->bind_param("s", $color);
        foreach ($tree->apples as $key => $value) {
            $color = $value->color;
            $stmt->execute();
        }
        $stmt->close();

        static::$status = 'ok';
        static::$message = 'the apples was added on the tree';

        static::disconnect();
    }

    static function getExistedApple ($id = 0) {
        static::connect();

        // get exited apple by id
        $stmt = static::$conn->prepare("SELECT ID, Color, UNIX_TIMESTAMP(CreationDate), UNIX_TIMESTAMP(DropDate), Dropped, EatedPercentage FROM apples WHERE id = " . $id);

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
            static::$status = 'error';
            static::$message = 'not found an apple by the id ' . $id;
        }
        
        static::disconnect();

        return $response;
    }

    static function editApple ($apple = null) {
        static::connect();

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
                $stmt = static::$conn->prepare("
                UPDATE apples 
                SET 
                    " . $addToSql . "
                WHERE
                    id = " . $apple->id . ";
                "); 
                $stmt->execute();
                $stmt->close();

                static::$status = 'ok';
                static::$data = $apple;
                static::$message = 'the apple ' . $apple->id . ' was updated';
            }
        } else {
            // delete the apple
            $stmt = static::$conn->prepare("DELETE FROM apples WHERE id = " . $apple->id); 
            $stmt->execute();
            $stmt->close();

            static::$status = 'ok';
            static::$data = $apple;
            static::$message = 'the apple ' . $apple->id . ' was deleted';
        }

        static::disconnect();
    }

    static function disconnect () {
        // Close connection
        static::$conn->close();
    }
}