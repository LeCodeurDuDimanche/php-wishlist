<?php


use \mywishlist\Migration\Migration;
use \mywishlist\models\Item;
use Illuminate\Database\Schema\Blueprint;

class ItemsTableMigration extends Migration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $this->schema->create("item", function(Blueprint $table){
            $table->increments('id');
            $table->integer('liste_id')->unsigned();
            $table->string('titre');
            $table->string('desc');
            $table->string('img');
            $table->string('url');
            $table->float('tarif');
            $table->timestamps();
        });

        Item::create(["id" =>1,   "liste_id" => 2,"titre" =>   'Champagne',  "desc" =>   'Bouteille de champagne + flutes + jeux à gratter',"img" => 'champagne.jpg',"url" => 	'',"tarif" =>20.00, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 2, "liste_id" => 2, "titre" => 'Musique', "desc" => 'Partitions de piano à 4 mains', "img" => 'musique.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 3, "liste_id" => 2, "titre" => 'Exposition', "desc" => 'Visite guidée de l’exposition ‘REGARDER’ à la galerie Poirel', "img" => 'poirelregarder.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 4, "liste_id" => 3, "titre" => 'Goûter', "desc" => 'Goûter au FIFNL', "img" => 'gouter.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 5, "liste_id" => 3, "titre" => 'Projection', "desc" => 'Projection courts-métrages au FIFNL', "img" => 'film.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 6, "liste_id" => 2, "titre" => 'Bouquet', "desc" => 'Bouquet de roses et Mots de Marion Renaud', "img" => 'rose.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 7, "liste_id" => 2, "titre" => 'Diner Stanislas', "desc" => 'Diner à La Table du Bon Roi Stanislas (Apéritif /Entrée / Plat / Vin / Dessert / Café / Digestif)', "img" => 'bonroi.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 8, "liste_id" => 3, "titre" => 'Origami', "desc" => 'Baguettes magiques en Origami en buvant un thé', "img" => 'origami.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 9, "liste_id" => 3, "titre" => 'Livres', "desc" => 'Livre bricolage avec petits-enfants + Roman', "img" => 'bricolage.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 10, "liste_id" => 2, "titre" => 'Diner  Grand Rue ', "desc" => 'Diner au Grand’Ru(e) (Apéritif / Entrée / Plat / Vin / Dessert / Café)', "img" => 'grandrue.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 11, "liste_id" => 0, "titre" => 'Visite guidée', "desc" => 'Visite guidée personnalisée de Saint-Epvre jusqu’à Stanislas', "img" => 'place.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 12, "liste_id" => 2, "titre" => 'Bijoux', "desc" => 'Bijoux de manteau + Sous-verre pochette de disque + Lait après-soleil', "img" => 'bijoux.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 19, "liste_id" => 0, "titre" => 'Jeu contacts', "desc" => 'Jeu pour échange de contacts', "img" => 'contact.png', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 22, "liste_id" => 0, "titre" => 'Concert', "desc" => 'Un concert à Nancy', "img" => 'concert.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 23, "liste_id" => 1, "titre" => 'Appart Hotel', "desc" => 'Appart’hôtel Coeur de Ville, en plein centre-ville', "img" => 'apparthotel.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 24, "liste_id" => 2, "titre" => 'Hôtel d\'Haussonville', "desc" => 'Hôtel d\'Haussonville, au coeur de la Vieille ville à deux pas de la place Stanislas', "img" => 'hotel_haussonville_logo.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 25, "liste_id" => 1, "titre" => 'Boite de nuit', "desc" => 'Discothèque, Boîte tendance avec des soirées à thème & DJ invités', "img" => 'boitedenuit.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 26, "liste_id" => 1, "titre" => 'Planètes Laser', "desc" => 'Laser game : Gilet électronique et pistolet laser comme matériel, vous voilà équipé.', "img" => 'laser.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);
        Item::create(["id" => 27, "liste_id" => 1, "titre" => 'Fort Aventure', "desc" => 'Découvrez Fort Aventure à Bainville-sur-Madon, un site Accropierre unique en Lorraine ! Des Parcours Acrobatiques pour petits et grands, Jeu Mission Aventure, Crypte de Crapahute, Tyrolienne, Saut à l\'élastique inversé, Toboggan géant... et bien plus encore.', "img" => 'fort.jpg', "url" => '', "tarif" => 0, "updated_at" => date("Y-m-d"), "created_at" => date("Y-m-d") ]);

    }

    public function down()
    {
        $this->schema->drop("item");
    }
}
