# MyWishList : Projet web de DUT

Projet web (PHP/JS/CSS) concernant la création d'un site web gérant des listes de souhaits, avec contrôle d'accès et gestion de fichiers.

Projet faisant parti du cours de PHP de deuxième année de DUT à l'IUT Nancy Charlemagne.

## Outils nécessaires pour l'installation :
    `git`
    `composer`
    `php >=7.0`

## Instructions d'installation :

    1. Utiliser composer pour installer les dépendances (https://getcomposer.org/download/)
        `composer install`
    2. Renommer le fichier src/conf/conf.ini.example en conf.ini, et adapter les valeurs pour correspondre à la base de donnée locale
       `mv src/conf/conf.ini.example src/conf/conf.ini`
        `nano src/conf/conf.ini`
    3. Exécuter les migrations afin de créer et remplir les tables avec des données de test
        `php vendor/robmorgan/phinx/bin/phinx migrate -c src/Migration/config.php`
        (ou, sous Linux uniquement : `php vendor/bin/phinx migrate -c src/Migration/config.php`)
    4. Lancer un serveur, on peut lancer le serveur php integré :
        `php -S localhost:8080`

## Troubleshooting :

    La page indique "Le fichier [...]/index.php n'existe pas"
        => Décommenter et mettre à jour la valeur de `RewriteBase` dans le fichier `.htaccess`
            `nano .htaccess`

    Erreur 500 : RuntimeException, unable to create the cache directory
        => Donner les droits d'accès au serveur web au répertoire src/cache

    Les images des items des données de test ne s'affichent pas correctement
        => Modifier la variable ```php $path_to_root``` dans le fichier `src/Migration/{timestamp}_liste_table_migration3.php`.
            Elle doit être egale au chemin depuis la racine serveur jusqu'à la racine du projet
            Ensuite éxécuter
                `php vendor/robmorgan/phinx/bin/phinx rollback -c src/Migration/config.php -t 0`
                `php vendor/robmorgan/phinx/bin/phinx migrate -c src/Migration/config.php`


## Remarques :
    Pour pouvoir les urls de participation il faut soit utiliser un autre navigateur (ou la navigation privée), soit supprimer les cookies du site ou être connecté à un autre compte que le compte créateur.
    De plus, si la liste est créé par un utilisateur authentifié, il faut se déconnecter de son compte.
    En local, les urls de partage ne sont disponible que depuis l'ordinateur local. De plus, le widget de partage Facebook ne marchera pas.
    Si vous avez un blocage de publicité ou de cookies traceurs (firefox), les boutons des réseaux sociaux n'apparaîtront pas

## Partis pris:
    Suppresion de l'image d'un item : même si cela n'était pas demandé, nous avons décidé pour plus de cohérence de supprimer l'image du serveur lorsqu'elle est supprimée ou modifiée
    Changement du mot de passe : Vu qu'il n'y a pas de jeton rememberMe, et qu'il n'y a aucun moyen de déconnecter tous les session d'une utilisateur vu notre implémentation, nous ne déconnectons pas l'utilisateur lors du changement du mot de passe.
    Modification de pseudo: Même si cela n'était pas demandé, nous avons jugé utile de pouvoir modifier le pseudo, avec vérification préalable pour conserver l'unicité (forcée par la bdd de toute façon)
