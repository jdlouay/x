<?php
$dsn = 'mysql:host=localhost;dbname=gestion_livres';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}


if (isset($_POST['ajouter'])) {
    $code = $_POST['code'];
    $libelle = $_POST['libelle'];
    $auteur = $_POST['auteur'];
    $maison_edit = $_POST['maison_edit'];
    $nb_tomes = $_POST['nb_tomes'];
    $reserver = isset($_POST['reserver']) ? 1 : 0;

    $sqlInsert = "INSERT INTO livres (code, libelle, auteur, maison_edit, nb_tomes, reserver)
                  VALUES (:code, :libelle, :auteur, :maison_edit, :nb_tomes, :reserver)";
    $stmtInsert = $pdo->prepare($sqlInsert);

    $stmtInsert->execute([
        ':code' => $code,
        ':libelle' => $libelle,
        ':auteur' => $auteur,
        ':maison_edit' => $maison_edit,
        ':nb_tomes' => $nb_tomes,
        ':reserver' => $reserver,
    ]);
    echo "<script>alert('Livre ajouté avec succès !');</script>";
}


$sqlNonReserves = "SELECT * FROM livres WHERE reserver = 0";
$stmtNonReserves = $pdo->query($sqlNonReserves);
$livresNonReserves = $stmtNonReserves->fetchAll(PDO::FETCH_ASSOC);



if (isset($_POST['rechercher_maison']) && !empty($_POST['maison_edit'])) {
    $maison_edit = htmlspecialchars($_POST['maison_edit'], ENT_QUOTES, 'UTF-8');
    $sqlMaison = "SELECT * FROM livres WHERE maison_edit = :maison_edit";
    $stmtMaison = $pdo->prepare($sqlMaison);
    $stmtMaison->execute([':maison_edit' => $maison_edit]);
    $livresMaison = $stmtMaison->fetchAll(PDO::FETCH_ASSOC);
}



if (isset($_POST['lister_tous'])) {
    $sqlListerTous = "SELECT * FROM livres";
    $stmtListerTous = $pdo->query($sqlListerTous);
    $livresTous = $stmtListerTous->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Livres</title>
</head>

<body>
    <h1>Gestion des Livres</h1>


    <form method="post" action="">
        <h2>Ajouter un Livre</h2>
        <label for="code">Code :</label>
        <input type="text" id="code" name="code" required><br><br>

        <label for="libelle">Libellé :</label>
        <input type="text" id="libelle" name="libelle" required><br><br>

        <label for="auteur">Auteur :</label>
        <input type="text" id="auteur" name="auteur" required><br><br>

        <label for="maison_edit">Maison d'Édition :</label>
        <input type="text" id="maison_edit" name="maison_edit" required><br><br>

        <label for="nb_tomes">Nombre de Tomes :</label>
        <input type="number" id="nb_tomes" name="nb_tomes" required><br><br>

        <label for="reserver">Réservé :</label>
        <input type="checkbox" id="reserver" name="reserver"><br><br>

        <button type="submit" name="ajouter">Ajouter</button>
    </form>


    <form method="post" action="" style="margin-top: 20px;">
        <h2>Rechercher par Maison d'Édition</h2>
        <label for="maison_edit">Maison d'Édition :</label>
        <input type="text" id="maison_edit" name="maison_edit" required>
        <button type="submit" name="rechercher_maison">Rechercher</button>
    </form>


    <form method="post" action="" style="margin-top: 20px;">
        <button type="submit" name="lister_tous">Lister tous les livres</button>
    </form>


    <h2>Livres Non Réservés</h2>
    <table border="1">
        <tr>
            <th>Code</th>
            <th>Libellé</th>
            <th>Auteur</th>
            <th>Maison d'Édition</th>
            <th>Nombre de Tomes</th>
        </tr>
        <?php if (!empty($livresNonReserves)): ?>
            <?php foreach ($livresNonReserves as $livre): ?>
                <tr>
                    <td><?= ($livre['code']) ?></td>
                    <td><?= ($livre['libelle']) ?></td>
                    <td><?= ($livre['auteur']) ?></td>
                    <td><?= ($livre['maison_edit']) ?></td>
                    <td><?= ($livre['nb_tomes']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Aucun livre non réservé trouvé.</td>
            </tr>
        <?php endif; ?>
    </table>


    <?php if (isset($_POST['rechercher_maison'])): ?>
        <h2>Livres de la Maison d'Édition : <?= ($_POST['maison_edit']) ?></h2>
        <table border="1">
            <tr>
                <th>Code</th>
                <th>Libellé</th>
                <th>Auteur</th>
                <th>Maison d'Édition</th>
                <th>Nombre de Tomes</th>
                <th>Réservé</th>
            </tr>
            <?php if (!empty($livresMaison)): ?>
                <?php foreach ($livresMaison as $livre): ?>
                    <tr>
                        <td><?= ($livre['code']) ?></td>
                        <td><?= ($livre['libelle']) ?></td>
                        <td><?= ($livre['auteur']) ?></td>
                        <td><?= ($livre['maison_edit']) ?></td>
                        <td><?= ($livre['nb_tomes']) ?></td>
                        <td><?= $livre['reserver'] ? 'Oui' : 'Non' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Aucun livre trouvé pour cette maison d'édition.</td>
                </tr>
            <?php endif; ?>
        </table>
    <?php endif; ?>


    <?php if (isset($_POST['lister_tous'])): ?>
        <h2>Liste de tous les livres</h2>
        <table border="1">
            <tr>
                <th>Code</th>
                <th>Libellé</th>
                <th>Auteur</th>
                <th>Maison d'Édition</th>
                <th>Nombre de Tomes</th>
                <th>Réservé</th>
            </tr>
            <?php if (!empty($livresTous)): ?>
                <?php foreach ($livresTous as $livre): ?>
                    <tr>
                        <td><?= ($livre['code']) ?></td>
                        <td><?= ($livre['libelle']) ?></td>
                        <td><?= ($livre['auteur']) ?></td>
                        <td><?= ($livre['maison_edit']) ?></td>
                        <td><?= ($livre['nb_tomes']) ?></td>
                        <td><?= $livre['reserver'] ? 'Oui' : 'Non' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Aucun livre trouvé dans la base de données.</td>
                </tr>
            <?php endif; ?>
        </table>
    <?php endif; ?>
</body>

</html>