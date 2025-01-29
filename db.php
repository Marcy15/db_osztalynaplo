<?php
include_once  "config.php";

function execSql($sql): bool|int|array
{
    $mysqli = null;
 
    try {
        $mysqli = getConn();
 
        // Check if connection was established
        if (!$mysqli) {
            return false;
        }
 
        $result = $mysqli->query($sql);
 
        // Check if the query execution was successful
        if (!$result) {
            throw new Exception('Hiba lépett fel az SQL utasítás futtatása közben: ' . $mysqli->error);
        }
 
        // Handle INSERT queries
        if (str_starts_with(strtoupper(trim($sql)), 'INSERT')) {
            return $mysqli->insert_id > 0 ? $mysqli->insert_id : false;
        }
 
        // Handle SELECT queries
        if (str_starts_with(strtoupper(trim($sql)), 'SELECT')) {
            $numRows = $result->num_rows;
 
            if ($numRows === 0) {
                return false;
            }
 
            if ($numRows === 1) {
                return $result->fetch_assoc();
            }
 
            return $result->fetch_all(MYSQLI_ASSOC);
        }
 
        // For other types of queries (e.g., UPDATE, DELETE)
        return $mysqli->affected_rows > 0;
 
    } catch (Exception $e) {
        // Handle exceptions by logging and displaying messages
        //displayMessage($e->getMessage(), 'error');
        error_log($e->getMessage());
        return false;
    } finally {
        // Ensure the database connection is always closed
        $mysqli?->close();
    }
}

function getConn($dbName = DB_NAME): ?mysqli
{
    try {
        // Kapcsolódás az adatbázishoz
        $mysqli = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, $dbName);
 
        // Ellenőrizzük a csatlakozás sikerességét
        if (!$mysqli) {
            throw new Exception('Kapcsolódási hiba az adatbázishoz: ' . mysqli_connect_error());
        }
 
        return $mysqli;
 
    } catch (Exception $e) {
        // Hibaüzenet megjelenítése a felhasználónak
        //displayMessage($e->getMessage(), 'error');
 
        // Hibanaplózás
        error_log($e->getMessage());
 
        // Hibás csatlakozás esetén `null`-t ad vissza
        return null;
    }
}

function dbExists($dbName = DB_NAME): bool
{
    try {
        $mysqli = getConn('mysql');
        if (!$mysqli) {
            return false;
        }
 
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName';";
        $result = $mysqli->query($query);
 
        if (!$result) {
            throw new Exception('Lekérdezési hiba: ' . $mysqli->error);
        }
        $exists = $result->num_rows > 0;
 
        return $exists;
 
    } catch (Exception $e) {
        //displayMessage($e->getMessage(), 'error');
        error_log($e->getMessage());
 
        return false;
    } finally {
        // Ensure the database connection is always closed
        $mysqli?->close();
    }
 
}
function createDbs($dbName): bool
{
    try {
        if(dbExists($dbName)) return false;
        // Kapcsolódás a MySQL szerverhez (alap adatbázishoz)
        $mysqli = getConn('mysql');
        
        if (!$mysqli) {
            return false; // Ha nem sikerül kapcsolódni, visszatérünk false-szal
        }

        // Az SQL parancs az új adatbázis létrehozására
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbName`";
        
        // A lekérdezés végrehajtása
        if ($mysqli->query($sql)) {
            return true; // Ha sikeres a létrehozás, true-t adunk vissza
        } else {
            throw new Exception('Hiba történt az adatbázis létrehozása közben: ' . $mysqli->error);
        }
    } catch (Exception $e) {
        // Hiba esetén hibaüzenet megjelenítése és naplózása
        //displayMessage($e->getMessage(), 'error');
        error_log($e->getMessage());
        return false;
    } finally {
        // Kapcsolat bezárása, ha az létrejött
        $mysqli?->close();
    }
}
