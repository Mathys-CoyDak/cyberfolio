## Étapes d'installation

### 1. Cloner le dépôt
Clonez le dépôt du projet depuis le gestionnaire de version :
```bash
git clone <URL_DU_DEPOT>
cd <NOM_DU_PROJET>
```

### 2. Installer les dépendances
Exécutez la commande suivante pour installer les dépendances du projet :
```bash
composer install
```

### 3. Configurer le fichier `.env`


- Mettez à jour les variables d'environnement dans le fichier `.env`, par exemple :
  ```env
  DATABASE_URL="mysql://root@127.0.0.1:3306/cyberfolio?serverVersion=10.4.32-MariaDB&charset=utf8mb4"
  ```

### 4. Configurer la base de données

1. Créez la base de données :
   ```bash
   php bin/console doctrine:database:create
   ```
2. Exécutez les migrations :
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
3. Chargez les données initiales (fixtures)88 :
   ```bash
   php bin/console doctrine:fixtures:load
   ```

### 5. Lancer le serveur
Démarrez le serveur pour tester l'application :

- Avec Symfony CLI :
  ```bash
  symfony server:start
  ```

L'application sera accessible à l'adresse : [http://127.0.0.1:8000](http://127.0.0.1:8000).
