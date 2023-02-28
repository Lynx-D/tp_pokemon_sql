<?php
    include 'pokemonDatabase.php';

    $isDeleted = false;

    if ($bdd) {
        $pokemonID = $_GET['id'];

        if (isset($pokemonID)) {
            if (isset($_POST['submit'])) {
                $requete = "DELETE FROM pokemon WHERE id=?";
                $stmt = $bdd->prepare($requete);
                $stmt->execute([$pokemonID]);
                $isDeleted = true;
            }
            if (isset($_POST['cancel'])) {
                header('Location: index.php');
                exit();
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
    <title>Suppression du pokemon</title>
    <link rel="stylesheet" href="./style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            padding: 15px;
            color: #d3dfe3;
        }

        a {
            margin: 0;
        }
    </style>
</head>
<body>
    <a href="./index.php"><button class="clickBtn">Retourner à l'accueil</button></a>
    <?php if(!$bddError) :?>
        <?php if (!$isDeleted) :?>
            <h3>Êtes vous-sûr de vouloir le supprimer ?</h3>
            <form action="<?=$_SERVER["PHP_SELF"]?>?id=<?= $pokemonID ?>" method="post" enctype="multipart/form-data">
                <button class="clickBtn warning" type="submit" name="submit">Supprimer</button>
                <button class="clickBtn" type="submit" name="cancel">Annuler</button>
            </form>
        <?php else :?>
            <h4><?= $isDeleted ?"Le pokémon a bien été supprimé 😔":""?></h4>
        <?php endif; ?>
    <?php else :?>
        <h2>Erreur lors de la connexion.</h2>
    <?php endif; ?>
</body>
</html>