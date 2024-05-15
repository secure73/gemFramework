# gemFramework
super light php Framwork based on Gemvc Library

Require .env with following data in app folder:

DB_HOST=localhost
DB_PORT=3306
DB_NAME=db_name
DB_CHARSET=utf8
DB_USER=db_username
DB_PASSWORD=''
TOKEN_ISSUER=''
QUERY_LIMIT=10

TOKEN_SECRET='secret for your token'
TOKEN_ISSUER='MyCompany'
REFRESH_TOKEN_VALIDATION_IN_SECONDS=43200
ACCESS_TOKEN_VALIDATION_IN_SECONDS=1200

SERVICE_IN_URL_SECTION=2
METHOD_IN_URL_SECTION=3

please remember to create index.php as follows :

<?php
require_once 'vendor/autoload.php';

use GemLibrary\Http\ApacheRequest;
use App\Core\Bootstrap;
use GemLibrary\Helper\NoCors;
use Symfony\Component\Dotenv\Dotenv;

NoCors::NoCors();

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/app/.env');

$webserver = new ApacheRequest();
$bootstrap = new Bootstrap($webserver->request);