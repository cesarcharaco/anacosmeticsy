<?php
// app/Models/Cliente.php

require_once BASE_PATH . 'app/Config/Database.php';

class Cliente {

    public static function obtenerTodos($limite = 10, $offset = 0) {
        $db = Database::connect();
        $limite = (int) $limite;
        $offset = (int) $offset;
        
        $stmt = $db->prepare("SELECT * FROM clientes ORDER BY nombre ASC LIMIT :limite OFFSET :offset");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function contarTotal() {
        $db = Database::connect();
        $stmt = $db->query("SELECT COUNT(*) FROM clientes");
        return $stmt->fetchColumn();
    }

    public static function obtenerPorId($id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM clientes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public static function crear($data) {
        $db = Database::connect();

        // 1. Limpieza y validaciones de campos obligatorios
        $nombre = trim($data['nombre'] ?? '');
        if (empty($nombre)) {
            throw new Exception("El nombre del cliente es obligatorio.");
        }
        if (strlen($nombre) > 150) {
            throw new Exception("El nombre no puede superar los 150 caracteres.");
        }

        $telefono = trim($data['telefono'] ?? '');
        $direccion = trim($data['direccion'] ?? '');
        
        $saldoFavor = filter_var($data['saldo_favor_usd'] ?? 0, FILTER_VALIDATE_FLOAT);
        if ($saldoFavor === false || $saldoFavor < 0) {
            throw new Exception("El saldo a favor en USD no puede ser un valor negativo.");
        }

        // 2. Regla de Negocio: Evitar duplicados por Nombre exacto o Teléfono (si se proporciona)
        if (!empty($telefono)) {
            $stmtDup = $db->prepare("SELECT COUNT(*) FROM clientes WHERE nombre = :nombre OR telefono = :telefono");
            $stmtDup->execute(['nombre' => $nombre, 'telefono' => $telefono]);
        } else {
            $stmtDup = $db->prepare("SELECT COUNT(*) FROM clientes WHERE nombre = :nombre");
            $stmtDup->execute(['nombre' => $nombre]);
        }

        if ($stmtDup->fetchColumn() > 0) {
            throw new Exception("Ya existe un cliente registrado con este nombre o número de teléfono.");
        }

        // 3. Inserción segura
        $stmt = $db->prepare("
            INSERT INTO clientes (nombre, telefono, direccion, saldo_favor_usd)
            VALUES (:nombre, :telefono, :direccion, :saldo_favor_usd)
        ");
        
        return $stmt->execute([
            'nombre'          => $nombre,
            'telefono'        => $telefono,
            'direccion'       => $direccion,
            'saldo_favor_usd' => round($saldoFavor, 2)
        ]);
    }

    public static function actualizar($id, $data) {
        $db = Database::connect();

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception("ID de cliente inválido.");
        }

        $nombre = trim($data['nombre'] ?? '');
        if (empty($nombre)) {
            throw new Exception("El nombre del cliente es obligatorio.");
        }

        $telefono = trim($data['telefono'] ?? '');
        $direccion = trim($data['direccion'] ?? '');

        $saldoFavor = filter_var($data['saldo_favor_usd'] ?? 0, FILTER_VALIDATE_FLOAT);
        if ($saldoFavor === false || $saldoFavor < 0) {
            throw new Exception("El saldo a favor no puede ser negativo.");
        }

        // Regla de Negocio: Evitar duplicados al actualizar (excluyendo el propio ID)
        if (!empty($telefono)) {
            $stmtDup = $db->prepare("SELECT COUNT(*) FROM clientes WHERE (nombre = :nombre OR telefono = :telefono) AND id != :id");
            $stmtDup->execute(['nombre' => $nombre, 'telefono' => $telefono, 'id' => $id]);
        } else {
            $stmtDup = $db->prepare("SELECT COUNT(*) FROM clientes WHERE nombre = :nombre AND id != :id");
            $stmtDup->execute(['nombre' => $nombre, 'id' => $id]);
        }

        if ($stmtDup->fetchColumn() > 0) {
            throw new Exception("Ya existe otro cliente registrado con este mismo nombre o teléfono.");
        }

        $stmt = $db->prepare("
            UPDATE clientes 
            SET nombre = :nombre, telefono = :telefono, direccion = :direccion, saldo_favor_usd = :saldo_favor_usd
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id'              => $id,
            'nombre'          => $nombre,
            'telefono'        => $telefono,
            'direccion'       => $direccion,
            'saldo_favor_usd' => round($saldoFavor, 2)
        ]);
    }

    public static function eliminar($id) {
        $db = Database::connect();
        
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception("ID de cliente inválido.");
        }

        // 1. Regla de Negocio: Verificar si tiene citas asociadas
        $stmtCitas = $db->prepare("SELECT COUNT(*) FROM citas WHERE cliente_id = :id");
        $stmtCitas->execute(['id' => $id]);
        if ($stmtCitas->fetchColumn() > 0) {
            throw new Exception("No se puede eliminar el cliente porque posee un historial de citas agendadas.");
        }

        // 2. Regla de Negocio: Verificar si el cliente tiene deudas pendientes (Créditos / Cuentas por cobrar)
        // Nota: Asumimos una tabla 'creditos' o 'deudas' con campo 'cliente_id' y 'estado' o 'saldo_pendiente'
        $stmtDeudas = $db->prepare("SELECT COUNT(*) FROM creditos WHERE cliente_id = :id AND saldo_pendiente_usd > 0");
        $stmtDeudas->execute(['id' => $id]);
        if ($stmtDeudas->fetchColumn() > 0) {
            throw new Exception("No se puede eliminar el cliente porque mantiene deudas o créditos pendientes por pagar.");
        }

        // 3. Ejecutar eliminación segura si pasa todas las restricciones
        $stmt = $db->prepare("DELETE FROM clientes WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}