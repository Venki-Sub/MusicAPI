<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Album;
use PDO;

class AlbumRepository extends BaseRepository
{
    protected string $table = 'albums';
    protected string $primaryKey = 'idAlbums';
    protected array  $columns = ['Titre', 'Artist_idArtist'];
    protected ?string $entityClass = Album::class;   // les méthodes renvoient des objets Album

    // Méthode spécifique aux albums (le CRUD est hérité de BaseRepository)
    public function findByArtist(int $artistId): array
    {
        $sth = $this->db->prepare("SELECT * FROM `albums` WHERE Artist_idArtist = :artistId");
        $sth->execute(['artistId' => $artistId]);
        return array_map([$this, 'hydrate'], $sth->fetchAll(PDO::FETCH_ASSOC));
    }
}
