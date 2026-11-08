<?php
// app/Models/Usuario.php

require_once BASE_PATH . 'app/Config/Database.php';

class Usuario {
    
    public static function buscarPorEmail($email) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
}