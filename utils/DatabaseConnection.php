<?php
//
//namespace Utils;
//
//use PDO;
//
//class DatabaseConnection
//{
//
//    private static $pdo;
//
//    private static function connect()
//    {
//        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";unix_socket=/tmp/mysql.sock";
//
//
//
//        try {
//            self::$pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
//            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//            self::$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//            self::$pdo->setAttribute( PDO::ATTR_EMULATE_PREPARES, true);
//            return self::$pdo;
//        } catch (PDOException $e) {
//            return false;
//        }
//    }
//
//    public static function rawQuery($sql)
//    {
//        $connection = self::connect();
//
//        try {
//            $statement = $connection->prepare($sql);
//            $statement->execute();
//            return $statement->fetchAll(PDO::FETCH_ASSOC);
//        } catch (PDOException $e) {
//            echo "Query execution failed: " . $e->getMessage();
//        }
//    }
//
//
//}


namespace Utils;

use \PDO;


class DatabaseConnection
{
    private static $connection;
    public static $table = null;
    public static $tableid = 'id';


    public static function find($id, $status = 'active')
    {
        self::initConnection();
        $stmt = self::$connection->prepare("SELECT * from " . static::$table . " where " . static::$tableid . " = :id and status = :status");
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->bindParam('status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public static function raw($query, ...$parameters)
    {
        self::initConnection();
        $stmt = self::$connection->prepare($query);
        $result = $stmt->execute($parameters);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($query, ...$parameters) {
        self::initConnection();

        try {
            $stmt = self::$connection->prepare($query);

            $i = 1;
            foreach ($parameters as $value) {
                $stmt->bindValue($i++, $value);
            }

            $result = $stmt->execute();

            return $result;
        } catch (\Exception $e) {
            // echo "Error creating record: " . $e->getMessage();
            return false;
        }
    }


    public static function fetchOne($query, ...$parameters)
    {
        try {   
            self::initConnection();
            $stmt = self::$connection->prepare($query);

            foreach ($parameters as $key => $value) {
                $stmt->bindParam($key + 1, $parameters[$key]);
            }

            $result = $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row !== false ? $row : null;
        } catch (\Exception $e) {
            echo "HORRORI: " . $e->getMessage();
            return false;
        }
    }

    
    public static function fetchMany($query, ...$parameters)
    {
        self::initConnection();
        $stmt = self::$connection->prepare($query);

        // Bind parameters to the statement
        foreach ($parameters as $key => $value) {
            $stmt->bindParam($key + 1, $parameters[$key]);
        }

        // Execute the statement
        $result = $stmt->execute();

        // Fetch all rows
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }


    public static function initConnection()
    {
        if (empty(self::$connection)) {
            try {
                self::$connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=' . DB_PORT, DB_USERNAME, DB_PASSWORD);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            } catch (\PDOException $e) {
                print "Error: " . $e->getMessage() . "<br/>";
                die();
            }
        }
        return self::$connection;

    }

}
