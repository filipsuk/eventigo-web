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
    
3. Odeslání připravených newsletterů _(nemá nastavené datum odeslání user_newsletter.sent)_  
`
newsletters:send
`