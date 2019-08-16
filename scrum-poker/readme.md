# Scrum Poker

Aplikace bude sloužit k hlasování běhěm Scrum pokru. Chceme si vyzkoušet framework Nette na nějakém jednoduchém projektu.

Specifikace a zadání [zde](docs/spec.md).

## Prerekvizity

- [Composer](https://getcomposer.org)
- web server, např. Apache

## Instalace

- Stáhněte si zdrojové kódy
- Spusťte composer příkazem `composer install`
- Vytvořte soubor `config/local.neon`
- Výchozí adresář pro webový server je `www`
- Adresáři **temp** je třeba nastavit práva pro zápis (týká se linuxu, nastavte práva zápisu pro uživatele, pod kterým běží Apache, většinou www-data)

## Databáze

- Ve výchozím stavu aplikace používá SQLite (soubor je uložen v adresáři temp)
- Databáze se inicializuje při vstupu na úvodní stránku (pokud uděláte čistou instalaci a půjdete na jinou stránku než homepage, tak můžete dostat chybovou hlášku, že databáze neexistuje) 
