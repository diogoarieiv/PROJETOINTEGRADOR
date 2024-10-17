<?php
$servername = "localhost";
$username = "root"; // Use o nome de usuário correto
$password = ""; // Se a senha do usuário root for diferente, coloque aqui
$dbname = "usuarios"; // Verifique se o nome do banco de dados está correto

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Opcional: Define o charset para evitar problemas com caracteres especiais
$conn->set_charset("utf8mb4");
?>

