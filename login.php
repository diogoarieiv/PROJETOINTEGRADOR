<?php
session_start();
include 'conexao.php'; // Certifique-se de que 'conexao.php' contém a conexão correta com o banco de dados

$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $senha = '';

    if ($tipo === 'admin') {
        $usuario = $_POST['usuario'] ?? '';
        $senha = $_POST['senha'] ?? '';

        // Prepare a consulta
        $query = $conn->prepare("SELECT * FROM admin WHERE usuario = ?");
        if ($query) {
            $query->bind_param("s", $usuario);
            $query->execute();
            $result = $query->get_result();

            if ($result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                // Comparar senhas diretamente
                if ($senha === $admin['senha']) {
                    $_SESSION['usuario'] = $usuario;
                    header('Location: controle_alunos.php');
                    exit();
                } else {
                    $mensagemErro = "Usuário ou senha do administrador inválidos.";
                }
            } else {
                $mensagemErro = "Usuário ou senha do administrador inválidos.";
            }
        } else {
            $mensagemErro = "Erro na consulta ao banco de dados.";
        }
    } else {
        $id = $_POST['id'] ?? '';
        $senha = $_POST['senha_aluno'] ?? '';

        $query = $conn->prepare("SELECT * FROM alunos WHERE id = ?");
        if ($query) {
            $query->bind_param("s", $id);
            $query->execute();
            $result = $query->get_result();

            if ($result->num_rows > 0) {
                $aluno = $result->fetch_assoc();
                // Comparar senhas diretamente
                if ($senha === $aluno['senha']) {
                    $_SESSION['id'] = $id;
                    header('Location: situacao_aluno.php');
                    exit();
                } else {
                    $mensagemErro = "ID ou senha do aluno inválidos.";
                }
            } else {
                $mensagemErro = "ID ou senha do aluno inválidos.";
            }
        } else {
            $mensagemErro = "Erro na consulta ao banco de dados.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Login</h1>
        <div class="auth-buttons">
            <button onclick="location.href='index.html'">Voltar</button>
        </div>
    </header>

    <main>
        <section id="login">
            <!-- Formulário de login para Administrador -->
            <h2>Administrador</h2>
            <form id="login-admin" action="" method="POST">
                <input type="hidden" name="tipo" value="admin">
                <label for="usuario">Usuário:</label>
                <input type="text" id="usuario" name="usuario" required>
                
                <label for="senha-admin">Senha:</label>
                <input type="password" id="senha-admin" name="senha" required>

                <button type="submit">Entrar</button>
            </form>

            <!-- Formulário de login para Aluno -->
            <h2>Aluno</h2>
            <form id="login-aluno" action="" method="POST">
                <input type="hidden" name="tipo" value="aluno">
                <label for="id">ID do Aluno:</label>
                <input type="text" id="id" name="id" required>
                
                <label for="senha-aluno">Senha:</label>
                <input type="password" id="senha-aluno" name="senha_aluno" required>

                <button type="submit">Entrar</button>
            </form>

            <?php if (!empty($mensagemErro)): ?>
                <div class="mensagem-erro">
                    <?php echo htmlspecialchars($mensagemErro); ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 ValeSmart. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
