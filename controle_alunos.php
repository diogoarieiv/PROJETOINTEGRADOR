<?php
session_start();
include 'conexao.php'; // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.html');
    exit();
}

// Inicializa a variável de pesquisa
$pesquisa = isset($_POST['pesquisa']) ? $_POST['pesquisa'] : '';

// Busca alunos com base na pesquisa
if ($pesquisa) {
    $query = $conn->prepare("SELECT * FROM alunos WHERE nome LIKE ?");
    $likePesquisa = "%" . $pesquisa . "%"; // Adiciona wildcards para a pesquisa
    $query->bind_param("s", $likePesquisa);
} else {
    $query = $conn->prepare("SELECT * FROM alunos");
}

$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Alunos</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #dddddd;
        }

        th {
            background-color: #00a727;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .botao-container {
            display: flex;
            gap: 10px;
        }

        input[type="text"] {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        button {
            padding: 10px 15px;
            background-color: #00a727;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        .auth-buttons {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Controle de Alunos</h1>
        <div class="auth-buttons">
            <button onclick="location.href='logout.php'">Sair</button>
        </div>
    </header>

    <main>
        <h2>Lista de Alunos</h2>

        <!-- Formulário de pesquisa -->
        <form method="POST" action="">
            <input type="text" name="pesquisa" placeholder="Pesquisar por nome" value="<?php echo htmlspecialchars($pesquisa); ?>">
            <button type="submit">Buscar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Endereço</th> <!-- Adicionando coluna para endereço -->
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($aluno = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $aluno['id']; ?></td>
                        <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['endereco']); ?></td> <!-- Exibindo endereço -->
                        <td>
                            <div class="botao-container">
                                <button onclick="location.href='editar_aluno.php?id=<?php echo $aluno['id']; ?>'">Editar</button>
                                <button onclick="location.href='deletar_aluno.php?id=<?php echo $aluno['id']; ?>'">Deletar</button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="botao-container">
            <button onclick="location.href='cadastro_aluno.php'">Cadastrar Novo Aluno</button>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Vale Transporte. Todos os direitos reservados.</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
