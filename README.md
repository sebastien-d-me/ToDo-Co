# ToDo-Co
Enhance an existing ToDo &amp; Co app

### Téléchargement
1. Téléchargez ou clonez le projet.
2. Nécessite PHP : https://www.apachefriends.org/fr/index.html
2. Nécessite Composer : https://getcomposer.org/
3. Nécessite Symfony : https://symfony.com/download
4. Nécessite NPM : https://nodejs.org/en/download

### Installation
1. Configurez le fichier .env
2. Effectuez la commande : `composer install` à la racine
3. Effectuez la commande : `npm install` à la racine
4. Effectuez la commande : `npm run build` à la racine
5. Effectuez la commande : `php bin/console doctrine:database:create` à la racine du projet
6. Effectuez la commande : `php bin/console make:migration` à la racine du projet
7. Effectuez la commande : `php bin/console doctrine:migrations:migrate` à la racine du projet
8. Effectuez la commande : `php bin/console doctrine:fixtures:load` à la racine du projet
9. Lancez le projet avec la commande : `symfony serve`

### Le rapport de couverture est déjà présent dans le dossier public/test-coverage.
