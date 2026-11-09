<?php
// app/Models/Usuario.php

require_once BASE_PATH . 'app/Config/Database.php';

class Usuario {
    
    public static function buscarPorEmail($email) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId($id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT id, nombre, email, rol, created_at FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerTodos() {
        $db = Database::connect();
        $stmt = $db->query("SELECT id, nombre, email, rol, created_at FROM usuarios ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function registrar($data) {
        $db = Database::connect();
        
        // Verificar si el correo ya existe
        if (self::buscarPorEmail($data['email'])) {
            throw new Exception("El correo electrónico ya se encuentra registrado en el sistema.");
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO usuarios (nombre, email, password, rol) 
            VALUES (:nombre, :email, :password, :rol)
        ");
        
        return $stmt->execute([
            'nombre'   => trim($data['nombre']),
            'email'    => trim($data['email']),
            'password' => $passwordHash,
            'rol'      => $data['rol'] ?? 'operador'
        ]);
    }

    public static function actualizarCampo($id, $campo, $valor) {
        $db = Database::connect();
        
        $camposPermitidos = ['nombre', 'email', 'password', 'rol'];
        if (!in_array($campo, $camposPermitidos)) {
            throw new Exception("Campo de actualización no válido.");
        }

        if ($campo === 'email') {
            $existente = self::buscarPorEmail($valor);
            if ($existente && $existente['id'] != $id) {
                throw new Exception("El correo electrónico ya está asignado a otro usuario.");
            }
        }

        if ($campo === 'password') {
            $valor = password_hash($valor, PASSWORD_DEFAULT);
        }

        $stmt = $db->prepare("UPDATE usuarios SET {$campo} = :valor WHERE id = :id");
        return $stmt->execute(['valor' => $valor, 'id' => $id]);
    }

    public static function eliminar($id) {
        $db = Database::connect();
        
        // Evitar auto-eliminación si es el único admin o el usuario actual
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
            throw new Exception("No puedes eliminar tu propio usuario activo.");
        }

        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Actualizar los datos generales de un usuario desde la administración.
     */
    public static function actualizarAdmin($id, $nombre, $email, $rol, $password = null) {
        $db = Database::connect();

        // Validar si el correo ya pertenece a otro usuario
        $existente = self::buscarPorEmail($email);
        if ($existente && $existente['id'] != $id) {
            throw new Exception("El correo electrónico ya se encuentra registrado por otro usuario.");
        }

        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("
                UPDATE usuarios 
                SET nombre = :nombre, email = :email, rol = :rol, password = :password 
                WHERE id = :id
            ");
            return $stmt->execute([
                'nombre'   => trim($nombre),
                'email'    => trim($email),
                'rol'      => $rol,
                'password' => $passwordHash,
                'id'       => $id
            ]);
        } else {
            $stmt = $db->prepare("
                UPDATE usuarios 
                SET nombre = :nombre, email = :email, rol = :rol 
                WHERE id = :id
            ");
            return $stmt->execute([
                'nombre' => trim($nombre),
                'email'  => trim($email),
                'rol'    => $rol,
                'id'     => $id
            ]);
        }
    }
}