# 🎮 GameTracker

Webová aplikácia na sledovanie videohier. Môžeš si pridávať hry, označovať ich status (chcem hrať / hrám / dohraná), dávať hodnotenia a filtrovať podľa žánru alebo platformy.


---

## Čo aplikácia vie

- **Zoznam hier** – prehľadná tabuľka so všetkými hrami
- **Pridanie hry** – formulár s validáciou
- **Úprava hry** – zmena statusu, hodnotenia, poznámky
- **Vymazanie hry** – s potvrdením
- **Filtrovanie** – podľa statusu, žánru, alebo vyhľadávanie podľa názvu
- **Správa žánrov** – CRUD pre žánre hier
- **Štatistiky** – prehľad s grafmi koľko hier je v akom stave

---

## Štruktúra projektu

```
gametracker/
├── index.php               # Hlavná stránka – zoznam hier
├── database.sql            # SQL skript – vytvorí DB, tabuľky, vloží testovacie dáta
├── includes/
│   ├── db.php              # Pripojenie k databáze
│   ├── functions.php       # Pomocné funkcie
│   ├── header.php          # HTML hlavička + navbar
│   └── footer.php          # HTML pätička
├── pages/
│   ├── add_game.php        # Formulár – pridanie hry
│   ├── edit_game.php       # Formulár – úprava hry
│   ├── delete_game.php     # Vymazanie hry
│   ├── game_detail.php     # Detail jednej hry
│   ├── genres.php          # Správa žánrov
│   ├── delete_genre.php    # Vymazanie žánru
│   └── stats.php           # Štatistiky
└── assets/
    └── css/
        └── style.css       # Štýly
```

---

## Tabuľky v databáze

### `genres`
| stĺpec | typ | popis |
|--------|-----|-------|
| id | INT AI PK | primárny kľúč |
| name | VARCHAR(50) | názov žánru |
| description | VARCHAR(255) | popis |

### `games`
| stĺpec | typ | popis |
|--------|-----|-------|
| id | INT AI PK | primárny kľúč |
| title | VARCHAR(150) | názov hry |
| genre_id | INT FK | cudzí kľúč → genres |
| platform | VARCHAR(50) | platforma (PC, PS5...) |
| release_year | YEAR | rok vydania |
| status | ENUM | chcem hrat / hrám / dohraná / nedohraná |
| rating | TINYINT | hodnotenie 1–10 |
| note | TEXT | moja poznámka |
| added_at | DATETIME | kedy som pridal záznam |

---

## Inštalácia

1. Nainštaluj XAMPP (alebo iný PHP + MySQL stack)
2. Skopíruj projekt do `htdocs/gametracker`
3. Otvor phpMyAdmin a spusti `database.sql` – vytvorí databázu aj testovacie dáta
4. V súbore `includes/db.php` skontroluj prihlasovacie údaje (štandardne `root` bez hesla)
5. Otvor `http://localhost/gametracker`

---

## Použité technológie

- **PHP 8+** – bez frameworkov, čisté PHP
- **MySQL** – cez `mysqli` (nie PDO)
- **HTML + CSS** – vlastné štýly, žiadny Bootstrap
- **Google Fonts** – Outfit + JetBrains Mono

---

## Čo by som doplnil (keby mal viac času)

- [ ] Stránkovanie pri veľkom počte hier
- [ ] Možnosť nahrať obrázok ku hre
- [ ] Prihlasovanie / používateľské účty

---

Projekt: GameTracker
