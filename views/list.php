<?php
require_once '../autoload.php';
use App\Pokemon;
use App\Database;
/**
 * Page de liste des Pokémon
 * Affiche tous les Pokémon de la base de données dans un tableau
 * Ce fichier nécessite que $pdo soit déjà défini
 */

// Récupération de tous les Pokémon depuis la base de données
try {
    $pdo = (new Database())->getConnection();
    $stmt = $pdo->query("SELECT * FROM pokemon ORDER BY id ASC");
    $pokemonsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Conversion des données en objets Pokemon
    $pokemons = [];
    foreach ($pokemonsData as $data) {
        $pokemon = new Pokemon(
            $data['id'],
            $data['nom'],
            $data['type'],
            $data['niveau']
        );
        $pokemons[] = $pokemon;
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération des Pokémon : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Pokémon - Pokédex</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        
        .actions {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s;
            margin: 0 10px;
        }
        
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-home {
            background: #6c757d;
        }
        
        .btn-home:hover {
            background: #5a6268;
        }
        
        .count {
            text-align: center;
            margin: 20px 0;
            font-size: 1.1em;
            color: #666;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: bold;
            font-size: 1.1em;
        }
        
        th:first-child {
            border-top-left-radius: 8px;
        }
        
        th:last-child {
            border-top-right-radius: 8px;
        }
        
        tbody tr {
            transition: background 0.3s;
        }
        
        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        tbody tr:hover {
            background: #e3f2fd;
            transform: scale(1.01);
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .pokemon-id {
            font-weight: bold;
            color: #764ba2;
            font-size: 1.1em;
        }
        
        .pokemon-nom {
            font-weight: bold;
            color: #333;
            font-size: 1.1em;
        }
        
        .pokemon-type {
            display: inline-block;
            padding: 5px 15px;
            background: #667eea;
            color: white;
            border-radius: 20px;
            font-size: 0.9em;
        }
        
        .pokemon-niveau {
            font-weight: bold;
            color: #28a745;
            font-size: 1.1em;
        }
        
        .no-pokemon {
            text-align: center;
            padding: 50px;
            color: #999;
            font-size: 1.2em;
        }
        
        .no-pokemon p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Liste des Pokémon</h1>
        <p class="subtitle">Tous les Pokémon de votre Pokédex</p>
        
        <div class="actions">
            <a href="../index.php" class="btn btn-home">🏠 Accueil</a>
            <a href="insert.php" class="btn btn-success">➕ Ajouter un Pokémon</a>
        </div>
        
        <?php if (count($pokemons) > 0): ?>
            <div class="count">
                Total : <?= count($pokemons) ?> Pokémon enregistré<?= count($pokemons) > 1 ? 's' : '' ?>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Niveau</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pokemons as $pokemon): ?>
                        <tr>
                            <td class="pokemon-id">#<?= htmlspecialchars($pokemon->getId()) ?></td>
                            <td class="pokemon-nom"><?= htmlspecialchars($pokemon->getNom()) ?></td>
                            <td>
                                <span class="pokemon-type">
                                    <?= htmlspecialchars($pokemon->getType()) ?>
                                </span>
                            </td>
                            <td class="pokemon-niveau">Niv. <?= htmlspecialchars($pokemon->getNiveau()) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $pokemon->getId() ?>" class="btn btn-warning">✏️ Modifier</a>
                                <a href="delete.php?id=<?= $pokemon->getId() ?>" class="btn btn-danger" onclick="return confirm('Supprimer ce Pokémon ?')">🗑️ Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-pokemon">
                <p>😢 Aucun Pokémon dans votre Pokédex pour le moment.</p>
                <p>Commencez par en ajouter un !</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>