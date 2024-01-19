<?php

namespace Database;

use PDO;


class DB
{
    private static $connection;
    public static $table = null;
    public static $tableid = 'id';

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

            $stmt->execute();

            return self::$connection->lastInsertId();
        } catch (\Exception $e) {
             echo "Error creating record: " . $e->getMessage();
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
