<?php
// app/Services/BcvRateService.php

require_once BASE_PATH . 'app/Config/Database.php';

class BcvRateService {

    private const API_URL = 'https://rates.dolarvzla.com/bcv/current.json';

    /**
     * Obtiene la tasa activa. Primero busca en BD (menos de 6 horas de antigüedad),
     * si no hay o está vencida, consulta la API pública y guarda en BD.
     */
    public function getCurrentRate(string $currency = 'USD'): float {
        $currency = strtoupper($currency);
        $db = Database::connect();

        // 1. Intentar obtener desde la base de datos (últimos registros dentro de 6 horas)
        $stmt = $db->prepare("
            SELECT rate FROM exchange_rates 
            WHERE currency = :currency 
              AND fetched_at >= DATE_SUB(NOW(), INTERVAL 6 HOUR)
            ORDER BY fetched_at DESC LIMIT 1
        ");
        $stmt->execute(['currency' => $currency]);
        $latest = $stmt->fetch();

        if ($latest) {
            return (float) $latest['rate'];
        }

        // 2. Si no hay datos recientes, consultar la API
        return $this->fetchFromApi($currency);
    }

    /**
     * Consume la API externa y persiste en la BD.
     */
    private function fetchFromApi(string $currency): float {
        try {
            // Inicializar cURL para consumo seguro con timeout de 10s
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::API_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Evita bloqueos de certificado SSL local

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$response) {
                return $this->getFallbackRate($currency);
            }

            $data = json_decode($response, true);

            $rate = match ($currency) {
                'USD' => $data['current']['usd'] ?? null,
                'EUR' => $data['current']['eur'] ?? null,
                default => null,
            };

            if (!$rate) {
                return $this->getFallbackRate($currency);
            }

            $rateFloat = (float) $rate;
            $rateDate = $data['current']['date'] ?? date('Y-m-d');

            // Guardar en la base de datos para el historial y caché
            $db = Database::connect();
            $stmt = $db->prepare("
                INSERT INTO exchange_rates (currency, rate, rate_date, fetched_at)
                VALUES (:currency, :rate, :rate_date, NOW())
            ");
            $stmt->execute([
                'currency' => $currency,
                'rate' => $rateFloat,
                'rate_date' => $rateDate
            ]);

            return $rateFloat;

        } catch (\Exception $e) {
            return $this->getFallbackRate($currency);
        }
    }

    /**
     * Respaldo: Si falla la conexión o la API, obtiene la última tasa registrada sin importar la fecha.
     */
    private function getFallbackRate(string $currency): float {
        $db = Database::connect();
        $stmt = $db->prepare("
            SELECT rate FROM exchange_rates 
            WHERE currency = :currency 
            ORDER BY fetched_at DESC LIMIT 1
        ");
        $stmt->execute(['currency' => $currency]);
        $latest = $stmt->fetch();

        // Tasa por defecto si la base de datos estuviera completamente vacía
        return $latest ? (float) $latest['rate'] : 1.0000;
    }

    /**
     * Forzar la actualización manual de la tasa consumiendo la API directamente
     */
    public function refreshRate(string $currency = 'USD'): float {
        return $this->fetchFromApi(strtoupper($currency));
    }
}