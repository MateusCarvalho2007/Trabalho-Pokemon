<?php
session_start();
if(!isset($_SESSION['idTreinador'])){
    header("location:loginForm.php");
}

require_once __DIR__."/classes/pokemon.php";
$Pokemons = pokemon::findNaoTrocavelPorUsuario($_SESSION['idTreinador']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémons Para Troca</title>
</head>
<body>
     <div class="container">
        <table>
            <br>
            <tr><h1>Meus Pokemons</h1></tr>
            <br>
            <?php
            foreach ($Pokemons as $Pokemon) {
                echo "<tr>";
                echo "<td>" . $Pokemon->getnome() . "</td>";
                echo "<td>
                        <form action='adicionarTrocavel.php' method='post'>
                            <input type='hidden' name='idLPokemon' value='" . $Pokemon->getidPokemon() . "'>
                            <button type='submit'>AddTrocavel</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            ?>

        </table>
<a href='formCadPokemon.php'>Cadastrar Pokémon</a>    
<a href='NaoQueroTrocar.php'>Não quero Trocar</a>      
<a href='sair.php'>Sair</a>  
</body>
</html>