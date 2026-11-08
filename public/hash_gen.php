<?php
// public/hash_gen.php
$passwordOriginal = '123456';
$hashGenerado = password_hash($passwordOriginal, PASSWORD_DEFAULT);

echo "Contraseña: " . $passwordOriginal . "<br>";
echo "Hash generado por tu PHP 8.2: " . $hashGenerado . "<br>";
echo "Longitud del hash: " . strlen($hashGenerado);