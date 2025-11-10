<?php
require_once __DIR__ . '/../autoload.php';
use App\Database;
use App\Pokemon;

// Inclusion de la connexion à la base de données
$pdo = (new Database())->getConnection();

// Démarrer la session au début du fichier
session_start();

// Variables pour les messages
$message = '';
$messageType = '';

// Générer token CSRF s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = "Erreur de sécurité. Veuillez réessayer.";
        $messageType = "error";
    } else {
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
        } else{

            // Insertion dans la base de données
            try {
                $tempPokemon = new Pokemon(0, $nom, $type, $niveau);
                $stmt = $pdo->prepare("INSERT INTO pokemon (nom, type, niveau) VALUES (:nom, :type, :niveau)");
                $stmt->execute([
                    ':nom' => $nom,
                    ':type' => $type,
                    ':niveau' => $niveau
                ]);
                
                $message = "Le Pokémon <strong>$nom</strong> a été ajouté avec succès !";
                $messageType = "success";

                // Régénérer le token CSRF
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                // Redirection après 2 secondes
                header("refresh:2;url=list.php");
            } catch (\InvalidArgumentException $e) {
                $message = htmlspecialchars($e->getMessage());
                $messageType = "error";
            }catch (PDOException $e) {
                $message = "Erreur lors de l'ajout : " . $e->getMessage();
                $messageType = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Pokémon - Pokédex</title>
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
            border-color: #667eea;
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
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Ajouter un Pokémon</h1>
        <p class="subtitle">Ajoutez un nouveau Pokémon à votre Pokédex</p>
        
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
                    value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="type">
                    Type <span class="required">*</span>
                </label>
                <select id="type" name="type" required>
                    <option value="">-- Sélectionnez un type --</option>
                    <option value="Normal" <?= (isset($_POST['type']) && $_POST['type'] === 'Normal') ? 'selected' : '' ?>>Normal</option>
                    <option value="Feu" <?= (isset($_POST['type']) && $_POST['type'] === 'Feu') ? 'selected' : '' ?>>Feu</option>
                    <option value="Eau" <?= (isset($_POST['type']) && $_POST['type'] === 'Eau') ? 'selected' : '' ?>>Eau</option>
                    <option value="Plante" <?= (isset($_POST['type']) && $_POST['type'] === 'Plante') ? 'selected' : '' ?>>Plante</option>
                    <option value="Électrik" <?= (isset($_POST['type']) && $_POST['type'] === 'Électrik') ? 'selected' : '' ?>>Électrik</option>
                    <option value="Glace" <?= (isset($_POST['type']) && $_POST['type'] === 'Glace') ? 'selected' : '' ?>>Glace</option>
                    <option value="Combat" <?= (isset($_POST['type']) && $_POST['type'] === 'Combat') ? 'selected' : '' ?>>Combat</option>
                    <option value="Poison" <?= (isset($_POST['type']) && $_POST['type'] === 'Poison') ? 'selected' : '' ?>>Poison</option>
                    <option value="Sol" <?= (isset($_POST['type']) && $_POST['type'] === 'Sol') ? 'selected' : '' ?>>Sol</option>
                    <option value="Vol" <?= (isset($_POST['type']) && $_POST['type'] === 'Vol') ? 'selected' : '' ?>>Vol</option>
                    <option value="Psy" <?= (isset($_POST['type']) && $_POST['type'] === 'Psy') ? 'selected' : '' ?>>Psy</option>
                    <option value="Insecte" <?= (isset($_POST['type']) && $_POST['type'] === 'Insecte') ? 'selected' : '' ?>>Insecte</option>
                    <option value="Roche" <?= (isset($_POST['type']) && $_POST['type'] === 'Roche') ? 'selected' : '' ?>>Roche</option>
                    <option value="Spectre" <?= (isset($_POST['type']) && $_POST['type'] === 'Spectre') ? 'selected' : '' ?>>Spectre</option>
                    <option value="Dragon" <?= (isset($_POST['type']) && $_POST['type'] === 'Dragon') ? 'selected' : '' ?>>Dragon</option>
                    <option value="Ténèbres" <?= (isset($_POST['type']) && $_POST['type'] === 'Ténèbres') ? 'selected' : '' ?>>Ténèbres</option>
                    <option value="Acier" <?= (isset($_POST['type']) && $_POST['type'] === 'Acier') ? 'selected' : '' ?>>Acier</option>
                    <option value="Fée" <?= (isset($_POST['type']) && $_POST['type'] === 'Fée') ? 'selected' : '' ?>>Fée</option>
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
                    value="<?= isset($_POST['niveau']) ? htmlspecialchars($_POST['niveau']) : '' ?>"
                >
                <div class="help-text">Entre 1 et 100</div>
            </div>
            
            <div class="actions">
                <button type="submit" class="btn btn-primary">✅ Ajouter le Pokémon</button>
                <a href="list.php" class="btn btn-secondary">❌ Annuler</a>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            </div>
        </form>
        
        <div class="navigation">
            <a href="../index.php" class="nav-link">🏠 Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>