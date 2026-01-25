 <?php
require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/firebase-credentials.json');

$auth = $factory->createAuth(); 