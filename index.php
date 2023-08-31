<?php
    $data = file_get_contents('includes/equipes.json');
    $data = json_decode($data, true);
    $equipes = $pilotos = [];
    foreach($data['teams'] as $item) {
        
        //pilotos da escuderia
        $numero_pilotos_escuderia = [];
        foreach($item['pilots'] as $subitem) {
            $numero_pilotos_escuderia[] = $subitem['number'];
            $pilotos[] = [
                'Numero' => $subitem['number'],
                'Nome' => $subitem['name'],
                'País' => $subitem['country'],
                'Poder' => $subitem['level'],
                'Escuderia' => $item['name'],
                'Progresso' => $subitem['level'],
                'Status' => 'Parado',
                'Ultrapassagens' => 0,
                'Ultrapassado' => 0,
                'Img' => $subitem['pilot_image_url']
            ];
        }
        
        //escuderia
        $equipes[] = [
            'Escuderia' => ($item['name'] ?? ''),
            'Poder' => ($item['team_level'] ?? ''),
            'Pilotos' => $numero_pilotos_escuderia 
        ];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            padding:0;
            margin:0;
        }

        #area-corrida {
            background-color: #000;
            height:1000px;
        }

        table {
            border:1px solid #fff;
            width:100%;
        }

        table tr td {
            border:1px solid #000;
            text-align:center;
            font-size:14px;
            font-family: Arial;
            color:#fff;
        }

        table tr td img {
            height:50px;
        }

        
    </style>
</head>
<body>
    <div id="area-corrida">
        <h1>Formula - 1 2022 - Grande Prêmio do Brasil - Voltas <span></span></h1>
        <table>
            <thead>
                <tr>
                    <td>Posição</td>
                    <td>Piloto</td>
                    <td>Piloto Rosto</td>
                    <td>Equipe</td>
                    <td>Situação</td>
                    <td>Poder</td>
                    <td>Progresso</td>
                    <td>Qtd. Ultrapassagens</td>
                    <td>Qtd. Ultrapassanges Sofridas</td>
                </tr>
            </thead>
            <tbody>
            </tbody>
            
        </table>
        <button onclick="iniciarCorrida();">Iniciar!</button>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script>
    var pilotos = `<?= json_encode($pilotos)?>`;
    pilotos = JSON.parse(pilotos);
    pilotos = pilotos.reverse();

    var equipes = `<?= json_encode($equipes)?>`;
    equipes = JSON.parse(equipes);

    var voltas = 0;

    function refreshPositions() {
        $('#area-corrida h1 span').html(voltas);
        $('table tbody tr').html(``);

        //Adiciona o progresso de cada um dos pilotos
        pilotos.forEach(function(item, index) {
            let numeroPiloto = item['Numero'];
            let poderEscuderia = 0;
            equipes.forEach(function(v, i){
                (v['Pilotos']).forEach(function(num, j){
                   if(num == numeroPiloto)
                        poderEscuderia = v['Poder'];
                });
            });

            if(voltas == 1)
                pilotos[index]['Status'] = 'Em corrida';
            
            if(pilotos[index]['Status'] == 'Parado' || pilotos[index]['Status'] == 'Em corrida') {
                let motorLigado = ((Math.round(50 * Math.random())) == 0) ? false : true;
                if(motorLigado) 
                    pilotos[index]['Progresso'] += Math.round((pilotos[index]['Poder'] + poderEscuderia) * Math.random());
                else {
                    pilotos[index]['Status'] = 'Quebrou!';
                    pilotos[index]['Progresso'] = 0;
                }
                
            }
        });

        //Ordena os pilotos conforme o seu progresso
        pilotos.forEach(function(item, index){
            if(index > 0) {
                let i = index;
                while(i > 0) {
                    if(item['Progresso'] > pilotos[i-1]['Progresso']) {
                        let aux = pilotos[i-1];
                        pilotos[i-1] = item;
                        pilotos[i] = aux;
                        if(voltas > 0) {
                            pilotos[i]['Ultrapassado']++;
                            pilotos[i-1]['Ultrapassagens']++;
                        }
                    }
                    i--;
                }
            }
        });

        //Printa
        $('table tbody').html(``).hide();
        pilotos.forEach(function(item, index){
           $('table tbody').append(`
                <tr>
                    <td>${(index+1)}º</td>
                    <td>${item['Nome']}</td>
                    <td><img src="${item['Img']}"></td>
                    <td>${item['Escuderia']}</td>
                    <td>${item['Status']}</td>
                    <td>${item['Poder']}</td>
                    <td>${item['Progresso']}</td>
                    <td>${item['Ultrapassagens']}</td>
                    <td>${item['Ultrapassado']}</td>
                </tr>
           `);
        });
        $('table tbody').show(1000);
    }

    function iniciarCorrida() {
        const timer = setInterval(function() {
            voltas++;
            refreshPositions();
            if(voltas == 45) {
                clearTimeout(timer);
            } 
        }, 5000);
    }
    
    $(document).ready(function() {
        refreshPositions();
    });
    
</script>
</html>
