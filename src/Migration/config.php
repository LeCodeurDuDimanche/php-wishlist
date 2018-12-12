<?php
    $conf = parse_ini_file(__DIR__ . "/../conf/conf.ini");


return [
  'paths' => [
    'migrations' => 'src/Migration'
  ],
  'migration_base_class' => '\mywishlist\Migration\Migration',
  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_database' => 'db',
    'db' => [
      'adapter' => 'mysql',
      'host' => $conf["host"],
      'name' => $conf["database"],
      'user' => $conf["username"],
      'pass' => $conf["password"],
      'port' => 3306
    ]
  ]
];
