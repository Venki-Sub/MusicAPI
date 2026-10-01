<?php

declare(strict_types=1);

namespace App\Entity;

use JsonSerializable;

/**
 * Entité Rating = une ligne de la table `ratings` sous forme d'objet PHP.
 */
class Rating implements JsonSerializable
{
    public function __construct(
        private ?int $idRatings = null,
        private ?string $grade = null,
        private int $albumId = 0,
    ) {}

    // Ligne SQL (tableau) → objet Rating
    public static function fromArray(array $row): self
    {
        return new self(
            isset($row['idRatings']) ? (int) $row['idRatings'] : null,
            $row['Grade'] ?? null,
            (int) ($row['Albums_idAlbums'] ?? 0),
        );
    }

    // Objet Rating → tableau (colonnes de la table)
    public function toArray(): array
    {
        return [
            'idRatings'       => $this->idRatings,
            'Grade'           => $this->grade,
            'Albums_idAlbums' => $this->albumId,
        ];
    }

    // Utilisé automatiquement par json_encode()
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    // Getters / setters
    public function getId(): ?int { return $this->idRatings; }
    public function getGrade(): ?string { return $this->grade; }
    public function setGrade(?string $grade): void { $this->grade = $grade; }
    public function getAlbumId(): int { return $this->albumId; }
    public function setAlbumId(int $albumId): void { $this->albumId = $albumId; }
}
