Eventigo
========

Newsletters
-----------
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

