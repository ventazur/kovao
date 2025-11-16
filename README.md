# KOVAO

**KOVAO** est une plateforme d’évaluation open source créé en 2018. Développée à l’origine pour répondre aux besoins du niveau collégial au Québec, KOVAO privilégie la simplicité et peut être utilisée dans une grande variété de contextes pédagogiques.

---
## Objectif

KOVAO vise à offrir un environnement d’évaluation :

- **centralisé et stable** pour le personnel enseignant ;
- **transparent et équitable** pour les étudiants et étudiantes ;
- **pérenne et libre** pour les institutions d’enseignement.

Toute personne ou institution peut l’utiliser et y contribuer librement,
dans le respect de la licence AGPL-3.0 et des conditions d’attribution.

---
## Notes
#### Politique de confidentialité

Le minimum de renseignements personnels est requis pour assurer le fonctionnement du processus d’évaluation. 
#### Déclaration de non responsabilité

Cette plateforme est fournie “tel quel”, sans garantie d’exactitude, de disponibilité ou de performance.  Les auteurs déclinent toute responsabilité pour toute erreur, perte de données ou conséquence découlant de son utilisation.  L’utilisateur assume l’entière responsabilité de l’usage qu’il fait de la plateforme.

---
## Prérequis

- PHP >= 8.0
- MySQL >= 8.0

KOVAO a été développé avec le framework CodeIgniter 3.1.13. Il utilise également le Javascript pour le rendu dynamique avec la bibliothèque jQuery 3.5. Finalement, le design a été construit autour de la bibliothèque Bootstrap 4.

---
## Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/ventazur/kovao.git

# 2. Installer les dépendances de KOVAO
cd application
composer install

# 3. Installer les dépendances du système
apt install ghostscript php8.1-intl php8.1-fpm php8.1-mysql php8.1-xml php8.1-bcmath php8.1-gd php8.1-mbstring

(Vérifier la liste des extensions PHP dans INSTALL_php_extensions.txt si nécessaire)

# 3. Créer la structure de la base de données
INSTALL_database_creation_aaaammjj.sql
INSTALL_database_table_enseignants_aaammjj.sql (le compte admin par défaut)
INSTALL_database_table_parametres_aaaammjj.sql (les paramètres de base par défaut)

# 4. Configurer
vi application/config/database.php
vi application/config/config.php
vi application/config/config_site.php
```

---
## Fonctionnement

KOVAO est configuré de façon à rouler seulement en https.

Un manuel d'utilisation sommaire est disponible à cette adresse : [Manuel d'utilisation](https://docs.google.com/document/d/1gB2gdlvXzuszN6C3DhrJeLHWjMCv6p_3ZcliIVWIFuU/edit?tab=t.0)

En 2024, les laboratoires ont été ajoutés en complément des évaluations.  
Comme aucun éditeur graphique n’est disponible pour créer les vues de laboratoires, celles-ci doivent être développées directement en HTML en utilisant les balises PHP des helpers. Plusieurs exemples fonctionnels ainsi que des gabarits sont fournis dans le répertoire :  
`/application/views/laboratoire`

De 2018 à 2021, il était possible de répondre à une évaluation sans créer de compte. Cette fonctionnalité a depuis été retirée, mais il est possible que certains fragments de code liés à cet ancien mode de fonctionnement soient encore présents.