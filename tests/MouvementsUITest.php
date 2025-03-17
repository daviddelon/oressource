<?php 
use PHPUnit\Framework\TestCase;

use PDO as PDO;
use Dotenv\Dotenv;
use PlaywrightPhp\Playwright;


class MouvementsUITest extends TestCase {
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


    
        $this->faker = Faker\Factory::create('fr_FR');




    }

    protected function tearDown(): void {
        
       // $this->pdo->rollBack();
    }

    


    public function  test_VenteAvecPeseeCreate()
    {
        $playwright = new Playwright(['browser' => 'chromium']);

        $browser = $playwright->launch(); 
        $page = $browser->newPage(); 

        $commentaire=$this->faker->sentence();
        $page->goto($_ENV['URL']);
        $page->getByRole('textbox', [ 'name'=> 'Mail :' ])->fill($_ENV['ADMIN_MAIL']);
        $page->getByRole('textbox', [ 'name'=> 'Mot de passe :=' ])->fill($_ENV['ADMIN_PASS']);
        $page->getByRole('textbox', [ 'name'=> 'Mot de passe :=' ])->press('Enter');
        $page->getByRole('link', [ 'name'=> 'Points de vente' ])->first()->click();
        $page->locator('[href*="../ifaces/ventes.php?numero=1"]')->click();
        $page->getByRole('button', [ 'name'=> 'D3E' ])->click();
        $page->getByRole('textbox', [ 'name'=> 'Prix unitaire:' ])->fill( (string) $this->faker->randomDigitNotNull());
        $page->getByRole('textbox', [ 'name'=> 'Masse unitaire:' ])->fill((string) $this->faker->randomDigitNotNull());
        $page->getByRole('textbox', [ 'name'=> 'Commentaire' ])->fill($commentaire);
        $page->getByRole('button', [ 'name'=> 'Ajouter' ])->click();
        $page->getByRole('button', [ 'name'=> 'Encaisser' ])->click();


        // Attendre que la base se mette à jour
        sleep(1);
      
        $stmt = $this->pdo->query("SELECT * FROM ventes WHERE commentaire ='".$commentaire."'");
        $fetch = $stmt->fetch();
        $this->assertNotFalse($fetch);

        if ($fetch) {
            $vente_id=$fetch['id'];
            $stmt = $this->pdo->query("SELECT * FROM vendus WHERE id_vente ='".$vente_id."'");
            $fetch = $stmt->fetch();
            $this->assertNotFalse($fetch);

            if ($fetch) {
                $vendu_id=$fetch['id'];
                $stmt = $this->pdo->query("SELECT * FROM pesees_vendus WHERE id = '".$vendu_id."'");
                $fetch = $stmt->fetch();
                $this->assertNotFalse($fetch);
            }
        }
        

    } 


    

}
