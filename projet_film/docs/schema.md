# Schéma relationnel (résumé)

```
USER (id PK, email, roles, password)
 1..* LOCATION (id PK, date_location (date_immutable), location_prix_final, utilisateur_id FK -> USER)
  |    1..* DETAIL_LOCATION (id PK, prix_jour, location_id FK -> LOCATION, film_id FK -> FILM)
  |
  *..* FILM (id PK, titre, annee, duree, synopsis, prix_location_defaut, affiche)
         *..* GENRE (id PK, nom)
         1..* TARIF (id PK, jour_semaine, coefficient, film_id FK -> FILM)
```

- Table pivot `user_film` relie les favoris (USER ↔ FILM).
- Table pivot `film_genre` relie les genres d'un film.
- `detail_location` relie une location à un film loué avec le prix du jour.
