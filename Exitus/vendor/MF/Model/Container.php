<?php

namespace MF\Model;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Container
{
    public static function getModel($model)
    {
        // Oculta mensagens de funções obsoletas
        error_reporting(E_ALL & ~E_DEPRECATED);

        // Caminho até a raiz do projeto (onde está o .env)
        $dotenvPath = realpath(__DIR__ . '/../../../');

        if ($dotenvPath && file_exists($dotenvPath . '/.env')) {
            $dotenv = Dotenv::createImmutable($dotenvPath);
            $dotenv->safeLoad(); // <- mais seguro que load() pois não falha se .env incompleto
        } else {
            die("Arquivo .env não encontrado em: " . $dotenvPath);
        }

        // Variáveis de conexão
        $host     = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port     = $_ENV['DB_PORT'] ?? '3306';
        $db_name  = $_ENV['DB_DATABASE'] ?? 'exitus_db';
        $username = $_ENV['DB_USERNAME'] ?? 'root';

        // Aqui garantimos que mesmo string vazia será passada corretamente
        $password = array_key_exists('DB_PASSWORD', $_ENV) ? $_ENV['DB_PASSWORD'] : '';

        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$db_name};charset=utf8mb4";
            $db = new PDO($dsn, $username, $password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $modelClass = "\\App\\Models\\" . $model;
            return new $modelClass($db);
        } catch (PDOException $e) {
            echo 'Erro de conexão: ' . $e->getMessage();
            return null;
        }
    }
}
