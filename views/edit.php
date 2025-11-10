<?php
namespace App;

use App\Pokemon;
use App\Database;
use PDO;
use PDOException;

// Inclusion de l'autoload
require_once __DIR__ . '/../autoload.php';

// Inclusion de la connexion à la base de données
$pdo = (new Database())->getConnection();

// Vérifier si un ID est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = intval($_GET['id']);

// Récupération du Pokémon à modifier
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

// Traitement du formulaire
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $nom = trim($_POST['nom'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $niveau = intval($_POST['niveau'] ?? 0);
    
    // Validation des données
    if (empty($nom)) {
        $message = "Le nom du Pokémon est obligatoire.";
        $messageType = "error";
    } elseif (empty($type)) {
        $message = "Le type du Pokémon est obligatoire.";
        $messageType = "error";
    } elseif ($niveau < 1 || $niveau > 100) {
        $message = "Le niveau doit être entre 1 et 100.";
        $messageType = "error";
    } else {
        // Mise à jour dans la base de données
        try {
            $stmt = $pdo->prepare("UPDATE pokemon SET nom = :nom, type = :type, niveau = :niveau WHERE id = :id");
            $stmt->execute([
                ':nom' => $nom,
                ':type' => $type,
                ':niveau' => $niveau,
                ':id' => $id
            ]);
            
            $message = "Le Pokémon <strong>$nom</strong> a été modifié avec succès !";
            $messageType = "success";
            
            // Mettre à jour l'objet Pokemon
            $pokemon->setNom($nom);
            $pokemon->setType($type);
            $pokemon->setNiveau($niveau);
            
            // Redirection après 2 secondes
            header("refresh:2;url=list.php");
        } catch (PDOException $e) {
            $message = "Erreur lors de la modification : " . $e->getMessage();
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Pokémon - Pokédex</title>
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
            color: #333;
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
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
            font-size: 1.1em;
        }
        
        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #ffc107;
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
        
        .btn-primary {
            background: #ffc107;
            color: #000;
        }
        
        .btn-primary:hover {
            background: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .required {
            color: #dc3545;
        }
        
        .help-text {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
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
        
        .pokemon-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .pokemon-info strong {
            color: #667eea;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Modifier un Pokémon</h1>
        <p class="subtitle">Modification du Pokémon #<?= $pokemon->getId() ?></p>
        
        <?php if (!empty($message)): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="nom">
                    Nom <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="nom" 
                    name="nom" 
                    placeholder="Ex: Pikachu" 
                    required
                    maxlength="100"
                    value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : htmlspecialchars($pokemon->getNom()) ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="type">
                    Type <span class="required">*</span>
                </label>
                <?php 
                $currentType = isset($_POST['type']) ? $_POST['type'] : $pokemon->getType();
                ?>
                <select id="type" name="type" required>
                    <option value="">-- Sélectionnez un type --</option>
                    <option value="Normal" <?= $currentType === 'Normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="Feu" <?= $currentType === 'Feu' ? 'selected' : '' ?>>Feu</option>
                    <option value="Eau" <?= $currentType === 'Eau' ? 'selected' : '' ?>>Eau</option>
                    <option value="Plante" <?= $currentType === 'Plante' ? 'selected' : '' ?>>Plante</option>
                    <option value="Électrik" <?= $currentType === 'Électrik' ? 'selected' : '' ?>>Électrik</option>
                    <option value="Glace" <?= $currentType === 'Glace' ? 'selected' : '' ?>>Glace</option>
                    <option value="Combat" <?= $currentType === 'Combat' ? 'selected' : '' ?>>Combat</option>
                    <option value="Poison" <?= $currentType === 'Poison' ? 'selected' : '' ?>>Poison</option>
                    <option value="Sol" <?= $currentType === 'Sol' ? 'selected' : '' ?>>Sol</option>
                    <option value="Vol" <?= $currentType === 'Vol' ? 'selected' : '' ?>>Vol</option>
                    <option value="Psy" <?= $currentType === 'Psy' ? 'selected' : '' ?>>Psy</option>
                    <option value="Insecte" <?= $currentType === 'Insecte' ? 'selected' : '' ?>>Insecte</option>
                    <option value="Roche" <?= $currentType === 'Roche' ? 'selected' : '' ?>>Roche</option>
                    <option value="Spectre" <?= $currentType === 'Spectre' ? 'selected' : '' ?>>Spectre</option>
                    <option value="Dragon" <?= $currentType === 'Dragon' ? 'selected' : '' ?>>Dragon</option>
                    <option value="Ténèbres" <?= $currentType === 'Ténèbres' ? 'selected' : '' ?>>Ténèbres</option>
                    <option value="Acier" <?= $currentType === 'Acier' ? 'selected' : '' ?>>Acier</option>
                    <option value="Fée" <?= $currentType === 'Fée' ? 'selected' : '' ?>>Fée</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="niveau">
                    Niveau <span class="required">*</span>
                </label>
                <input 
                    type="number" 
                    id="niveau" 
                    name="niveau" 
                    placeholder="Ex: 25" 
                    required
                    min="1"
                    max="100"
                    value="<?= isset($_POST['niveau']) ? htmlspecialchars($_POST['niveau']) : htmlspecialchars($pokemon->getNiveau()) ?>"
                >
                <div class="help-text">Entre 1 et 100</div>
            </div>
            
            <div class="actions">
                <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
                <a href="list.php" class="btn btn-secondary">❌ Annuler</a>
            </div>
        </form>
        
        <div class="navigation">
            <a href="list.php" class="nav-link">← Retour à la liste</a>
        </div>
    </div>
</body>
</html>