<?php
// app/Config/Env.php

class Env
{
    public static function load(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $i => $line) {
            if ($i === 0) {
                $line = preg_replace('/^\xEF\xBB\xBF/', '', $line);
            }

            $line = trim($line);

            if ($line === '' || $line[0] === '#') {
                continue;
            }

            if (str_starts_with($line, 'export ')) {
                $line = ltrim(substr($line, 7));
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);

            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                continue;
            }

            $value = self::parseValue(trim($value));

            if (getenv($key) === false && !isset($_ENV[$key])) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    private static function parseValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $first = $value[0];

        // Valor entre comillas: se quita solo el par exterior
        if ($first === '"' || $first === "'") {
            $end = strrpos($value, $first);
            if ($end !== false && $end > 0) {
                $inner = substr($value, 1, $end - 1);
                return $first === '"'
                    ? str_replace(['\\"', '\\n'], ['"', "\n"], $inner)
                    : $inner;
            }
        }

        // Sin comillas: se ignora un comentario " #..." al final
        $pos = strpos($value, ' #');
        if ($pos !== false) {
            $value = substr($value, 0, $pos);
        }

        return trim($value);
    }
}