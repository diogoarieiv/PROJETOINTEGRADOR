<?php
session_start();
include 'conexao.php'; // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.html'); // Redireciona se não estiver logado
    exit();
}

// Verifica se o ID do aluno foi passado
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Deleta o aluno
    $stmt = $conn->prepare("DELETE FROM alunos WHERE id = ?");
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        header('Location: controle_alunos.php'); // Redireciona após a deleção
        exit();
    } else {
        echo "Erro ao deletar aluno: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ID do aluno não fornecido.";
    exit();
}

$conn->close();
?>