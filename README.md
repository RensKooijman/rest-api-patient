[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-24ddc0f5d75046c5622901739e7c5dd533143b0c8e959d652212380cedb1ea36.svg)](https://classroom.github.com/a/61GDVs9q)
# REST-webservice patientendossier

Misschien heb je eerder gehoord van een patientendossier. Voorheen (en deels nog) werd
bijvoorbeeld bij een huisarts een papieren dossier bijgehouden.
Daarin stonden afspraken, recepten voor medicijnen en notities.

In de huidige tijd worden dit op een digitale manier gedaan.
De REST-webservice die je gaat ontwikkelen is voor een deel klaar.

(Let op: Het is niet de bedoeling dat je het front-end gaat maken!)

##Voorbereiding per teamlid:
* Clone dit project naar jouw computer
* Draai 'composer install' in de Terminal (of CMD)
* Pas het .env-bestand aan jouw situatie aan
* Pull en push veranderingen!!
* Let op de *coding styles*, *namespaces*, *strict typing*, *error handling*

##Voorbereiding team:
* Maak met je team een project op Github (kanban-style) en voeg een 'Todo do'-, 'In progress'-, 'Review'- en 'Done'-kolom aan toe.
* Lees onderstaande items en vul de 'To do'-kolom aan.
* Let op: Ontwikkelen is niet 1 'To do' maar uitgewerkt in meerdere!
* Gebruik de scrum ontwikkeltechniek.
* Tip: gebruik in PHPStorm de optie *Code With Me* zodat je samen aan bepaalde code kunt werken.

##Taken team:
* Bestudeer en bespreek in het team het database-diagram (zie *databasediagram.txt* in project_bestanden-map)
* Bestudeer en bespreek in het team het class-diagram (zie *class_diagram_patientendossier_full.uml* in project_bestanden-map)
* Maak de database en maak hierin de tabellen uit het database-diagram
* Bestudeer de tests in *src/tests/test.php*. Hierin zijn deze *use cases* verwerkt:
  * De zorgverlener voegt een nieuwe patient toe aan het systeem
  * De administrator voegt een nieuwe zorgverlener (practitioner) toe aan het systeem
  * De zorgverlener voegt een nieuwe afspraak in met een bestaande patient
  * De zorgverlener voegt een samenvatting toe van het gesprek
  * De patient haalt alle afspraken op, gesorteerd op oplopende datum
  * De patient haalt alle samenvattingen van gesprekken uit zijn dossier op
  * De patient haalt alle samenvattingen van gesprekken uit zijn dossier op bij een bepaalde zorgverlener
  * De zorgverlener schrijft medicijnen voor aan een bepaalde patient
  * De zorgverlener annuleert een afspraak met een bepaalde patient
* Ontwikkel de applicatie zo dat de tests gaan werken (dit noem je Test Drive Design)
* Werk eventuele TODO's in de code uit
* Niet alle methods hebben commentaar. Voeg dat toe met: /** <enter>
* Tip: eventueel kun je ook *RestMan* of *PostMan* gebruiken om te testen
* Gevorderd: Beveilig het systeem met *OAuth2 (Authorization Code Flow)*:
  * Het principe: https://www.oclc.org/developer/api/keys/oauth/explicit-authorization-code-pkce.en.html
  * Te bouwen: inlogpagina in deze webservice: https://www.sitepoint.com/php-authorization-jwt-json-web-tokens/ (Let’s Use JWTs in a PHP-based Application)
  * Te bouwen: losse html-pagina met javascript om te testen (front-end van een patient): https://www.sitepoint.com/php-authorization-jwt-json-web-tokens/ (Consuming the JWT)
  * Te bouwen: verwerken van een access token in een AUTHORIZATION-request in deze webservice: https://www.sitepoint.com/php-authorization-jwt-json-web-tokens/ (Validating the JWT)


Documentatie van een echt patientendossier zien?
http://hl7.org/fhir/http.html



mysqli configuration: my.ini{
  sql_mode:"STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION" >>>>> sql_mode:""
}  
