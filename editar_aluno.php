<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.html');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Busca o aluno
    $stmt = $conn->prepare("SELECT * FROM alunos WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $aluno = $result->fetch_assoc();
    } else {
        echo "Aluno não encontrado.";
        exit();
    }

    // Atualiza os dados do aluno
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST['nome'];
        $senha = isset($_POST['senha']) ? $_POST['senha'] : null;
        $endereco = $_POST['endereco'];

        if ($senha) {
            $stmt = $conn->prepare("UPDATE alunos SET nome = ?, senha = ?, endereco = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nome, $senha, $endereco, $id);
        } else {
            $stmt = $conn->prepare("UPDATE alunos SET nome = ?, endereco = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $endereco, $id);
        }

        if ($stmt->execute()) {
            header('Location: controle_alunos.php');
            exit();
        } else {
            echo "Erro ao atualizar aluno: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    echo "ID do aluno não fornecido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edição de Aluno</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Edição de Aluno</h1>
        <div class="auth-buttons">
            <button onclick="location.href='controle_alunos.php'">Voltar</button>
        </div>
    </header>

    <main>
        <form action="editar_aluno.php?id=<?php echo $id; ?>" method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($aluno['nome']); ?>" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha">

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($aluno['endereco']); ?>" required>

            <button type="submit">Atualizar</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Vale Transporte. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
