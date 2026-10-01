<?php

declare(strict_types=1);

namespace App\Entity;

use JsonSerializable;

/**
 * Entité Album = une ligne de la table `albums` sous forme d'objet PHP.
 */
class Album implements JsonSerializable
{
    public function __construct(
        private ?int $idAlbums = null,
        private ?string $titre = null,
        private int $artistId = 0,
    ) {}

    // Ligne SQL (tableau) → objet Album
    public static function fromArray(array $row): self
    {
        return new self(
            isset($row['idAlbums']) ? (int) $row['idAlbums'] : null,
            $row['Titre'] ?? null,
            (int) ($row['Artist_idArtist'] ?? 0),
        );
    }

    // Objet Album → tableau (colonnes de la table)
    public function toArray(): array
    {
        return [
            'idAlbums'        => $this->idAlbums,
            'Titre'           => $this->titre,
            'Artist_idArtist' => $this->artistId,
        ];
    }

    // Utilisé automatiquement par json_encode()
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    // Getters / setters
    public function getId(): ?int { return $this->idAlbums; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(?string $titre): void { $this->titre = $titre; }
    public function getArtistId(): int { return $this->artistId; }
    public function setArtistId(int $artistId): void { $this->artistId = $artistId; }
}
