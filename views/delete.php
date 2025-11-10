<?php
require_once __DIR__ . '/../autoload.php';
use App\Pokemon;
use App\Database;

// Démarrage de la session pour le CSRF
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Génération du token CSRF s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Inclusion de la connexion à la base de données
$pdo = (new Database())->getConnection();

// Vérifier si un ID est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = intval($_GET['id']);

// Récupération du Pokémon à supprimer pour afficher son nom
try {
    $stmt = $pdo->prepare("SELECT * FROM pokemon WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $pokemonData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pokemonData) {
        header('Location: list.php');
        exit;
    }
    
    $pokemon = new Pokemon(
        $pokemonData['id'],
        $pokemonData['nom'],
        $pokemonData['type'],
        $pokemonData['niveau']
    );
} catch (PDOException $e) {
    die("Erreur lors de la récupération du Pokémon : " . $e->getMessage());
}

// Traitement de la suppression
$message = '';
$messageType = '';
$deleted = false;

// Générer ou vérifier le token CSRF
if (empty($_SESSION)) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Token CSRF invalide');
    }
    
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        try {
            $stmt = $pdo->prepare("DELETE FROM pokemon WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $message = "Le Pokémon <strong>" . htmlspecialchars($pokemon->getNom()) . "</strong> a été supprimé avec succès !";
            $messageType = "success";
            $deleted = true;
            
            // Redirection après 2 secondes
            header("refresh:2;url=list.php");
        } catch (PDOException $e) {
            $message = "Erreur lors de la suppression : " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        header('Location: list.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un Pokémon - Pokédex</title>
    <link rel="stylesheet" href="../style.css">
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
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        h1 {
            text-align: center;
            color: #dc3545;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .warning-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        
        .warning-text {
            font-size: 1.2em;
            color: #856404;
            margin-bottom: 10px;
        }
        
        .pokemon-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 20px 0;
            border: 2px solid #e0e0e0;
        }
        
        .pokemon-name {
            font-size: 2em;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        
        .pokemon-type {
            display: inline-block;
            padding: 8px 20px;
            background: #667eea;
            color: white;
            border-radius: 20px;
            font-size: 1em;
            margin: 10px 0;
        }
        
        .pokemon-level {
            font-size: 1.3em;
            font-weight: bold;
            color: #764ba2;
            margin-top: 10px;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: block;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .navigation {
            text-align: center;
            margin-top: 20px;
        }
        
        .nav-link {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        
        .nav-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$deleted): ?>
            <h1>🗑️ Supprimer un Pokémon</h1>
            <p class="subtitle">Êtes-vous sûr de vouloir supprimer ce Pokémon ?</p>
            
            <div class="warning-box">
                <div class="warning-icon">⚠️</div>
                <div class="warning-text">
                    <strong>Attention !</strong><br>
                    Cette action est irréversible.
                </div>
            </div>
            
            <div class="pokemon-card">
                <div class="pokemon-name"><?= htmlspecialchars($pokemon->getNom()) ?></div>
                <div class="pokemon-type"><?= htmlspecialchars($pokemon->getType()) ?></div>
                <div class="pokemon-level">Niveau <?= htmlspecialchars($pokemon->getNiveau()) ?></div>
            </div>
            
            <form method="POST" action="">
                <?php
                if (empty($_SESSION)) {
                    session_start();
                }
                if (empty($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="confirm" value="yes">
                <div class="actions">
                    <button type="submit" class="btn btn-danger">🗑️ Oui, supprimer</button>
                    <a href="list.php" class="btn btn-secondary">❌ Non, annuler</a>
                </div>
            </form>
        <?php else: ?>
            <h1>✅ Suppression réussie</h1>
            
            <?php if (!empty($message)): ?>
                <div class="message <?= $messageType ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            
            <div class="navigation">
                <p>Redirection automatique vers la liste...</p>
                <a href="list.php" class="nav-link">Cliquez ici si vous n'êtes pas redirigé</a>
            </div>
        <?php endif; ?>
        
        <?php if (!$deleted): ?>
            <div class="navigation">
                <a href="list.php" class="nav-link">← Retour à la liste</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>