<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=mailmarket", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão bem-sucedida.";
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
