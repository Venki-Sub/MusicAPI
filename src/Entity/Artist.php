<?php

declare(strict_types=1);

namespace App\Entity;

use JsonSerializable;

/**
 * Entité Artist = une ligne de la table `artists` sous forme d'objet PHP.
 */
class Artist implements JsonSerializable // JsonSerializable = objet qui peut être converti en JSON (json_encode()) 
{
    public function __construct(
        private ?int $idArtist = null,
        private string $name = '',
        private ?int $annee = null,
        private ?string $description = null,
    ) {}

    // Ligne SQL (tableau) → objet Artist
    public static function fromArray(array $row): self
    {
        return new self(
            isset($row['idArtist']) ? (int) $row['idArtist'] : null, //isset() pour éviter "Undefined index" si la colonne n'est pas sélectionnée 
            isset($row['Name']) ? (string) $row['Name'] : '',
            isset($row['Annee']) ? (int) $row['Annee'] : null,
            $row['Description'] ?? null,
        );
    }

    // Objet Artist → tableau (colonnes de la table)
    public function toArray(): array
    {
        return [
            'idArtist'    => $this->idArtist,
            'Name'        => $this->name,
            'Annee'       => $this->annee,
            'Description' => $this->description,
        ];
    }

    // Utilisé automatiquement par json_encode()
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    // Getters / setters
    public function getId(): ?int { return $this->idArtist; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getAnnee(): ?int { return $this->annee; }
    public function setAnnee(?int $annee): void { $this->annee = $annee; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }
}
