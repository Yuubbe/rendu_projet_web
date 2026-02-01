# Projet Film – Résumé technique

## Architecture
- Symfony (MVC) avec Repository + Service Layer (ex: `RecommendationService`).
- Front Twig + AssetMapper (CSS/JS) pour carrousel, recherche instantanée, toasts.
- Entités clés : `User`, `Film`, `Genre`, `Location`, `DetailLocation`, `Tarif`.
- Recommandations : epsilon-greedy (affinité genres -> popularité fallback).

## Endpoints utiles
- Catalogue : `GET /films` (filtres genre/année) ; détail : `GET /film/{id}`.
- Recherche avancée JSON : `GET /films/search?q=...&genres=1,2&annee_min=2000&annee_max=2024` (retourne `results` + `suggestions`).
- Tarification dynamique : `GET /film/{id}/prix?jour=lundi`.
- Favoris :
  - Liste : `GET /fav`
  - Toggle : `GET /film/{id}/favori/toggle`
  - Export : `GET /fav/export/csv` ou `GET /fav/export/json`
  - Import : `POST /fav/import` (JSON `[1,2]` ou `{ "ids": [1,2] }`)
- Panier : `POST /panier/ajouter/{id}` ; gestion/suppression/confirmation via routes `app_cart_*`.
- Profil : `GET/POST /profile` (changement mot de passe avec toast de succès).

## Recherche & UI dynamique
- `assets/app.js` : recherche instantanée (fetch `/films/search`), suggestions, carrousel, toasts.
- `assets/styles/app.css` : thème, cartes, suggestions, toasts, micro-interactions.

## Tests
- PHPUnit (voir `phpunit.dist.xml`).
- Unitaires : `tests/Service/RecommendationServiceTest.php`.
- Fonctionnels :
  - `tests/Controller/FilmSearchControllerTest.php` (JSON search)
  - `tests/Controller/ProfileControllerTest.php` (changement mot de passe + toast)

## Documentation / schémas
- UML PlantUML : `docs/uml.puml`
- Schéma relationnel : `docs/schema.md`

## Lancer les tests
```
php bin/phpunit
```

## Notes
- Les services peuvent être documentés via Doctum si besoin (phpdoc déjà ajouté sur repository/service clés).
- L'import favoris attend des IDs valides côté base.
