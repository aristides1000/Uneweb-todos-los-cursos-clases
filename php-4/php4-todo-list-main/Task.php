<?php
class Task {
    // Conexion y nombre de la tabla
    private $conn;
    private $table_name = "tasks";

    // Propiedades del Objeto
    public $id;
    public $title;
    public $completed;

    // Constructor: Recibe la conexion a la base de datos cuando se instancia el objeto
    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. LEER TAREAS (READ)
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // 2. CREAR TAREA (CREATE)
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (title) VALUES (:title)";
        $stmt = $this->conn->prepare($query);

        // Limpiar entrada (evitar HTML/script dañinos)
        $this->title = htmlspecialchars(strip_tags($this->title));

        // Vincular parametros para prevenir SQL Injection
        $stmt->bindParam(":title", $this->title);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // 3. CAMBIAR ESTADO / COMPLETAR (UPDATE)
    public function toggleComplete() {
        $query = "UPDATE " . $this->table_name . " SET completed = :completed WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":completed", $this->completed);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // 4. ELIMINAR TAREA (DELETE)
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}