<?php
namespace App;

class Pokemon {
    private int $id;
    private string $nom;
    private string $type;
    private int $niveau;
    
    public function __construct(int $id, string $nom, string $type, int $niveau) {
        $this->id = $id;
        $this->nom = $nom;
        $this->type = $type;
        $this->niveau = $niveau;
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
        $this->nom = $nom;
    }
    
    public function setType(string $type): void {
        $this->type = $type;
    }
    
    public function setNiveau(int $niveau): void {
        $this->niveau = $niveau;
    }
}