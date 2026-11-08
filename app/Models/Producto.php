<?php
// app/Models/Producto.php

require_once BASE_PATH . 'app/Config/Database.php';

class Producto {

    public static function obtenerTodos() {
        $db = Database::connect();
        // Mapeamos los nombres de la BD a los que usa la vista para evitar romper nada
        $stmt = $db->query("
            SELECT p.*, 
                   p.porcentaje_ganancia AS margen_ganancia, 
                   p.precio_venta_usd AS precio_usd, 
                   c.nombre AS categoria_nombre 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            ORDER BY p.nombre ASC
        ");
        return $stmt->fetchAll();
    }

    public static function crear($data) {
            $db = Database::connect();

            // 1. Validar que el código/SKU no exista previamente en el inventario
            if (!empty($data['codigo'])) {
                $stmtCodigo = $db->prepare("SELECT id FROM productos WHERE codigo = :codigo");
                $stmtCodigo->execute(['codigo' => trim($data['codigo'])]);
                if ($stmtCodigo->fetch()) {
                    throw new Exception("El código o SKU '{$data['codigo']}' ya se encuentra registrado en otro producto.");
                }
            }

            // 2. Validar que la categoría exista (si se seleccionó una)
            if (!empty($data['categoria_id'])) {
                $stmtCat = $db->prepare("SELECT id FROM categorias WHERE id = :categoria_id");
                $stmtCat->execute(['categoria_id' => $data['categoria_id']]);
                if (!$stmtCat->fetch()) {
                    throw new Exception("La categoría seleccionada no es válida o ya no existe.");
                }
            }

            // 3. Sanitización y validación estricta de variables numéricas y de negocio
            $costoUsd = filter_var($data['costo_usd'], FILTER_VALIDATE_FLOAT);
            $margen   = filter_var($data['margen_ganancia'], FILTER_VALIDATE_FLOAT);
            $stock    = filter_var($data['stock'], FILTER_VALIDATE_INT);
            $stockMin = filter_var($data['stock_minimo'] ?? 5, FILTER_VALIDATE_INT);

            if ($costoUsd === false || $costoUsd < 0) {
                throw new Exception("El costo en USD debe ser un número válido mayor o igual a cero.");
            }
            if ($margen === false || $margen < 0) {
                throw new Exception("El porcentaje de ganancia no puede ser un valor negativo.");
            }
            if ($stock === false || $stock < 0) {
                throw new Exception("El stock inicial debe ser un número entero válido.");
            }

            // 4. Ejecución segura del INSERT
            $stmt = $db->prepare("
                INSERT INTO productos (codigo, nombre, categoria_id, costo_usd, porcentaje_ganancia, precio_venta_usd, stock, stock_minimo)
                VALUES (:codigo, :nombre, :categoria_id, :costo_usd, :porcentaje_ganancia, :precio_venta_usd, :stock, :stock_minimo)
            ");

            $executed = $stmt->execute([
                'codigo'              => strtoupper(trim($data['codigo'])), // Estandarizamos SKU en mayúsculas por orden
                'nombre'              => trim($data['nombre']),
                'categoria_id'        => !empty($data['categoria_id']) ? $data['categoria_id'] : null,
                'costo_usd'           => $costoUsd,
                'porcentaje_ganancia' => $margen,
                'precio_venta_usd'    => $data['precio_usd'],
                'stock'               => $stock,
                'stock_minimo'        => max(1, $stockMin)
            ]);

            return $executed ? $db->lastInsertId() : false;
        }

    public static function actualizar($id, $data) {
            $db = Database::connect();

            // 1. Validar que el producto exista antes de actualizar
            $stmtCheck = $db->prepare("SELECT id FROM productos WHERE id = :id");
            $stmtCheck->execute(['id' => $id]);
            if (!$stmtCheck->fetch()) {
                throw new Exception("El producto que intenta actualizar no existe en el sistema.");
            }

            // 2. Validar que el código/SKU no esté siendo utilizado por OTRO producto
            if (!empty($data['codigo'])) {
                $stmtCodigo = $db->prepare("SELECT id FROM productos WHERE codigo = :codigo AND id != :id");
                $stmtCodigo->execute(['codigo' => $data['codigo'], 'id' => $id]);
                if ($stmtCodigo->fetch()) {
                    throw new Exception("El código o SKU '{$data['codigo']}' ya está asignado a otro producto.");
                }
            }

            // 3. Validar que la categoría exista (si se proporcionó una)
            if (!empty($data['categoria_id'])) {
                $stmtCat = $db->prepare("SELECT id FROM categorias WHERE id = :categoria_id");
                $stmtCat->execute(['categoria_id' => $data['categoria_id']]);
                if (!$stmtCat->fetch()) {
                    throw new Exception("La categoría seleccionada no es válida o ya no existe.");
                }
            }

            // 4. Sanitización y validación de reglas de negocio numéricas
            $costoUsd = filter_var($data['costo_usd'], FILTER_VALIDATE_FLOAT);
            $margen   = filter_var($data['margen_ganancia'], FILTER_VALIDATE_FLOAT);
            $stock    = filter_var($data['stock'], FILTER_VALIDATE_INT);

            if ($costoUsd === false || $costoUsd < 0) {
                throw new Exception("El costo en USD debe ser un valor numérico válido mayor o igual a cero.");
            }
            if ($margen === false || $margen < 0) {
                throw new Exception("El porcentaje de ganancia no puede ser negativo.");
            }
            if ($stock === false || $stock < 0) {
                throw new Exception("El stock inicial debe ser un número entero válido.");
            }

            // 5. Ejecución segura de la actualización
            $stmt = $db->prepare("
                UPDATE productos 
                SET codigo = :codigo,
                    nombre = :nombre,
                    categoria_id = :categoria_id,
                    costo_usd = :costo_usd,
                    porcentaje_ganancia = :porcentaje_ganancia,
                    precio_venta_usd = :precio_venta_usd,
                    stock = :stock,
                    stock_minimo = :stock_minimo
                WHERE id = :id
            ");

            return $stmt->execute([
                'id'                  => $id,
                'codigo'              => trim($data['codigo']),
                'nombre'              => trim($data['nombre']),
                'categoria_id'        => !empty($data['categoria_id']) ? $data['categoria_id'] : null,
                'costo_usd'           =>  $costoUsd,
                'porcentaje_ganancia' => $margen,
                'precio_venta_usd'    => $data['precio_usd'],
                'stock'               => $stock,
                'stock_minimo'        => max(1, (int)($data['stock_minimo'] ?? 5))
            ]);
        }

    public static function eliminar($id) {
            $db = Database::connect();

            // 1. Verificar si el producto tiene salidas registradas (detalles de ventas/despachos)
            $stmtSalidas = $db->prepare("SELECT COUNT(*) FROM salida_detalles WHERE producto_id = :id");
            $stmtSalidas->execute(['id' => $id]);
            if ($stmtSalidas->fetchColumn() > 0) {
                throw new Exception("No se puede eliminar el producto porque posee registros en salidas o ventas históricas.");
            }

            // 2. Verificar si el producto está en detalles de créditos (cuentas por cobrar)
            $stmtCreditos = $db->prepare("SELECT COUNT(*) FROM credito_detalles WHERE tipo_item = 'producto' AND item_id = :id");
            $stmtCreditos->execute(['id' => $id]);
            if ($stmtCreditos->fetchColumn() > 0) {
                throw new Exception("No se puede eliminar el producto porque está asociado a créditos o cuentas por cobrar.");
            }

            // Si pasa todas las validaciones de negocio, procedemos a eliminar de forma segura
            $stmt = $db->prepare("DELETE FROM productos WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        }
    public static function registrarRecepcionLote($usuarioId, $observaciones, $items) {
        $db = Database::connect();

        try {
            // Iniciamos la transacción para garantizar integridad total
            $db->beginTransaction();

            // 1. Insertar la cabecera de la recepción
            $stmtCabecera = $db->prepare("
                INSERT INTO recepciones_inventario (usuario_id, observaciones, created_at) 
                VALUES (:usuario_id, :observaciones, NOW())
            ");
            $stmtCabecera->execute([
                'usuario_id'    => $usuarioId,
                'observaciones' => trim($observaciones) ?: null
            ]);
            $recepcionId = $db->lastInsertId();

            // Preparar sentencias reutilizables dentro del bucle
            $stmtDetalle = $db->prepare("
                INSERT INTO recepcion_detalles (recepcion_id, producto_id, cantidad_recibida, costo_unitario_registrado, precio_venta_aplicado, actualizo_precio) 
                VALUES (:recepcion_id, :producto_id, :cantidad_recibida, :costo_unitario, :precio_aplicado, :actualizo_precio)
            ");

            $stmtUpdateStock = $db->prepare("
                UPDATE productos 
                SET stock = stock + :cantidad 
                WHERE id = :id
            ");

            $stmtUpdateCostoPrecio = $db->prepare("
                UPDATE productos 
                SET costo_usd = :costo_nuevo, 
                    precio_venta_usd = :precio_nuevo 
                WHERE id = :id
            ");

            $stmtGetProducto = $db->prepare("SELECT costo_usd, porcentaje_ganancia, precio_venta_usd FROM productos WHERE id = :id");

            // 2. Iterar sobre cada producto recibido en el lote
            foreach ($items as $item) {
                $productoId       = (int) $item['producto_id'];
                $cantidad         = (int) $item['cantidad'];
                $costoNuevo       = (float) $item['costo_nuevo'];
                $actualizarPrecio = isset($item['actualizar_precio']) ? 1 : 0;

                if ($cantidad <= 0 || $costoNuevo < 0) {
                    throw new Exception("Los valores de cantidad o costo para uno de los productos no son válidos.");
                }

                // Obtener datos actuales del producto para cálculos seguros
                $stmtGetProducto->execute(['id' => $productoId]);
                $prodActual = $stmtGetProducto->fetch();
                if (!$prodActual) {
                    throw new Exception("El producto con ID {$productoId} no existe en el sistema.");
                }

                $margenGanancia = (float) $prodActual['porcentaje_ganancia'];
                $precioAplicado = (float) $prodActual['precio_venta_usd'];

                // 3. Aplicar lógica comercial si decidió actualizar costo y precio
                if ($actualizarPrecio === 1) {
                    // Recalcular precio de venta basado en el nuevo costo y el margen existente
                    $precioAplicado = $costoNuevo + ($costoNuevo * ($margenGanancia / 100));

                    $stmtUpdateCostoPrecio->execute([
                        'costo_nuevo'   => $costoNuevo,
                        'precio_nuevo'  => $precioAplicado,
                        'id'            => $productoId
                    ]);
                }

                // 4. Sumar la cantidad al stock general del producto
                $stmtUpdateStock->execute([
                    'cantidad' => $cantidad,
                    'id'       => $productoId
                ]);

                // 5. Registrar la línea de detalle histórica de esta recepción
                $stmtDetalle->execute([
                    'recepcion_id'      => $recepcionId,
                    'producto_id'       => $productoId,
                    'cantidad_recibida' => $cantidad,
                    'costo_unitario'    => $costoNuevo,
                    'precio_aplicado'   => $precioAplicado,
                    'actualizo_precio'  => $actualizarPrecio
                ]);
            }

            // Confirmar transacción si todo salió perfecto
            $db->commit();
            return true;

        } catch (Exception $e) {
            // Revertir cambios si ocurre cualquier fallo
            $db->rollBack();
            throw $e;
        }
    }

    public static function obtenerHistorialRecepciones() {
        $db = Database::connect();
        $stmt = $db->query("
            SELECT r.*, u.nombre AS usuario_nombre,
                   (SELECT COUNT(*) FROM recepcion_detalles WHERE recepcion_id = r.id) AS total_items
            FROM recepciones_inventario r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function obtenerHistorialRecepcionesPaginado($limite, $offset) {
        $db = Database::connect();
        
        // Consulta paginada con LIMIT y OFFSET seguros
        $stmt = $db->prepare("
            SELECT r.*, u.nombre AS usuario_nombre,
                   (SELECT COUNT(*) FROM recepcion_detalles WHERE recepcion_id = r.id) AS total_items
            FROM recepciones_inventario r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            ORDER BY r.created_at DESC
            LIMIT :limite OFFSET :offset
        ");
        $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public static function contarTotalRecepciones() {
        $db = Database::connect();
        $stmt = $db->query("SELECT COUNT(*) FROM recepciones_inventario");
        return (int) $stmt->fetchColumn();
    }

    public static function obtenerDetallesRecepcion($recepcionId) {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT d.*, p.nombre AS producto_nombre, p.codigo AS producto_codigo
            FROM recepcion_detalles d
            JOIN productos p ON d.producto_id = p.id
            WHERE d.recepcion_id = :recepcion_id
        ");
        $stmt->execute(['recepcion_id' => $recepcionId]);
        return $stmt->fetchAll();
    }
}