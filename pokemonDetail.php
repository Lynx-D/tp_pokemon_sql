<?php 
    include 'pokemonDatabase.php';

    if ($bdd) {
        $pokemonType_json = file_get_contents('./data/pokemonTypes.json');
        $pokemonType = json_decode($pokemonType_json, true);

        $pokemonID = $_GET['id'];

        $pokemons = null;
        try {
            $requete = "SELECT * FROM pokemon WHERE id=:id";
            $stmt = $bdd->prepare($requete);
            $stmt->bindParam(":id",$pokemonID);
            $stmt->execute();
            $pokemons = $stmt->fetchAll();

            $pokemon = $pokemons[0];
        } catch (PDOException $e) {
            
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pokemon['nom'] ?></title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <div class="pokemonDetail">
        <a href="./index.php"><button class="clickBtn">Retourner à l'accueil</button></a>
        <?php if(!$bddError) :?>
            <div class="pokemonCard">
                <h3><?= $pokemon['nom'] ?></h3>
                <img class="pokemonSprite" src="<?= $pokemon['sprite'] ?>">
                <div class="pokemonType">
                    <?php $pokeTypes = json_decode($pokemon['types']); ?>
                    <?php foreach ($pokeTypes as &$pokeType) :?>
                        <img src="<?= $pokemonType[array_search($pokeType, array_column($pokemonType, 'name'))]['image'] ?>">
                    <?php endforeach; ?>
                </div>
                <div class="pokemonStats">
                    <p>PV: <span><?= $pokemon['hp'] ?></span></p>
                </div>
            </div>

            <a href="./editPokemon.php?id=<?= $pokemonID ?>"><button class="clickBtn">Modifier ce pokémon</button></a>
            <a href="./deletePokemon.php?id=<?= $pokemonID ?>"><button class="clickBtn warning">Supprimer ce pokémon</button></a>
        <?php else :?>
            <h2>Erreur lors de la connexion.</h2>
        <?php endif; ?>
    </div>
</body>
</html>