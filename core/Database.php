<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $masterConnection = null;
    private static ?PDO $tenantConnection = null;

    public static function master(): PDO
    {
        if (self::$masterConnection === null) {
            $config = require __DIR__ . '/../config/database.php';
            $db = $config['master'];
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset={$db['charset']}";
            try {
                self::$masterConnection = new PDO($dsn, $db['username'], $db['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new PDOException("Error connecting to master database: " . $e->getMessage());
            }
        }
        return self::$masterConnection;
    }

    public static function tenant(): PDO
    {
        if (self::$tenantConnection === null) {
            throw new \RuntimeException("Tenant database not initialized. Call setTenant() first.");
        }
        return self::$tenantConnection;
    }

    public static function setTenant(string $host, string $dbName, string $user, string $pass): void
    {
        $config = require __DIR__ . '/../config/database.php';
        $masterDb = $config['master']['database'];

        if ($dbName === $masterDb) {
            self::$tenantConnection = self::master();
            return;
        }

        $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
        try {
            self::$tenantConnection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Error connecting to tenant database: " . $e->getMessage());
        }
    }

    public static function hasTenant(): bool
    {
        return self::$tenantConnection !== null;
    }
}
