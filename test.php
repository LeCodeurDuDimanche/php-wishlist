<?php
require_once 'vendor/autoload.php';

use \mywishlist\models\Item;
use \mywishlist\models\Liste;
use Illuminate\Database\Capsule\Manager as DB;
$db = new DB();
$db->addConnection(parse_ini_file("src/conf/conf.ini"));
$db->setAsGlobal();
$db->bootEloquent();


if(isset($_GET['id']) && !empty($_GET['id'])){
	echo "<h3>Afficher un item avec id en parametre</h3>";
	$item = Item::select("*")
			->where("id", "=", $_GET['id'])
			->first();
	echo "<p>";
	 echo $item->id . " " . $item->nom . " " . $item->descr . " " . $item->tarif . "€";
	echo "</p>";
}

echo "<h3>Lister les items</h3>";
foreach(Item::select("*")->get() as $i)
{
    echo "<p>";
    echo $i->id . " " . $i->nom . " " . $i->descr . " " . $i->tarif . "€";
    echo "</p>";
}

echo "<h3>Insérer un item</h3>";
$item = new Item();
$item->nom = "Nouvel item";
$item->descr = "Item ajouté graĉe à Eloquent!";
$item->tarif = 999;
$item->liste_id = 1;

$item->save();

echo "Item sauvegardé avec l'id " . $item->id;

echo "<h3>Lister les listes</h3>";
$lSouhait = Liste::select('*')->get();
foreach($lSouhait as $i){
	echo "<p>".$i."</p>";
}
