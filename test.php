<?php
require_once 'vendor/autoload.php';


use \mywishlist\modele\Item;
use \mywishlist\modele\Liste;
use Illuminate\Database\Capsule\Manager as DB;
$db = new DB();
$db->addConnection(parse_ini_file("src/conf/conf.ini"));
$db->setAsGlobal();
$db->bootEloquent();

foreach(Item::select("*")->get() as $i)
{
    echo "<p>";
    var_dump($i);
    echo "</p>";
}

$lSouhait = Liste::select('*')->get();
foreach($lSouhait as $i){
	echo "<p>".$i."</p>";
}