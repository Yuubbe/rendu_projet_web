# Projet Film de fou – Résumé technique et lancement

## Prérequis
Avant de lancer le projet, assurez-vous d’avoir installé :

- PHP >= 8.4
- Symfony > 7
- Composer
- MySQL


## Installation du projet

### Cloner le projet

```git clone <URL_DU_REPO> projet_film
cd projet_film```

### Installer les dépendances PHP avec Composer

```composer install```

### Configurer la base de données

```mysql -u root```

Il faut placer le fichier *bdd.sql* dans un dossier facilement accessible puis l'exécuter

```SOURCE <emplacements>/bdd.sql```

### Mettre le fichier .env à jour 

à la ligne prévu on met :
DATABASE_URL="mysql://root:@127.0.0.1:3306/projet_web"

### Vérifier la connexion Doctrine

```php bin/console doctrine:database:info
php bin/console doctrine:schema:validate```

### Enfin démarrer le serveur

```symfony serve```

Le site est maintenant disponible sur un navigateur à l'adresse [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
