<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Artist;
use PDO;

class ArtistRepository extends BaseRepository
{
    protected string $table = 'artists';
    protected string $primaryKey = 'idArtist';
    protected array  $columns = ['Name', 'Annee', 'Description'];
    protected ?string $entityClass = Artist::class;   // les méthodes renvoient des objets Artist

    // Méthode spécifique aux artistes (le CRUD est hérité de BaseRepository)
    public function findByYear(int $annee): array
    {
        $sth = $this->db->prepare("SELECT * FROM `artists` WHERE Annee = :annee");
        $sth->execute(['annee' => $annee]);
        return array_map([$this, 'hydrate'], $sth->fetchAll(PDO::FETCH_ASSOC));
    }
}
