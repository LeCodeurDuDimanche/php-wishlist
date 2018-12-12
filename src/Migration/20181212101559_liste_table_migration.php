<?php


use \mywishlist\Migration\Migration;
use \mywishlist\models\Liste;
use Illuminate\Database\Schema\Blueprint;

class ListeTableMigration extends Migration
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
        $this->schema->create("liste", function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('titre');
            $table->string('desc');
            $table->date('expiration');
            $table->string('tokenCreateur');
            $table->string('tokenParticipant');
            $table->timestamps();
        });


        Liste::create([1,	1,	'Pour fêter le bac !',	'Pour un week-end à Nancy qui nous fera oublier les épreuves. ',	'2018-06-27',	'nosecure1']);
        Liste::create([2,	2,	'Liste de mariage d\'Alice et Bob',	'Nous souhaitons passer un week-end royal à Nancy pour notre lune de miel :)',	'2018-06-30',	'nosecure2']);
        Liste::create([3,	3,	'C\'est l\'anniversaire de Charlie',	'Pour lui préparer une fête dont il se souviendra :)',	'2017-12-12',	'nosecure3']);

    }

    public function down()
    {
        $this->schema->drop("liste");
    }
}
