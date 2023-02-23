# Bando

![couverture](./badge.svg)

## Equipe
[Bruno](https://github.com/Surfista13)   
[Déana](https://github.com/DeaLeSa)   
[AnneSo](https://github.com/HeyPinkiePie)   
Février 2023

## Présentation du projet
Bando est une plateforme développée par la société ENI pour permettre aux stagiaires actifs ainsi qu'aux anciens stagiaires d'organiser des sorties. La plateforme est privée et l'inscription est gérée par les administrateurs. Les sorties et les participants sont rattachés à un campus pour une meilleure organisation géographique.

## Pré-requis
:heavy_check_mark: PHP >= 8.1  
:heavy_check_mark: Symfony >= 6.2  
:heavy_check_mark: MySQL >= 8.0.32  
:heavy_check_mark: Composer  

## Installation
:one: Clonez le dépôt : 
`
git clone https://github.com/[nom_utilisateur]/Bando.git
`  
:two: Installez les dépendances : 
`
composer install
`  
:three: Créez un fichier nommé **.env.local** à la racine du projet et configurez le en y renseignant vos informations (mot de passe, nom de la base de données et APP_SECRET) :  
  
```
DATABASE_URL="mysql://root:[votre-mot-de-passe]@127.0.0.1:3306/[nom-de-votre-base-de-donnees]"
APP_SECRET=[votre-app-secret]
MAILER_DSN=smtp://localhost
```
:four: Créez la base de données : 
`
symfony console doctrine:database:create
`  
:five: Installez les fixtures de données : 
`
symfony console doctrine:fixtures:load
`  
:six: Lancez le serveur Symfony : 
`
symfony serve
`  
## Fonctionnalités
### Gestion des utilisateurs
- [x] :heavy_check_mark: Se connecter
- [x] :heavy_check_mark: Se souvenir de moi
- [x] :heavy_check_mark: Gérer son profil
- [x] :heavy_check_mark: Photo pour le profil
- [x] :heavy_check_mark: Mot de passe oublié
- [x] :heavy_check_mark: Inscrire des utilisateurs par intégration d'un fichier
- [x] :heavy_check_mark: Inscrire un utilisateur manuellement
- [x] :heavy_check_mark: Désactiver des utilisateurs
- [x] :heavy_check_mark: Supprimer des utilisateurs
### Gestion des sorties
- [x] :heavy_check_mark: Afficher les sorties par campus
- [x] :heavy_check_mark: Créer une sortie
- [x] :heavy_check_mark: S'inscrire
- [x] :heavy_check_mark: Se désister
- [x] :heavy_check_mark: Clôture des inscriptions
- [x] :heavy_check_mark: Annuler une sortie
- [x] :heavy_check_mark: Archiver les sorties
- [x] :heavy_check_mark: Afficher le profil des autres participants
- [ ] :x: Utilisation smartphone
- [ ] :x: Utilisation tablette
- [x] :heavy_check_mark: Annuler une sortie en tant qu'administrateur
- [x] :heavy_check_mark: Gérer les lieux
- [x] :heavy_check_mark: Gérer les villes
### Gestion de groupes privés
- [ ] :x: Gérer des groupes privés


