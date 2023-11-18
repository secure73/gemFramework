# gemFramework
super light php Framwork based on Gemvc Library

Require .env with following data
DB_HOST=localhost
DB_PORT=3306
DB_NAME=database_name
DB_CHARSET=charsset
DB_USER=root
DB_PASSWORD=secret

URI_CONTROLLER_SEGMENT=0
URI_METHOD_SEGMENT=1

please rememmber to load .env as follows :

after loading autoloader
require __DIR__ . '/vendor/autoload.php';

use following code

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();