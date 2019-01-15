<?php


use \mywishlist\Migration\Migration;
use \mywishlist\models\Liste;
use Illuminate\Database\Schema\Blueprint;

class ListeTableMigration3 extends Migration
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
        $this->schema->dropIfExists("liste");
        $this->schema->create("liste", function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('createur')->nullable();
            $table->string('titre');
            $table->string('desc', 2048);
            $table->date('expiration');
            $table->boolean('estPublique')->default(false);
            $table->boolean('estValidee')->default(false);
            $table->string('tokenCreateur')->nullable();
            $table->string('tokenParticipant')->nullable();
            $table->timestamps();
        });


        Liste::create([ "id" => 1,	"createur" => "Jean", "titre" => 'Pour fêter le bac !',"desc" =>'Pour un week-end à Nancy qui nous fera oublier les épreuves. ', "estPublique" =>true,	"expiration" =>'2019-06-27', "tokenCreateur" =>	'nosecure1', "tokenParticipant" =>    'token11']);
        Liste::create(["id" => 2,	"createur" => "Michel Henri", "titre"=> 'Liste de mariage d\'Alice et Bob',"desc" => 'Nous souhaitons passer un week-end royal à Nancy pour notre lune de miel :)',	"expiration" =>'2018-06-30', "estPublique" =>true, "created_at" => 1525132800, "tokenCreateur" => 'toekn21',"tokenParticipant" =>    'token22']);
        Liste::create(["id" => 3,	"user_id" => 1,"titre" =>	'C\'est l\'anniversaire de Charlie', "desc" =>	'Pour lui préparer une fête dont il se souviendra :)',	"expiration" =>'2019-12-12',"tokenCreateur" =>	'token31', "tokenParticipant" =>    'token32']);

    }

    public function down()
    {
        $this->schema->drop("liste");
    }
}
