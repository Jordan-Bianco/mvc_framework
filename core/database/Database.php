<?php

namespace App\core\database;

class Database
{
    public \PDO $pdo;
    public array $newMigrations = [];

    public function __construct(array $config)
    {
        try {
            $this->pdo = new \PDO($config['dsn'], $config['user'], $config['password']);

            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die(var_dump($e->getMessage()));
        }
    }

    /**
     * @return void 
     */
    public function applyMigrations(): void
    {
        $this->createMigrationsTable();

        $appliedMigrations = $this->getAppliedMigrations();

        $files = $this->getMigrationFilesName();

        // The migrations to be applied are given by the difference between those already applied and the files in the migrations folder
        $toApplyMigrations = array_diff($files, $appliedMigrations);

        foreach ($toApplyMigrations as $migration) {

            require_once ROOT_PATH . "/migrations/$migration.php";

            $instance = new $migration();

            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Applied migration $migration");

            array_push($this->newMigrations, $migration);
        }

        if (!empty($this->newMigrations)) {
            $this->saveMigrations($this->newMigrations);
        } else {
            $this->log('Nothing to migrate.');
        }
    }

    /**
     * Create a migrations table, which keeps track of the migrations already applied
     * 
     * @return void 
     */
    public function createMigrationsTable(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;
            ";

        $this->pdo->exec($query);
    }

    /**
     * Ritorna tutte le migrations applicate
     * 
     * @return array
     */
    public function getAppliedMigrations(): array
    {
        $stmt = $this->pdo->prepare("SELECT migration FROM migrations");
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * I insert in the migrations table, the name of each migration file
     * 
     * @param array $migrations
     * @return void
     */
    public function saveMigrations(array $migrations): void
    {
        $values = array_map(function ($migration) {
            return '(\'' . $migration . '\'),';
        }, $migrations);

        $values = rtrim(implode($values), ',');

        $query = "INSERT INTO migrations (migration) VALUES $values";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
    }

    /**
     * Returns all the filenames inside the migrations folder
     * 
     * @return array 
     */
    protected function getMigrationFilesName(): array
    {
        $files = scandir(ROOT_PATH . '/migrations');

        // Filter to take filenames only
        $files = array_filter($files, function ($file) {
            if (strlen($file > 3)) {
                return $file;
            }
        });

        // Remove the extension
        return array_map(function ($file) {
            return pathinfo($file, PATHINFO_FILENAME);
        }, $files);
    }

    /**
     * @return void 
     */
    public function truncateDatabaseTables(): void
    {
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=0");

        $stmt = $this->pdo->prepare("SHOW TABLES");
        $stmt->execute();

        $tables = $stmt->fetchAll();

        foreach ($tables as $table) {
            if (implode($table) === 'migrations') {
                continue;
            }
            $query = "TRUNCATE TABLE " . implode($table);
            $this->pdo->exec($query);
            $this->log("Truncated table " . implode($table));
        }

        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }

    /**
     * @return void 
     */
    public function dropDatabaseTables(): void
    {
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=0");

        $stmt = $this->pdo->prepare("SHOW TABLES");
        $stmt->execute();

        $tables = $stmt->fetchAll();

        foreach ($tables as $table) {
            $query = "DROP TABLE " . implode($table);
            $this->pdo->exec($query);
            $this->log("Dropped table " . implode($table));
        }

        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }

    /**
     * @param string $message
     * @return void 
     */
    protected function log(string $message): void
    {
        echo '[' . date('d-m-Y H:i:s') . '] - ' . $message . PHP_EOL;
    }
}
