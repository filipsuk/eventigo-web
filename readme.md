Eventigo
========

Newsletters
-----------
Před vytvořením emailů je možný dynamický preview na adrese `/newsletter/dynamic/<users.id>`

1. Vytvoření newsletterů pro všechny, kdo má nastavený flag users.newsletter _(true)_  
`
newsletters:create
`  

2. Poté je nutné nastavit předmět a obsah newsletterů v users_newsletters  
Zobrazení newsletteru na webu přes link https://eventigo.cz/newsletter/<users_newsletter.hash>  
Unsubscribe newsletterů přes link https://eventigo.cz/newsletter/unsubscribe/<users_newsletter.hash>  
    
3. Odeslání připravených newsletterů _(nemá nastavené datum odeslání user_newsletter.sent)_  
`
newsletters:send
`
