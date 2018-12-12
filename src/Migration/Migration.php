<?php
    namespace mywishlist\Migration;

    use Illuminate\Database\Capsule\Manager;
    use Phinx\Migration\AbstractMigration;

    class Migration extends AbstractMigration {
        public $capsule;
        public $schema;

        public function init() {
            $this->capsule = new Manager();
            $this->capsule->addConnection(parse_ini_file(__DIR__ . "/../conf/conf.ini"));

            $this->capsule->bootEloquent();
            $this->capsule->setAsGlobal();
            $this->schema = $this->capsule->schema();
        }
    }
