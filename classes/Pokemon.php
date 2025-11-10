<?php
namespace App;

class Pokemon {
    private int $id;
    private string $nom;
    private string $type;
    private int $niveau;
    
    // Liste des types de Pokémon valides
    private static array $typesValides = [
        'Normal', 'Feu', 'Eau', 'Plante', 'Électrik', 'Glace', 'Combat', 'Poison',
        'Sol', 'Vol', 'Psy', 'Insecte', 'Roche', 'Spectre', 'Dragon', 'Ténèbres', 'Acier', 'Fée'
    ];
    
    public function __construct(int $id, string $nom, string $type, int $niveau) {
        $this->id = $id;
        $this->setNom($nom);
        $this->setType($type);
        $this->setNiveau($niveau);
    }
    
    // Getters
    public function getId(): int {
        return $this->id;
    }
    
    public function getNom(): string {
        return $this->nom;
    }
    
    public function getType(): string {
        return $this->type;
    }
    
    public function getNiveau(): int {
        return $this->niveau;
    }
    
    // Setters
    public function setNom(string $nom): void {
        $nom = trim($nom);
        if (empty($nom)) {
            throw new \InvalidArgumentException("Le nom du Pokémon ne peut pas être vide.");
        }
        if (strlen($nom) > 100) {
            throw new \InvalidArgumentException("Le nom du Pokémon ne peut pas dépasser 100 caractères.");
        }
        $this->nom = htmlspecialchars($nom);
    }
    
    public function setType(string $type): void {
        $type = trim($type);
        if (!in_array($type, self::$typesValides)) {
            throw new \InvalidArgumentException("Type de Pokémon non valide.");
        }
        $this->type = $type;
    }
    
    public function setNiveau(int $niveau): void {
        if ($niveau < 1 || $niveau > 100) {
            throw new \InvalidArgumentException("Le niveau doit être entre 1 et 100.");
        }
        $this->niveau = $niveau;
    }
    
    // Méthode utilitaire pour obtenir la liste des types valides
    public static function getTypesValides(): array {
        return self::$typesValides;
    }
}