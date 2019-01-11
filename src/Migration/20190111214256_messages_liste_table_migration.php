<?php


use \mywishlist\Migration\Migration;
use \mywishlist\models\MessagesListe;
use Illuminate\Database\Schema\Blueprint;

class MessagesListeTableMigration extends Migration
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
         $this->schema->dropIfExists("messagesListe");
         $this->schema->create("messagesListe", function(Blueprint $table){
             $table->increments('id');
             $table->string('texte');
             $table->string('createur', 2048);
             $table->integer('liste_id')->unsigned();
             $table->timestamps();
         });
     }

     public function down()
     {
         $this->schema->drop("messagesListe");
     }
}
