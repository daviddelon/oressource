<?php 
use PHPUnit\Framework\TestCase;

use PDO as PDO;
use Dotenv\Dotenv;

if ( !isset( $_SESSION ) ) $_SESSION = array(  );

class GestionTest extends TestCase {
    private $pdo;
    private $faker;

    public static $shared_session = array(  ); 


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

        $this->faker = Faker\Factory::create('fr_FR');

        $_SESSION = GestionTest::$shared_session;

    }

    protected function tearDown(): void {
       // $this->pdo->rollBack();

       GestionTest::$shared_session = $_SESSION;
    }

    public function test_PointDeVenteCreate() {

        $data=array(
            'nom'=>$this->faker->company(),
            'adresse'=>$this->faker->address(),
            'surface_vente'=>$this->faker->randomNumber(3, false),
            'description'=>$this->faker->sentence(),
            'couleur'=>$this->faker->hexColor(),
            'createur'=>'1'
        );

        generic_insert_5Config($this->pdo, 'points_vente', 'nom', 'adresse', 'surface_vente', $data);

    
        $stmt = $this->pdo->query("SELECT * FROM points_vente WHERE nom ='".$data['nom']."'");
        $pointdevente = $stmt->fetch();
        $this->assertNotFalse($pointdevente);
        $this->assertEquals($data['nom'], $pointdevente['nom']);
    }
     
    public function test_PointDeCollecteCreate() {

        $data=array(
            'nom'=>$this->faker->company(),
            'adresse'=>$this->faker->address(),
            'pesee_max'=>$this->faker->randomNumber(3, false),
            'description'=>$this->faker->sentence(),
            'couleur'=>$this->faker->hexColor(),
            'createur'=>'1'
        );

        generic_insert_5Config($this->pdo, 'points_collecte', 'nom', 'adresse', 'pesee_max', $data);
       
    
        $stmt = $this->pdo->query("SELECT * FROM points_collecte WHERE nom ='".$data['nom']."'");
        $pointdecollecte = $stmt->fetch();
        $this->assertNotFalse($pointdecollecte);
        $this->assertEquals($data['nom'], $pointdecollecte['nom']);
    }
     

    public function test_PointDeSortieCreate() {

        $data=array(
            'nom'=>$this->faker->company(),
            'adresse'=>$this->faker->address(),
            'pesee_max'=>$this->faker->randomNumber(3, false),
            'description'=>$this->faker->sentence(),
            'couleur'=>$this->faker->hexColor(),
            'createur'=>'1'
        );

        generic_insert_5Config($this->pdo, 'points_sortie', 'nom', 'adresse', 'pesee_max', $data);


    
        $stmt = $this->pdo->query("SELECT * FROM points_sortie WHERE nom ='".$data['nom']."'");
        $pointdesortie = $stmt->fetch();
        $this->assertNotFalse($pointdesortie);
        $this->assertEquals($data['nom'], $pointdesortie['nom']);
    }
     

    public function test_TypeObjetsCollectes() {

        $data = array(
            'nom'=>$this->faker->word(),
            'description'=>$this->faker->sentence(),
            'couleur'=>$this->faker->hexColor(),
            'createur'=>'1'

          );


        generic_insert_config($this->pdo, 'type_dechets', $data);
    
        $stmt = $this->pdo->query("SELECT * FROM type_dechets WHERE nom ='". $data['nom']."'");
        $typedechets = $stmt->fetch();
        $this->assertNotFalse(condition: $typedechets);
        $this->assertEquals( $data['nom'], $typedechets['nom']);
    }
     
    

}
