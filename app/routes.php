<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

// for token generation and validation
use App\Middleware\JwtHelper;
use App\Middleware\JwtMiddleware;

// for database access
use App\Repository\ArtistRepository;
use App\Repository\AlbumRepository;
use App\Repository\RatingRepository;



return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });


    // Public route: login → returns a token
    $app->post('/login', function (Request $request, Response $response) {
        $params = (array) $request->getParsedBody();
        $username = $params['username'] ?? '';
        $password = $params['password'] ?? '';

        $expectedUser = $_ENV['API_USERNAME'] ?? '';
        $expectedPass = $_ENV['API_PASSWORD'] ?? '';

        if ($expectedUser !== '' && hash_equals($expectedUser, $username) && hash_equals($expectedPass, $password)) {
            $token = JwtHelper::generateToken(['id' => 1, 'username' => $username]);
            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    });

    // Protected route
    $app->get('/protected', function (Request $request, Response $response) {
        $user = $request->getAttribute('user');
        $response->getBody()->write(json_encode(['message' => 'Hello, ' . $user->username]));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new JwtMiddleware());


        // Test BDD : liste des artistes
    $app->get('/GetAllArtist', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);
        $sth = $db->prepare("SELECT * FROM `artists`");
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // ===== API Artistes (Repository) — protégée par JWT =====
    $app->group('/api', function (Group $group) {

        // GET /api/artists → liste
        $group->get('/artists', function (Request $request, Response $response) {
            $repo = $this->get(ArtistRepository::class);   // PHP-DI injecte PDO tout seul
            $response->getBody()->write(json_encode($repo->findAll()));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // GET /api/artists/year/{annee} → méthode spécifique
        $group->get('/artists/year/{annee}', function (Request $request, Response $response, array $args) {
            $artists = $this->get(ArtistRepository::class)->findByYear((int) $args['annee']);
            $response->getBody()->write(json_encode($artists));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // GET /api/artists/{id} → un artiste (404 si introuvable)
        $group->get('/artists/{id}', function (Request $request, Response $response, array $args) {
            $artist = $this->get(ArtistRepository::class)->findById((int) $args['id']);
            $response->getBody()->write(json_encode($artist ?? ['error' => 'Artiste introuvable']));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($artist ? 200 : 404);
        });

        // POST /api/artists → créer (201)
        $group->post('/artists', function (Request $request, Response $response) {
            $data = (array) $request->getParsedBody();
            if (empty($data['Name'])) {
                $response->getBody()->write(json_encode(['error' => 'Le champ Name est obligatoire']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $id = $this->get(ArtistRepository::class)->insert($data);
            $response->getBody()->write(json_encode(['idArtist' => $id]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        });

        // PUT /api/artists/{id} → modifier
        $group->put('/artists/{id}', function (Request $request, Response $response, array $args) {
            $repo = $this->get(ArtistRepository::class);
            $id = (int) $args['id'];
            if (!$repo->findById($id)) {
                $response->getBody()->write(json_encode(['error' => 'Artiste introuvable']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            $repo->update($id, (array) $request->getParsedBody());
            $response->getBody()->write(json_encode($repo->findById($id)));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // DELETE /api/artists/{id} → supprimer (204, ou 404)
        $group->delete('/artists/{id}', function (Request $request, Response $response, array $args) {
            $deleted = $this->get(ArtistRepository::class)->delete((int) $args['id']);
            if (!$deleted) {
                $response->getBody()->write(json_encode(['error' => 'Artiste introuvable']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            return $response->withStatus(204);
        });

        // GET /api/artists/{id}/albums → albums d'un artiste
        $group->get('/artists/{id}/albums', function (Request $request, Response $response, array $args) {
            $albums = $this->get(AlbumRepository::class)->findByArtist((int) $args['id']);
            $response->getBody()->write(json_encode($albums));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // ===== Albums =====

        // GET /api/albums → liste
        $group->get('/albums', function (Request $request, Response $response) {
            $response->getBody()->write(json_encode($this->get(AlbumRepository::class)->findAll()));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // GET /api/albums/{id} → un album (404 si introuvable)
        $group->get('/albums/{id}', function (Request $request, Response $response, array $args) {
            $album = $this->get(AlbumRepository::class)->findById((int) $args['id']);
            $response->getBody()->write(json_encode($album ?? ['error' => 'Album introuvable']));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($album ? 200 : 404);
        });

        // GET /api/albums/{id}/ratings → notes d'un album
        $group->get('/albums/{id}/ratings', function (Request $request, Response $response, array $args) {
            $ratings = $this->get(RatingRepository::class)->findByAlbum((int) $args['id']);
            $response->getBody()->write(json_encode($ratings));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // POST /api/albums → créer (201)
        $group->post('/albums', function (Request $request, Response $response) {
            $data = (array) $request->getParsedBody();
            if (empty($data['Titre']) || empty($data['Artist_idArtist'])) {
                $response->getBody()->write(json_encode(['error' => 'Les champs Titre et Artist_idArtist sont obligatoires']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            if (!$this->get(ArtistRepository::class)->findById((int) $data['Artist_idArtist'])) {
                $response->getBody()->write(json_encode(['error' => 'Artiste introuvable']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $id = $this->get(AlbumRepository::class)->insert($data);
            $response->getBody()->write(json_encode(['idAlbums' => $id]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        });

        // PUT /api/albums/{id} → modifier
        $group->put('/albums/{id}', function (Request $request, Response $response, array $args) {
            $repo = $this->get(AlbumRepository::class);
            $id = (int) $args['id'];
            if (!$repo->findById($id)) {
                $response->getBody()->write(json_encode(['error' => 'Album introuvable']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            $data = (array) $request->getParsedBody();
            if (isset($data['Artist_idArtist']) && !$this->get(ArtistRepository::class)->findById((int) $data['Artist_idArtist'])) {
                $response->getBody()->write(json_encode(['error' => 'Artiste introuvable']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $repo->update($id, $data);
            $response->getBody()->write(json_encode($repo->findById($id)));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // DELETE /api/albums/{id} → supprimer (204, ou 404)
        $group->delete('/albums/{id}', function (Request $request, Response $response, array $args) {
            $deleted = $this->get(AlbumRepository::class)->delete((int) $args['id']);
            if (!$deleted) {
                $response->getBody()->write(json_encode(['error' => 'Album introuvable']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            return $response->withStatus(204);
        });

        // ===== Ratings =====

        // GET /api/ratings → liste
        $group->get('/ratings', function (Request $request, Response $response) {
            $response->getBody()->write(json_encode($this->get(RatingRepository::class)->findAll()));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // GET /api/ratings/{id} → une note (404 si introuvable)
        $group->get('/ratings/{id}', function (Request $request, Response $response, array $args) {
            $rating = $this->get(RatingRepository::class)->findById((int) $args['id']);
            $response->getBody()->write(json_encode($rating ?? ['error' => 'Note introuvable']));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($rating ? 200 : 404);
        });

        // POST /api/ratings → créer (201)
        $group->post('/ratings', function (Request $request, Response $response) {
            $data = (array) $request->getParsedBody();
            if (empty($data['Grade']) || empty($data['Albums_idAlbums'])) {
                $response->getBody()->write(json_encode(['error' => 'Les champs Grade et Albums_idAlbums sont obligatoires']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            if (!$this->get(AlbumRepository::class)->findById((int) $data['Albums_idAlbums'])) {
                $response->getBody()->write(json_encode(['error' => 'Album introuvable']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $id = $this->get(RatingRepository::class)->insert($data);
            $response->getBody()->write(json_encode(['idRatings' => $id]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        });

        // PUT /api/ratings/{id} → modifier
        $group->put('/ratings/{id}', function (Request $request, Response $response, array $args) {
            $repo = $this->get(RatingRepository::class);
            $id = (int) $args['id'];
            if (!$repo->findById($id)) {
                $response->getBody()->write(json_encode(['error' => 'Note introuvable']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            $data = (array) $request->getParsedBody();
            if (isset($data['Albums_idAlbums']) && !$this->get(AlbumRepository::class)->findById((int) $data['Albums_idAlbums'])) {
                $response->getBody()->write(json_encode(['error' => 'Album introuvable']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            $repo->update($id, $data);
            $response->getBody()->write(json_encode($repo->findById($id)));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // DELETE /api/ratings/{id} → supprimer (204, ou 404)
        $group->delete('/ratings/{id}', function (Request $request, Response $response, array $args) {
            $deleted = $this->get(RatingRepository::class)->delete((int) $args['id']);
            if (!$deleted) {
                $response->getBody()->write(json_encode(['error' => 'Note introuvable']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            return $response->withStatus(204);
        });

    })->add(new JwtMiddleware());
};
