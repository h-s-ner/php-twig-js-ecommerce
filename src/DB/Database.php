<?php
namespace App\DB;

use \Dotenv\Dotenv;

Class Database{

    protected \PDO $conn;

    private String $db_host;
    private String $db_user;
    private String $db_pass;
    private String $db_name;

    public function __construct()
    {
        $this->connect();
    }
    // protect damit nur in class und subclass verwendet werden kann
    private function connect(): \PDO{
        $this->init_db();
        $this->conn = new \PDO(
            "mysql:host=$this->db_host;dbname=$this->db_name",
            $this->db_user,
            $this->db_pass,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
        return $this->conn;
    }
    private function init_db(): void {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../../','.env');
        $dotenv->load();
        $this->db_host = $_ENV['DB_HOST'];
        $this->db_user = $_ENV['DB_USER'];
        $this->db_pass = $_ENV['DB_PASS'];
        $this->db_name = $_ENV['DB_NAME'];
    }
}

?>