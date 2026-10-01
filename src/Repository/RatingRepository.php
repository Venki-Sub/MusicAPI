<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rating;
use PDO;

class RatingRepository extends BaseRepository
{
    protected string $table = 'ratings';
    protected string $primaryKey = 'idRatings';
    protected array  $columns = ['Grade', 'Albums_idAlbums'];
    protected ?string $entityClass = Rating::class;   // les méthodes renvoient des objets Rating

    // Méthode spécifique aux notes (le CRUD est hérité de BaseRepository)
    public function findByAlbum(int $albumId): array
    {
        $sth = $this->db->prepare("SELECT * FROM `ratings` WHERE Albums_idAlbums = :albumId");
        $sth->execute(['albumId' => $albumId]);
        return array_map([$this, 'hydrate'], $sth->fetchAll(PDO::FETCH_ASSOC));
    }
}
