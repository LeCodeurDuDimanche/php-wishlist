Projet MyWishList :

Outils nécessaires pour l'installation :
    git
    composer
    php >=7.0
    nano (ou autre editeur de texte)

Instructions d'installation :

    1. Cloner ce dépôt git ou le télécharger
        git clone https://bitbucket.org/pinot17u/php_wishlist.git
    2. Utiliser composer pour installer les dépendances (https://getcomposer.org/download/)
        composer install
    3. Renommer le fichier src/conf/conf.ini.example en conf.ini, et adapter les valeurs pour correspondre à la base de donnée locale
        mv src/conf/conf.ini.example src/conf/conf.ini
        nano src/conf/conf.ini
    4. Exécuter les migrations afin de créer et remplir les tables avec des données de test
        php vendor/robmorgan/phinx/bin/phinx migrate -c src/Migration/config.php
        (ou, sous Linux uniquement : php vendor/bin/phinx migrate -c src/Migration/config.php)
    5. Lancer un serveur, on peut lancer le serveur php integré :
        php -S localhost:8080

Troubleshooting :

    La page indique "Le fichier [...]/index.php n'existe pas"
        => Décommenter et mettre à jour la valeur de RewriteBase dans le fichier .htaccess
            nano .htaccess

    Erreur 500 : RuntimeException, unable to create the cache directory
        => Donner les droits d'accès au serveur web au répertoire src/cache

    Les images des items des données de test ne s'affichent pas correctement
        => Modifier la variable $path_to_root dans le fichier src/Migration/{timestamp}_liste_table_migration3.php.
            Elle doit être egale au chemin depuis la racine serveur jusqu'à la racine du projet
            Ensuite éxécuter
                php vendor/robmorgan/phinx/bin/phinx rollback -c src/Migration/config.php
                php vendor/robmorgan/phinx/bin/phinx migrate -c src/Migration/config.php


Remarques :
    En local, les urls de partage ne sont disponible que depuis l'ordinateur local. De plus, le widget de partage Facebook ne marchera pas.
    Si vous avez un blocage de publicité ou de cookies traceurs (firefox), les boutons des réseaux sociaux n'apparaitront pas
