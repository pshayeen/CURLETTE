<?php 

function getConnection(): PDO 
{
    $host = 'localhost';
    $db   = 'curlette_db';
    $user = 'root';
    $pass = 'admin9173';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", 
        $user, 
        $pass
    );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
