<?php

declare(strict_types=1);

namespace Service;

use Core\Env;
use MongoDB\Client;
use MongoDB\Collection;

class MongoService
{
    private Collection $collection;

    public function __construct()
    {
        $host     = Env::get('MONGO_HOST',     'mongo');
        $port     = Env::get('MONGO_PORT',     '27017');
        $user     = Env::get('MONGO_ROOT_USER',     'root');
        $password = Env::get('MONGO_ROOT_PASSWORD', 'rootpassword');
        $database = Env::get('MONGO_DATABASE', 'viteGourmandStats');

        $uri = "mongodb://{$user}:{$password}@{$host}:{$port}";

        $client           = new Client($uri);
        $this->collection = $client->$database->commandes_stats;
    }

    public function enregistrerCommande(
        int    $commandeId,
        int    $menuId,
        string $menuTitre,
        string $dateCommande,
        int    $nombrePersonnes,
        float  $prixTotal
    ): void {
        try {
            $this->collection->insertOne([
                'commande_id'     => $commandeId,
                'menu_id'         => $menuId,
                'menu_titre'      => $menuTitre,
                'date_commande'   => $dateCommande,
                'nombre_personnes' => $nombrePersonnes,
                'prix_total'      => $prixTotal,
                'statut'          => 'terminee',
                'created_at' => new \MongoDB\BSON\UTCDateTime(time() * 1000),
            ]);

        } catch (\Exception $e) {
            error_log('[MongoService::enregistrerCommande] ' . $e->getMessage());
          
        }
    }

    public function getNombreCommandesParMenu(): array
    {
        try {
            $pipeline = [
                [
                    '$group' => [
                        '_id'               => ['menu_id' => '$menu_id', 'menu_titre' => '$menu_titre'],
                        'nombre_commandes'  => ['$sum' => 1],
                        'total_personnes'   => ['$sum' => '$nombre_personnes'],
                    ]
                ],
                ['$sort' => ['nombre_commandes' => -1]],
            ];

            $result = $this->collection->aggregate($pipeline)->toArray();

            return array_map(function ($item) {
                return [
                    'menu_id'          => $item['_id']['menu_id'],
                    'menu_titre'       => $item['_id']['menu_titre'],
                    'nombre_commandes' => $item['nombre_commandes'],
                    'total_personnes'  => $item['total_personnes'],
                ];
            }, $result);

        } catch (\Exception $e) {
            error_log('[MongoService::getNombreCommandesParMenu] ' . $e->getMessage());
            return [];
        }
    }

    public function getCAParMenu(string $dateDebut, string $dateFin): array
    {
        try {
            $pipeline = [
                [
                    '$match' => [
                        'date_commande' => [
                            '$gte' => $dateDebut,
                            '$lte' => $dateFin,
                        ]
                    ]
                ],
                [
                    '$group' => [
                        '_id'              => ['menu_id' => '$menu_id', 'menu_titre' => '$menu_titre'],
                        'chiffre_affaires' => ['$sum' => '$prix_total'],
                        'nombre_commandes' => ['$sum' => 1],
                    ]
                ],
                ['$sort' => ['chiffre_affaires' => -1]],
            ];

            $result = $this->collection->aggregate($pipeline)->toArray();

            return array_map(function ($item) {
                return [
                    'menu_id'          => $item['_id']['menu_id'],
                    'menu_titre'       => $item['_id']['menu_titre'],
                    'chiffre_affaires' => round((float) $item['chiffre_affaires'], 2),
                    'nombre_commandes' => $item['nombre_commandes'],
                ];
            }, $result);

        } catch (\Exception $e) {
            error_log('[MongoService::getCAParMenu] ' . $e->getMessage());
            return [];
        }
    }

    public function getCATotalPeriode(string $dateDebut, string $dateFin): float
    {
        try {
            $pipeline = [
                [
                    '$match' => [
                        'date_commande' => ['$gte' => $dateDebut, '$lte' => $dateFin]
                    ]
                ],
                [
                    '$group' => [
                        '_id'   => null,
                        'total' => ['$sum' => '$prix_total'],
                    ]
                ],
            ];

            $result = $this->collection->aggregate($pipeline)->toArray();

            return isset($result[0]) ? round((float) $result[0]['total'], 2) : 0.0;

        } catch (\Exception $e) {
            error_log('[MongoService::getCATotalPeriode] ' . $e->getMessage());
            return 0.0;
        }
    }
}
