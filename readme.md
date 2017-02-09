# Eventigo

## Requirements

- PHP 7+
- MySQL 5.6.5+
- Composer, Bower

## First run

1. Vytvořit novou MySQL databázi a importovat `events.sql.zip`
2. Zkopírovat `app/config/templates/config.local.neon` do adresáře `app/config` a upravit konfiguraci
3. Zkopírovat `phinx.yml.template` jako nový soubor `phinx.yml` a nastavit přístupy do databáze (viz [Phinx docs](http://docs.phinx.org/en/latest/configuration.html))
4. Nainstalovat závislosti `composer install` a `bower install`
5. Spustit databázové migrace `vendor/bin/phinx migrate`
6. Vygenerovat heslo příkazem `php www/index.php admin:generatePassword <heslo>`
7. Vytvořit admin uživatele v tabulce `users` s vygenerovaným heslem nebo použít demo admin účet: demo@gmail.com, heslo: demo
8. Přihlásit se na url `/admin`


## Newsletters

Před vytvořením emailů je možný dynamický preview na adrese `/newsletter/dynamic/<users.id>`

1. Vytvořit (nebo zkotrolovat) záznam v tabulce newsletters - zatím ručně. Použije se poslední podle parametru created. Obsahuje texty, předmět mailu atd.

2. Kontrola možná na adrese https://eventigo.cz/newsletter/dynamic/<users.id>

3. Vytvoření newsletterů pro všechny, kdo má nastavený flag users.newsletter _(true)_
`
newsletters:create
`  

4. Zobrazení newsletteru na webu přes link https://eventigo.cz/newsletter/<users_newsletter.hash>
Unsubscribe newsletterů přes link https://eventigo.cz/newsletter/unsubscribe/<users_newsletter.hash>  
    
5. Odeslání připravených newsletterů _(nemá nastavené datum odeslání user_newsletter.sent)_
`
newsletters:send
`

## Exceptions

Html exceptions lze číst jako admin na url `/admin/exception/[exception-file.html]`

