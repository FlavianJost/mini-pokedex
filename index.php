<?php
require_once 'autoload.php';
use App\Pokemon;
use App\Database;

// Inclusion de la connexion à la base de données
$pdo = (new Database)->getConnection();

// Récupération du nombre total de Pokémon
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pokemon");
    $result = $stmt->fetch();
    $totalPokemon = $result['total'];
    
    // Récupération des 3 derniers Pokémon ajoutés
    $stmt = $pdo->query("SELECT * FROM pokemon ORDER BY id DESC LIMIT 3");
    $derniersPokemonsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $derniersPokemon = [];
    foreach ($derniersPokemonsData as $data) {
        $pokemon = new Pokemon(
            $data['id'],
            $data['nom'],
            $data['type'],
            $data['niveau']
        );
        $derniersPokemon[] = $pokemon;
    }
} catch (PDOException $e) {
    $totalPokemon = 0;
    $derniersPokemon = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Pokédex - Accueil</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .hero {
            background: white;
            border-radius: 15px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        
        .hero h1 {
            font-size: 3.5em;
            color: #333;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .hero .emoji {
            font-size: 4em;
            margin-bottom: 20px;
            display: block;
        }
        
        .hero p {
            font-size: 1.3em;
            color: #666;
            margin-bottom: 40px;
        }
        
        .stats {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            border-radius: 50px;
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 18px 35px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1.1em;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .btn:hover {
            background: #5568d3;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        
        .recent-section {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .recent-section h2 {
            text-align: center;
            color: #333;
            font-size: 2em;
            margin-bottom: 30px;
        }
        
        .pokemon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .pokemon-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid #e0e0e0;
        }
        
        .pokemon-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .pokemon-name {
            font-size: 1.6em;
            font-weight: bold;
            color: #333;
            margin-bottom: 12px;
        }
        
        .pokemon-type {
            display: inline-block;
            padding: 8px 20px;
            background: #667eea;
            color: white;
            border-radius: 20px;
            font-size: 1em;
            margin: 10px 0;
            font-weight: bold;
        }
        
        .pokemon-level {
            font-size: 1.3em;
            font-weight: bold;
            color: #764ba2;
            margin-top: 10px;
        }
        
        .no-pokemon {
            text-align: center;
            padding: 50px;
            color: #999;
            font-size: 1.2em;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            transition: all 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
        }
        
        .feature-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        
        .feature-title {
            font-size: 1.4em;
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .feature-description {
            color: #666;
            line-height: 1.6;
        }
        
        .badge-new {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.7em;
            margin-left: 8px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <span class="emoji">🎮</span>
            <h1>Bienvenue dans le Mini Pokédex</h1>
            <p>Gérez votre collection de Pokémon facilement !</p>
            
            <div class="stats">
                📊 <?= $totalPokemon ?> Pokémon <?= $totalPokemon > 1 ? 'enregistrés' : 'enregistré' ?>
            </div>
            
            <div class="actions">
                <a href="views/list.php" class="btn">
                    📋 Voir tous les Pokémon
                </a>
                <a href="views/insert.php" class="btn btn-success">
                    ➕ Ajouter un Pokémon
                </a>
            </div>
        </div>
        
        <!-- Recent Pokemon Section -->
        <?php if (count($derniersPokemon) > 0): ?>
        <div class="recent-section" style="margin-top: 30px;">
            <h2>🌟 Derniers Pokémon ajoutés <span class="badge-new">NOUVEAU</span></h2>
            <div class="pokemon-grid">
                <?php foreach ($derniersPokemon as $pokemon): ?>
                    <div class="pokemon-card">
                        <div class="pokemon-name"><?= htmlspecialchars($pokemon->getNom()) ?></div>
                        <div class="pokemon-type"><?= htmlspecialchars($pokemon->getType()) ?></div>
                        <div class="pokemon-level">Niveau <?= htmlspecialchars($pokemon->getNiveau()) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>