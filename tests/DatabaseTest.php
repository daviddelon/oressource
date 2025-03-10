<?php 

use PHPUnit\Framework\TestCase;
use PDO as PDO;
use Dotenv\Dotenv;

class DatabaseTest extends TestCase {
    private $pdo;

    protected function setUp(): void {
     
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../', '.env.test');
        $dotenv->load();


        $this->pdo = new PDO(
            "mysql:host=" . $_ENV['DB_HOST'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Créer la base de données si elle n'existe pas
        $this->pdo->exec("CREATE DATABASE IF NOT EXISTS " . $_ENV['DB_NAME']);
        $this->pdo->exec("USE " . $_ENV['DB_NAME']);

        // Charger le schéma SQL
        

        $sql = file_get_contents(__DIR__ . '/../mysql/oressource.sql');
        $this->pdo->exec($sql);

    }

    public function test_DatabaseEstInitialize() {

        $stmt = $this->pdo->query("SHOW TABLES LIKE 'utilisateurs'");
        $tableExists = $stmt->rowCount() > 0;
        $this->assertTrue($tableExists, "La table 'utilisateurs' n'existe pas.");
    }

}
