<?php

namespace Core;

use PDO;
use PDOException;

class Database {
    private static $pdo;

    public static function connect() {
        if (!self::$pdo) {
            $env = parse_ini_file(dirname(__DIR__, 2) . '/db-credentials.env');
            try {
                // Tenta se conectar ao banco de dados PostgreSQL
                self::$pdo = new PDO(
                    dsn: "pgsql:host={$env['PGHOST']};dbname={$env['PGDATABASE']}",
                    username: $env['PGUSER'],
                    password: $env['PGPASSWORD']
                );
                self::$pdo->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // Verifica se o erro é relacionado ao driver PDO
                if (strpos($e->getMessage(), 'could not find driver') !== false) {
                    die("Erro: O driver PDO do PostgreSQL não está instalado ou habilitado. " .
                        "Por favor, verifique se a extensão 'pdo_pgsql' está habilitada no seu php.ini. " .
                        "Se a extensão estiver habilitada e o problema persistir, " .
                        "verifique se o PHP está corretamente instalado e configurado, " .
                        "e se a arquitetura do PHP e PostgreSQL são compatíveis.");
                } else {
                    die("Erro na conexão com o banco de dados: " . $e->getMessage());
                }
                
            }
        }

        return self::$pdo;
    }
}
