<?php
require_once 'vendor/autoload.php';


use \mywishlist\modele\Item;
use Illuminate\Database\Capsule\Manager as DB;
$db = new DB();
$db->addConnection(parse_ini_file("src/conf/conf.ini"));
$db->setAsGlobal();
$db->bootEloquent();

foreach(Item::select("*")->get() as $i)
{
    echo "<p>";
    echo $i->id . " " . $i->nom . " " . $i->description . " " . $i->tarif . "€";
    echo "</p>";
}
