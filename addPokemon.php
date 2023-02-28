<?php 
    $isCreated = false;
    $isError = false;
    $isPokeNameEmpty = false;
    $isPokeHPEmpty = false;
    $isPokeNameNum = false;
    $isPokeNameSmall = false;
    $isPokeHPNotNum = false;
    $isPokeHPSmall = false;

    include 'pokemonDatabase.php';
    
    if ($bdd) {
        $pokemonType_json = file_get_contents('./data/pokemonTypes.json');
        $pokemonType = json_decode($pokemonType_json, true);

        if (isset($_POST["submit"])) {
            $pokeName = $_POST["pokeName"];
            $pokeHP = $_POST['pokeHP'];
            $pokeSprite = $_FILES['pokeSprite'];
    
            if(isset($pokeSprite) && isset($pokeName) && isset($pokeHP)){
                // Vérifier si le nom n'est pas vide
                if (empty($pokeName)) {
                    $isPokeNameEmpty = true;
                    $isError = true;
                }
                // Vérifier si les pv ne sont pas vide
                if (empty($pokeHP)) {
                    $isPokeHPEmpty = true;
                    $isError = true;
                }
                // Vérification de si le nom est un nombre
                if (is_numeric($pokeName)) {
                    $isPokeNameNum = true;
                    $isError = true;
                }
                // Vérification de la longueur du nom
                if (strlen($pokeName)<=2) {
                    $isPokeNameSmall = true;
                    $isError = true;
                }
                // Vérification de si les hps sont un nombre
                if (!is_numeric($pokeHP)) {
                    $isPokeHPNotNum = true;
                    $isError = true;
                }
                // Vérification du nombre
                if ($pokeHP<=0) {
                    $isPokeHPSmall = true;
                    $isError = true;
                }
    
                if (!$isError) {
                    // Ajouter les types
                    $pokeTypes = [];
            
                    for ($i = 0;$i < count($pokemonType); $i++) {
                        if (isset($_POST['pokeType'.$pokemonType[$i]['name']])) {
                            $pokeTypes[] = $pokemonType[$i]['name'];
                        }
                    }

                    $json_pokeType = json_encode($pokeTypes);
                    
                    $from = $pokeSprite["tmp_name"];
                    $to = "images/".$pokeSprite["name"];
                    move_uploaded_file($from,$to);

                    $requete = "INSERT INTO pokemon (nom, hp, sprite, types) VALUES (?,?,?,?)";
                    $stmt= $bdd->prepare($requete);
                    $stmt->execute([$pokeName, $pokeHP, $to, $json_pokeType]);
                    $isCreated = true;
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Pokemon</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <a href="./index.php"><button class="clickBtn">Retourner à l'accueil</button></a>
    <?php if(!$bddError) :?>
        <div class="formWrapper">
            <a href="./index.php"><img src="./assets/International_Pokémon_logo.svg" alt="Logo Pokemon"></a>
            <h2>Ajout d'un Pokemon</h2>
            <form action="<?=$_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data">
                <input type="text" name="pokeName" placeholder="Nom du pokémon">
                <input type="file" name="pokeSprite" placeholder="Image du pokémon">
                <h4>Type du pokemon</h4>
                <div class="formCheckboxWrap">
                    <?php for($i = 0;$i < count($pokemonType); $i++) :?>
                        <div>
                            <img src="<?= $pokemonType[$i]['image'] ?>" alt="<?= $pokemonType[$i]['name'] ?>">
                            <input type="checkbox" name="pokeType<?= $pokemonType[$i]['name'] ?>">
                        </div>
                    <?php endfor;?>
                </div>
                <input type="number" name="pokeHP" placeholder="PV du pokémon" min="0" step="10">
                <button class="clickBtn" type="submit" name="submit">Ajouter le pokemon</button>
            </form>

            <h4><?= $isCreated ?"Le pokémon a été crée !":""?></h4>
            <h4 class="error"><?= $isPokeNameEmpty ?"Le nom du pokémon doit être renseigné.":""?></h4>
            <h4 class="error"><?= $isPokeHPEmpty ?"Les pv du pokémon doivent être renseigné.":""?></h4>
            <h4 class="error"><?= $isPokeNameNum ?"Le nom du pokémon ne doit pas être un nombre.":""?></h4>
            <h4 class="error"><?= $isPokeNameSmall ?"Le nom du pokémon doit être de 2 caractères minimum.":""?></h4>
            <h4 class="error"><?= $isPokeHPNotNum ?"Les points de vie du pokémon doivent être un nombre.":""?></h4>
            <h4 class="error"><?= $isPokeHPSmall ?"Les points de vie du pokémon doivent être supérieur à 0.":""?></h4>
        </div>
    <?php else :?>
        <h2>Erreur lors de la connexion.</h2>
    <?php endif; ?>
</body>
</html>