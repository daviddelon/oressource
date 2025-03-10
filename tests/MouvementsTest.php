<?php 
use PHPUnit\Framework\TestCase;

use PDO as PDO;
use Dotenv\Dotenv;


class MouvementsTest extends TestCase {
    private $pdo;
    private $faker;


    protected function setUp(): void {


        $dotenv = Dotenv::createImmutable(__DIR__ . '/../',".env.test");
        $dotenv->load();

        // Connexion à la base de test
        $this->pdo = new PDO(
            "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );

        //$this->pdo->beginTransaction();

        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_DIRECT_QUERY, false);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        $this->pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . "/../api/");
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . "/../core/");
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . "/../ifaces/");
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . "/../moteur/");
        
        require_once  'configs.php';
        require_once  'session.php';
        require_once  'composants.php';
     
     

       $_SESSION=array(
        'id'=>1,
        'systeme'=>'oresssource'
    );
    
        $this->faker = Faker\Factory::create('fr_FR');




    }

    protected function tearDown(): void {
       // $this->pdo->rollBack();
    }

    

    public function test_VenteAvecPeseeCreate() {


        require_once  'ventes.php';

        // Le name ne semble pas etre stocke, requete inutile
        $stmt = $this->pdo->query("SELECT * FROM type_dechets WHERE id = 1");
        $typedechets = $stmt->fetch();

        $data=[
            'classe'=>'ventes',
            'id_point'=>1,
            'id_user'=>1,
            'id_moyen'=>1,
            'commentaire'=>$this->faker->sentence(),
            'date'=>new DateTime('now'),
            'items' => [
                [
                    'id_type' => 1,
                    'id_objet' => null,
                    'lot' => false,
                    'quantite' => $this->faker->numberBetween(1, 10),
                    'prix' => $this->faker->numberBetween(1, 20),
                    'masse' =>  $this->faker->numberBetween(1, 5),
                    'name' => $typedechets['nom']
                ],
                [
                    'id_type' => 1,
                    'id_objet' => null,
                    'lot' => false,
                    'quantite' => $this->faker->numberBetween(1, 10),
                    'prix' => $this->faker->numberBetween(1, 20),
                    'masse' =>  $this->faker->numberBetween(1, 5),
                    'name' => $typedechets['nom']
                ],
            ]



        ];


        $vente_id = vente_insert($this->pdo, $data);
        $vendu_ids = vendus_insert($this->pdo, $vente_id, $data);
        pesee_vendu_insert($this->pdo, $vendu_ids, $data);
        
      
      
        $stmt = $this->pdo->query("SELECT * FROM ventes WHERE commentaire ='".$data['commentaire']."'");
        $fetch = $stmt->fetch();
        $this->assertNotFalse($fetch);
        $this->assertEquals($data['commentaire'], $fetch['commentaire']);


        $stmt = $this->pdo->query("SELECT * FROM vendus WHERE id_vente ='".$vente_id."'");
        $fetchall = $stmt->fetchAll();

        $this->assertNotFalse($fetchall);
        
        foreach ($fetchall as $fetch) {
            $this->assertEquals($vente_id, $fetch['id_vente']);
        }
        

        $ids = [];
        foreach ($vendu_ids as $id) {
            $ids[] = $id;
        }
        $queryIds = implode(',', $ids);
        
        $stmt = $this->pdo->query("SELECT * FROM pesees_vendus WHERE id in (".$queryIds.")");
        
        $fetchall = $stmt->fetchAll();

        $this->assertNotFalse($fetchall);
        $i=0;
        foreach ($fetchall as $fetch) {
            $this->assertEquals($vendu_ids[$i], $fetch['id']);
            $i++;
        }
        


    }
     
    
    public function test_SortieDechetterieCreate() {


    require_once  'sorties.php';

    $data = [
        'timestamp' => new DateTime('now'),
        'type_sortie' => null, // id_type_action
        'localite' => null, 
        'classe' => 'sortiesd',
        'id_point_sortie' => 1,
        'commentaire' => $this->faker->sentence(),
        'id_user' => 1,
        'evacs' => [
          [
            'masse' => $this->faker->numberBetween(1, 25),
            'type' => 1
          ]
        ]
        ];



    $id_sortie = (int) insert_sortie($this->pdo, $data);

    insert_pesee_sortie($this->pdo, $id_sortie, $data, $data['evacs'], 'id_type_dechet_evac');


    $stmt = $this->pdo->query("SELECT * FROM sorties WHERE commentaire ='".$data['commentaire']."'");
    $fetch = $stmt->fetch();
    $this->assertNotFalse($fetch);
    $this->assertEquals($data['commentaire'], $fetch['commentaire']);

    }
    

}
