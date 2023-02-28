<?php 
    include 'pokemonDatabase.php';

    if ($bdd) {
        $pokemonType_json = file_get_contents('./data/pokemonTypes.json');
        $pokemonType = json_decode($pokemonType_json, true);

        $pokemons = null;
        try {
            $stmt = $bdd->query("SELECT * FROM pokemon", PDO::FETCH_ASSOC);
            $pokemons = $stmt->fetchAll();
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
    <title>Mes pokémons</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <div class="pokemonHome">
        <div class="pokemonHeader">
            <h1>Mes pokémons</h1>
            <div>
                <a href="./addPokemon.php"><button class="clickBtn">Ajouter un pokemon</button></a>
            </div>
        </div>
        <?php if(!$bddError) :?>
            <div class="pokemonCardWrapper">
                <?php foreach ($pokemons as &$pokemon) :?>
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
                        <a href="pokemonDetail.php?id=<?= $pokemon['id'] ?>"><button class="clickBtn">Voir en détail</button></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else :?>
            <h2>Erreur lors de la connexion.</h2>
        <?php endif; ?>
    </div>
</body>
</html>