<?php 
    include 'pokemonDatabase.php';
    
    $isEdited = false;
    $isError = false;
    $isPokeNameEmpty = false;
    $isPokeHPEmpty = false;
    $isPokeNameNum = false;
    $isPokeNameSmall = false;
    $isPokeHPNotNum = false;
    $isPokeHPSmall = false;

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

        if (isset($_POST["submit"])) {
        
            $pokeName = $_POST["pokeName"];
            $pokeHP = $_POST['pokeHP'];
            $pokeSprite = $_FILES['pokeSprite'];
    
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
                $pokeTypes = [];
            
                for ($i = 0;$i < count($pokemonType); $i++) {
                    if (isset($_POST['pokeType'.$pokemonType[$i]['name']])) {
                        $pokeTypes[] = $pokemonType[$i]['name'];
                    }
                }
    
                $json_pokeType = json_encode($pokeTypes);
    
                if(isset($pokeSprite) && $pokeSprite["size"] > 0  ){
                    $from = $pokeSprite["tmp_name"];
                    $pokeSprite = "images/".$pokeSprite["name"];
                    move_uploaded_file($from, $pokeSprite);
                }
                
                $requete = "UPDATE pokemon SET nom=?, hp=?, sprite=?, types=? WHERE id=?";
                $stmt = $bdd->prepare($requete);
                $stmt->execute([$pokeName, $pokeHP, $pokeSprite, $json_pokeType, $pokemonID]);
                $isEdited = true;
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
    <title>Modifier un Pokemon</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <a href="./index.php"><button class="clickBtn">Retourner à l'accueil</button></a>
    <?php if(!$bddError) :?>
        <div class="formWrapper">
            <img src="./assets/International_Pokémon_logo.svg" alt="Logo Pokemon">
            <h2>Modification de <?= $pokemon['nom'] ?></h2>
            <form action="<?=$_SERVER["PHP_SELF"]?>?id=<?= $pokemonID ?>" method="post" enctype="multipart/form-data">
                <input type="text" name="pokeName" placeholder="Nom du pokémon" value="<?= $pokemon['nom'] ?>">
                <input type="file" name="pokeSprite" placeholder="Image du pokémon">
                <h4>Type du pokemon</h4>
                <div class="formCheckboxWrap">
                    <?php $pokeTypes = json_decode($pokemon['types']); ?>
                    <?php for($i = 0;$i < count($pokemonType); $i++) :?>
                        <div>
                            <img src="<?= $pokemonType[$i]['image'] ?>" alt="<?= $pokemonType[$i]['name'] ?>">
                            <input type="checkbox" <?php if (in_array($pokemonType[$i]['name'], $pokeTypes)) echo "checked='checked'" ?> name="pokeType<?= $pokemonType[$i]['name'] ?>">
                        </div>
                    <?php endfor;?>
                </div>
                <input type="number" name="pokeHP" placeholder="PV du pokémon" value="<?= $pokemon['hp'] ?>">
                <button class="clickBtn" type="submit" name="submit">Mettre à jour le pokémon</button>
            </form>
            
            <h4><?= $isEdited ?"Le pokémon a été modifié !":""?></h4>
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
</html