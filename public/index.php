<?php
require_once __DIR__ . '/../vendor/autoload.php';

if (php_sapi_name() !== 'cli' && preg_match('/\.(png|ico|jpg|js|css)$/', $_SERVER['REQUEST_URI'])) {
  return false;
}

// Initialisation de certaines choses
use App\Entity\User;
use Twig\Environment;
use App\Routing\Router;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Twig\Loader\FilesystemLoader;
use App\Controller\IndexController;
use Symfony\Component\Dotenv\Dotenv;
use App\Controller\ContactController;
use App\Controller\UserController;
use App\Routing\RouteNotFoundException;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

// DB
[
  'DB_HOST'     => $host,
  'DB_PORT'     => $port,
  'DB_NAME'     => $dbname,
  'DB_CHARSET'  => $charset,
  'DB_USER'     => $user,
  'DB_PASSWORD' => $password
] = $_ENV;

// $dsn = "mysql:dbname=$dbname;host=$host:$port;charset=$charset";

/* try {
  $pdo = new PDO($dsn, $user, $password);
  var_dump($pdo);
} catch (PDOException $ex) {
  echo "Erreur lors de la connexion à la base de données : " . $ex->getMessage();
  exit;
} */

// Create a simple "default" Doctrine ORM configuration for Attributes
$config = ORMSetup::createAttributeMetadataConfiguration(
  paths: array(__DIR__ . "/../src/Entity"),
  isDevMode: $_ENV['APP_ENV'] === 'dev',
);

// configuring the database connection
$connection = DriverManager::getConnection([
  'host' => $host,
  'port' => $port,
  'driver' => 'pdo_mysql',
  'user'     => $user,
  'password' => $password,
  'dbname'   => $dbname,
], $config);

// obtaining the entity manager
$entityManager = new EntityManager($connection, $config);
$user = new User();
$user->setName('Mathis ROME');
$entityManager->persist($user);
$entityManager->flush();
// var_dump($entityManager);


// Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates/');
$twig = new Environment($loader, [
  'debug' => $_ENV['APP_ENV'] === 'dev',
  'cache' => __DIR__ . '/../var/twig/',
]);

// Appeler un routeur pour lui transférer la requête
$router = new Router([
  Environment::class => $twig,
  EntityManager::class => $entityManager
]);
$router->addRoute(
  'homepage',
  '/',
  'GET',
  IndexController::class,
  'home'
);
$router->addRoute(
  'contact_page',
  '/contact',
  'GET',
  ContactController::class,
  'contact'
);

$router->addRoute(
  'user_create',
  '/user/create',
  'GET',
  UserController::class,
  'create'
);

if (php_sapi_name() === 'cli') {
  return;
}

try {
  $router->execute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (RouteNotFoundException $ex) {
  http_response_code(404);
  echo "Page not found";
}
