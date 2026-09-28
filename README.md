# Les katas du cinéma (eloquent-26)

Les exercices du bloc Modèles de 5XCOS, chapitres 10 à 12 du cours. **Un groupe de
tests = un chapitre.** Le dépôt est un projet Laravel 13 neuf (Pest, SQLite) qui ne
contient **que les tests** : aucune migration, aucun modèle, aucun seeder, aucun
contrôleur, aucune vue pour le cinéma. Vous les créez avec les commandes Artisan
vues dans le chapitre, jusqu'à ce que les tests du groupe passent au vert.

Les tests sont déjà écrits. **Ils sont l'énoncé.** Les énoncés détaillés, avec les
données exactes à enregistrer, sont dans les chapitres 10 à 12 du cours.

Le domaine est celui de l'exercice du cinéma du chapitre Controllers : des films
(`movies`), des séances (`showtimes`) et des genres (`genres`). Les films sont
inventés.

Il faut **PHP 8.4 ou plus** et Composer.

## Les groupes, dans l'ordre

| Groupe | Chapitre | Kata | Ce que vous écrivez |
|---|---|---|---|
| `chapitre10` | 10 — Une table | 10.1 La table | la migration `create_movies_table` : `title`, `duration`, `released_on`, `synopsis` (facultatif) |
| | | 10.2 Le modèle | le modèle `Movie` avec `#[Fillable]` |
| | | 10.3 Le seeder | `MovieSeeder` avec les cinq films de l'énoncé, appelé par `DatabaseSeeder` |
| | | 10.4 La liste | `GET /films` (`movies.index`) : un tableau des films triés par titre |
| | | 10.5 La fiche | `GET /films/{id}` (`movies.show`) : titre, durée en « 1 h 52 », synopsis, 404 si le film n'existe pas |
| | | 10.6 Le filtre | `/films?annee=2024` : seulement les films de cette année-là, « Aucun film » s'il n'y en a pas |
| `chapitre11` | 11 — Deux tables | 11.1 La table liée | la migration `create_showtimes_table`, avec une clé étrangère vers `movies` |
| | | 11.2 Les deux relations | `Showtime::movie()` et `Movie::showtimes()` |
| | | 11.3 Les fabriques | `MovieFactory` et `ShowtimeFactory` avec Faker |
| | | 11.4 Le seeder avec fabriques | `DatabaseSeeder` : les cinq films, plus dix films de fabrique avec trois séances chacun |
| | | 11.5 Le programme | `GET /seances` (`showtimes.index`) : les séances triées par heure, avec le film et le prix |
| | | 11.6 La fiche complétée | `/films/{id}` liste les séances du film, ou « Aucune séance » |
| `chapitre12` | 12 — Plusieurs genres par film | 12.1 La pivot | les migrations `create_genres_table` et `create_genre_movie_table` |
| | | 12.2 `belongsToMany` | `Movie::genres()` et `Genre::movies()` |
| | | 12.3 Le seeder | `GenreSeeder` avec les six genres, puis un à trois genres par film |
| | | 12.4 La page genre | `GET /genres/{id}` (`genres.show`) : les films du genre |
| | | 12.5 Les genres dans la liste | `/films` affiche les genres de chaque film, chargés avec `with()` |
| `bonus` | 12 — Bonus | 12.6 Les salles | la table `rooms`, la colonne `room_id` dans `showtimes`, la salle sur `/seances` |

## La procédure, pas à pas

1. **Forkez** ce dépôt sur votre compte GitHub (bouton *Fork* en haut à droite).
2. **Clonez** votre fork :
   ```bash
   git clone https://github.com/VOTRE-COMPTE/eloquent-26.git
   cd eloquent-26
   ```
