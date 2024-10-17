<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$id_aluno = $_SESSION['id'];
$query = $conn->prepare("SELECT * FROM alunos WHERE id = ?");
$query->bind_param("s", $id_aluno);
$query->execute();
$result = $query->get_result();
$aluno = $result->fetch_assoc();

if ($aluno) {
    $endereco_aluno = $aluno['endereco'];
    $endereco_escola = "R. João Pereira dos Santos, 99 - Pte. do Imaruim, Palhoça - SC, 88130-475, Brasil"; // Exemplo fixo
    $situacao_vale = "Calculando..."; // Situação inicial
    $distanciaLimite = 6; // Distância em km para aprovação do vale
} else {
    echo "Aluno não encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situação do Vale-Transporte</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyNTgZjPeLMiuVRbW5ldRkzvrcfGBLtnM&callback=initMap" async defer></script>
</head>
<body>
    <header>
        <h1>Situação do Vale-Transporte</h1>
        <div class="auth-buttons">
            <button onclick="location.href='logout.php'">Sair</button> <!-- Botão para logout -->
        </div>
    </header>

    <main>
        <section id="situacao">
            <h2>Olá, <?php echo htmlspecialchars($aluno['nome']); ?>!</h2>
            <p>Situação do seu vale-transporte: <strong><?php echo htmlspecialchars($situacao_vale); ?></strong></p>
            <p>Distância: <strong id="distancia"></strong></p> <!-- Exibição da distância -->
            <div id="map" style="height: 600px; width: 100%;"></div> <!-- Aumentei a altura do mapa -->
        </section>
    </main>

    <footer>
        <p>&copy; 2024 ValeSmart. Todos os direitos reservados.</p>
    </footer>

    <script>
        let alunoLocation, escolaLocation;

        function initMap() {
            const enderecoAluno = "<?php echo htmlspecialchars($endereco_aluno); ?>";
            const enderecoEscola = "<?php echo htmlspecialchars($endereco_escola); ?>";
            const geocoder = new google.maps.Geocoder();

            // Geocodifica o endereço do aluno
            geocoder.geocode({ 'address': enderecoAluno }, (results, status) => {
                if (status === 'OK' && results.length > 0) {
                    alunoLocation = results[0].geometry.location;

                    // Geocodifica o endereço da escola
                    geocoder.geocode({ 'address': enderecoEscola }, (results, status) => {
                        if (status === 'OK' && results.length > 0) {
                            escolaLocation = results[0].geometry.location;

                            const map = new google.maps.Map(document.getElementById('map'), {
                                zoom: 14,
                                center: alunoLocation,
                            });

                            new google.maps.Marker({
                                position: alunoLocation,
                                map: map,
                                title: 'Sua Localização',
                            });

                            new google.maps.Marker({
                                position: escolaLocation,
                                map: map,
                                title: 'Escola',
                            });

                            calcularDistancia(map);
                        } else {
                            console.error('Erro ao geocodificar o endereço da escola: ' + status);
                        }
                    });
                } else {
                    console.error('Erro ao geocodificar o endereço do aluno: ' + status);
                }
            });
        }

        function calcularDistancia(map) {
            const service = new google.maps.DistanceMatrixService();
            service.getDistanceMatrix({
                origins: [alunoLocation],
                destinations: [escolaLocation],
                travelMode: google.maps.TravelMode.DRIVING,
            }, (response, status) => {
                if (status === 'OK' && response.rows[0].elements[0].status === "OK") {
                    const distancia = response.rows[0].elements[0].distance.value / 1000; // em km
                    document.getElementById('distancia').textContent = distancia.toFixed(2) + ' km'; // Exibe a distância
                    definirSituacaoVale(distancia);

                    // Adiciona um círculo ao mapa para representar o raio na localização do aluno
                    const radius = new google.maps.Circle({
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '', // Sem fundo
                        fillOpacity: 0, // Sem preenchimento
                        map: map,
                        center: escolaLocation, // Centro na localização do aluno
                        radius: <?php echo $distanciaLimite; ?> * 1000 // raio em metros baseado no limite de distância
                    });
                } else {
                    console.error('Erro ao calcular a distância: ' + (response.rows[0].elements[0].status || status));
                }
            });
        }

        function definirSituacaoVale(distancia) {
            const distanciaLimite = <?php echo $distanciaLimite; ?>;
            const situacao = distancia >= distanciaLimite ? 'Aprovada' : 'Reprovada';
            document.querySelector('#situacao p strong').textContent = situacao;
        }
    </script>
</body>
</html>
