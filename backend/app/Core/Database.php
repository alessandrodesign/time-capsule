<?php

namespace App\Core;

use Exception;
use PDO;
use PDOException;
use RuntimeException;

// Importar PDOException para tratamento de erros

/**
 * Class Database
 *
 * Implementa o padrão Singleton para gerenciar a conexão PDO com o banco de dados.
 */
final class Database
{
    /**
     * @var PDO|null A única instância da conexão PDO.
     */
    private static ?PDO $instance = null;

    /**
     * Construtor privado para impedir a instanciação externa.
     */
    private function __construct()
    {
        // Construtor vazio ou com lógica de inicialização adicional se necessário.
    }

    /**
     * Impede a clonagem da instância.
     */
    private function __clone()
    {
        throw new RuntimeException('Cloning is not allowed.');
    }

    /**
     * Impede a desserialização da instância.
     *
     * @throws Exception Se tentar desserializar a instância.
     */
    public function __wakeup(): void
    {
        throw new RuntimeException('Unserialization is not allowed.');
    }

    /**
     * Obtém a única instância da conexão PDO.
     * Se a instância ainda não existir, ela será criada.
     *
     * @return PDO A instância da conexão PDO.
     * @throws PDOException Se houver um erro ao conectar ao banco de dados.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = 'localhost';
            $db = 'capsula_db';
            $user = 'usuario'; // Lembre-se de usar variáveis de ambiente ou um arquivo de configuração para dados sensíveis
            $pass = 'senha';   // Lembre-se de usar variáveis de ambiente ou um arquivo de configuração para dados sensíveis
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Lança PDOExceptions para erros
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Define o modo de busca padrão como array associativo
                    PDO::ATTR_EMULATE_PREPARES => false,                // Desabilita a emulação de prepared statements para maior segurança
                ]);
            } catch (PDOException $e) {
                // Em um ambiente de produção, você deve conectar o erro e não exibi-lo diretamente
                error_log("Database connection error: " . $e->getMessage());
                // Ou, se for uma aplicação web, você pode redirecionar para uma página de erro genérica
                // header('Location: /error_page.php');
                // exit;
                throw new PDOException("Could not connect to the database. Please try again later.", (int)$e->getCode(), $e);
            }
        }
        return self::$instance;
    }
}