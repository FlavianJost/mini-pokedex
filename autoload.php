<?php
/**
 * Autoload automatique des classes
 * Ce fichier charge automatiquement les classes depuis le dossier /classes
*/

spl_autoload_register(function ($class) {
    // On attend des classes dans le namespace App
    if (str_starts_with($class, 'App\\')) {
        $className = str_replace('App\\', '', $class);

        // On cherche dans /classes
        $file = __DIR__ . '/classes/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }

        // On cherche aussi dans /config
        $file = __DIR__ . '/config/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }

        // Si rien trouvé
        die("Erreur : La classe $class n'a pas été trouvée.");
    }
});


