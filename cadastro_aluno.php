<?php
session_start();
include 'conexao.php'; // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.html'); // Redireciona se não estiver logado
    exit();
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];
    $endereco = $_POST['endereco']; // Novo campo para endereço

    // Insere novo aluno
    $stmt = $conn->prepare("INSERT INTO alunos (nome, senha, endereco) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $senha, $endereco); // Adicionando o endereço

    if ($stmt->execute()) {
        echo "Aluno cadastrado com sucesso!";
        header('Location: controle_alunos.php'); // Redireciona para controle de alunos
        exit();
    } else {
        echo "Erro ao cadastrar aluno: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Cadastro de Aluno</h1>
        <div class="auth-buttons">
            <button onclick="location.href='controle_alunos.php'">Voltar</button>
        </div>
    </header>

    <main>
        <form action="cadastro_aluno.php" method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" required>

            <button type="submit">Cadastrar</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Vale Transporte. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
