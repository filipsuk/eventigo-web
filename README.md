# Eventigo

[![Build Status](https://img.shields.io/travis/eventigo/eventigo-web/master.svg?style=flat-square)](https://travis-ci.org/eventigo/eventigo-web)


## Requirements

- PHP 7.1+
- MySQL 5.6.5+, [5.7 not supported](http://stackoverflow.com/questions/34691059/select-distinct-and-order-by-in-mysql)
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

1. Vytvořit záznam v tabulce newsletters - `$ php www/index.php newsletters:create`. Použije se poslední podle parametru created. Obsahuje texty, předmět mailu atd.

2. Do nového záznamu doplnit `intro_text` a `outro_text` (HTML formát)

3. Kontrola možná na adrese `/newsletter/dynamic/<users.id>`

4. Vyrenderování (přípravení) newsletterů pro všechny, kdo má nastavený flag users.newsletter _(true)_
`
$ php www/index.php newsletters:render
`  

5. Preview konkrétního newsletteru na adrese `/newsletter/<users_newsletter.hash>`
Unsubscribe newsletterů přes link `/newsletter/unsubscribe/<users_newsletter.hash>` 
    
6. Odeslání připravených newsletterů _(nemá nastavené datum odeslání user_newsletter.sent)_
`
$ php www/index.php newsletters:send
`

## API

📚 [Apiary documentation](http://docs.eventigo.apiary.io) 

## Code style check & fix

✅ Check by running: 
```bash
composer check-cs
# OR
composer cs
```

✨ Auto-fix by running: 
```bash
composer fix-cs
# OR
composer fs
```

We use [Symplify/EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) (PHP_CodeSniffer and PHP-CS-Fixer). Thanks to @TomasVotruba!
 
## Exceptions

Html exceptions lze číst jako admin na url `/admin/exception/[exception-file.html]`
