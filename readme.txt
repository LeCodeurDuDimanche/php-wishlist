Projet MyWishList :

Instructions d'installation

    1. Cloner ce dépôt git
        git clone git@bitbucket.org:pinot17u/PHP_Wishlist.git
    2. Utiliser composer pour installer les dépendances
        composer install
    3. Renommer le fichier src/conf/conf.ini.example en conf.ini, et adapter les valeurs pour correspondre à la base de donnée locale
        mv src/conf/conf.ini.example src/conf/conf.ini
        nano src/conf/conf.ini
    4. Exécuter les migrations afin de créer et remplir les tables avec des données de test
        php vendor/robmorgan/phinx/bin/phinx migrate -c src/Migration/config.php
        (ou, sous Linux uniquement : php vendor/bin/phinx migrate -c src/Migration/config.php)
    5. Lancer un serveur, on peut lancer le serveur php integré :
        php -S localhost:8080

Remarques :
    En local, les urls de partage ne sont disponible que depuis l'ordinateur local. De plus, le widget de partage Facebook ne marchera pas.