3. **Installez** les dépendances et préparez le projet :
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   ```
   `php artisan migrate` vous propose de créer `database/database.sqlite` :
   répondez `yes`. C'est la base que vous regardez dans le navigateur, avec
   Herd ou Laragon.
4. **Lancez le premier groupe** :
   ```bash
   php artisan test --group=chapitre10
   ```
   Tout est rouge. C'est normal, c'est le point de départ.
5. **Travaillez un groupe à la fois**, dans l'ordre du tableau :
   ```bash
   php artisan test --group=chapitre11
   php artisan test --group=chapitre12
   php artisan test --group=bonus
   ```
6. **Committez et poussez** dès qu'un groupe est vert :
   ```bash
   git add .
   git commit -m "chapitre 10 vert"
   git push
   ```
7. **Regardez l'onglet Actions** de votre fork sur GitHub : trois jobs
   (`chapitre10`, `chapitre11`, `chapitre12`), une coche verte ou une croix rouge
   pour chacun.

Les tests tournent sur une base en mémoire : votre `database.sqlite` n'est pas
touchée. Chaque test repart d'une base vide, migrée avec **vos** migrations. Pour
voir vos données dans le navigateur, lancez vous-même `php artisan migrate:fresh --seed`.

## Comment lire un test rouge

Ouvrez `tests/Feature/Chapitre10Test.php`. Chaque test porte une phrase qui dit ce
qui est attendu, par exemple :

```php
test('la fiche d\'un film affiche son titre, sa durée en « 1 h 52 » et son synopsis', function (): void {
```

Quand il échoue, Pest affiche cette phrase, puis la raison, puis la ligne du test.
Quelques raisons que vous rencontrerez :

- `Class "App\Models\Movie" not found` : le modèle n'existe pas encore.
- `SQLSTATE[HY000]: General error: 1 no such table: movies` : la migration manque.
- `Target class [Database\Seeders\MovieSeeder] does not exist.` : le seeder n'existe pas encore.
- `Route [movies.index] not defined.` : la route n'existe pas, ou elle n'a pas ce nom.
- `Expected response status code [200] but received 404` : la page n'existe pas à cette adresse.
- `Failed asserting that false is true` suivi d'un message en français : lisez le
  message, il dit ce qui manque.

Lisez le test en entier : il montre les données qu'il prépare, l'adresse qu'il
ouvre et ce qu'il cherche dans la page.

Pour ne relancer qu'un seul test pendant que vous cherchez :

```bash
vendor/bin/pest --filter="1 h 52"
```

## Ce que les tests imposent

Les tests s'appuient sur des noms précis. Gardez ceux du chapitre :

- les tables et les colonnes : `movies`, `showtimes`, `genres`, `genre_movie`, `rooms` ;
- les modèles : `Movie`, `Showtime`, `Genre`, `Room`, dans `app/Models` ;
- les seeders : `MovieSeeder`, `GenreSeeder`, appelés par `DatabaseSeeder` ;
- les noms de routes : `movies.index`, `movies.show`, `showtimes.index`, `genres.show` ;
- les textes affichés : « Aucun film », « Aucune séance », la durée en « 1 h 52 ».

Le reste (le contrôleur que vous choisissez, le HTML de vos vues, le menu) est libre.

## Ce qui est dans le dossier

| Fichier | Rôle |
|---|---|
| `tests/Feature/Chapitre10Test.php` … `Chapitre12Test.php` | Les énoncés, un fichier par chapitre, chaque test marqué `->group('chapitre10')` (etc.). **Ne les modifiez pas.** Les tests du bonus sont à la fin de `Chapitre12Test.php`, dans le groupe `bonus`. |
| `tests/Pest.php` | Configuration de Pest : la base en mémoire remise à neuf avant chaque test, et trois fonctions qui insèrent des lignes avec `DB::table()`. |
| `.github/workflows/tests.yml` | La CI : un job par chapitre, trois résultats visibles dans l'onglet Actions. |
| Tout le reste | Le projet Laravel 13 tel que `laravel new` le crée. C'est là que vous travaillez. |
