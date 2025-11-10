<?php
// Gestion des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Création du dossier logs s'il n'existe pas
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0777, true);
}

spl_autoload_register(function ($class) {
    try {
        // On attend des classes dans le namespace App
        if (str_starts_with($class, 'App\\')) {
            $className = str_replace('App\\', '', $class);
            $directories = ['classes', 'config'];
            
            foreach ($directories as $dir) {
                $file = __DIR__ . '/' . $dir . '/' . $className . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
            
            throw new \Exception("La classe $class n'a pas été trouvée.");
        }
    } catch (\Exception $e) {
        error_log($e->getMessage());
        header('HTTP/1.1 500 Internal Server Error');
        echo "Une erreur est survenue lors du chargement de l'application.";
        exit;
    }
});


