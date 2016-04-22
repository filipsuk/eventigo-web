Eventigo
========

Newsletters
-----------

1. Vytvoření newsletterů pro všechny, kdo má nastavený flag users.newsletter _(true)_  
Nepovinný parameter _from_ určí odesílatele _(výchozí odesílatel je filip@eventigo.cz)_  
`
newsletters:create [from]
`  
  
2. Poté je nutné nastavit předmět a obsah newsletterů v users_newsletters  
Zobrazení newsletteru na webu přes link https://eventigo.cz/newsletter/<users_newsletter.hash>  
Unsubscribe newsletterů přes link https://eventigo.cz/newsletter/unsubscribe/<users_newsletter.hash>  
    
3. Odeslání připravených newsletterů _(nemá nastavené datum odeslání user_newsletter.sent)_  
`
newsletters:send
`