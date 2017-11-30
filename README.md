# ScoutnetWidgets - Wordpress plugin

Plugin för att koppla Wordpress till Scoutnet och visa data genom Widgets.

## Installation

Installeras som vanlig plugin i wordpress.
> Fungerar enbart om man har en egen lösning!

### Första uppstarten
Navigera till Adminmenyn > Scoutnet Widgets

Här behöver du fylla i Kår ID och en del API nycklar som finns i Scoutnet
> Du behöver ha rätt behörighet i Scoutnet (IT ansvarig eller Medlemsregisterare fungerar)

Du hittar uppgifterna i Scoutnet under "Din kår" > Webbkoppling.
Är inte API-systemet påslaget måste du göra detta först genom knappen högst upp till höger.
Kår ID hittar du genom att expandera ett av fälten.

Se till att skriva in rätt API-nyckel på rätt plats, och att inga extra tecken följer med.

Efter man sparat så kommer man se att brädgårstecknet ändrat till grönt om anslutningen fungerar.

### Lägga till Widgets
Navigera till Adminmenyn > Utseende > Widgetar

Här lägger du till widgetar som vanligt, bara dra till den plats du vill ha den.
Vissa widgetar har fler val av formatering, exempelvis val av titel, se nedan för vad det finns för widgetar och val.

### Beroenden
Wordpress installationen behöver (bör) ha ett tillägg såsom [Dynamic Widgets](https://wordpress.org/plugins/dynamic-widgets/) eller likannde som gör att man kan styra på vilka sidor en viss widgets visas på.


## Inkluderade Widgets
Det finns fem olika widgets inkluderade i detta tillägg.
* Antal Medlemmar
  * Tre formateringar
* Födelsedag
  * Fyra formateringar
* Ledare på avdelning
  * Tre val
* Scouter på avdelning
  * Tre val
* Trygga möten


#### Visa antalet medlemmar
Visar antalet medlemmar på aktuell avdelning

**Formateringsval**
- Simpel
  - Visar bara totala antalet medlemmar i kåren
- Ledare
  - Visar totala antalet medlemmar och antalet ledare.
- Allt
  - Visar totala antalet medlemmar, antalet ledare, antalet kårfunktionärer-
- Grafik
  - Visar totala antalet medlemmar, antalet ledare samt en grafik över de som är under 18 fördelat per årskull med respektive grens färg.


#### Visa ledare på en avdelning
Visar ledarna på aktuell avdelning.
> Kräver ett tillägg såsom Dynamic Widgets.

**Formateringsval**
- Visa namn på ledarna
  - Visar för-/efternamn samt funktion på avdelningen
- Visar namn samt telefonnummer
  - Som ovan, med telefonnummer med
- Visar namn, telefonnummer samt mejladress
  - Som ovan, med mejladress

...
Denna wdiget kräver att sidans namn är samma som avdelningens namn i scoutnet
...
...
Denna widget klarar bara av att hantera de ledare som har avdelningen som huvudavdelning, så ledare som är på fler avdelningar kommer bara listas på ett ställe. Utmanare som är assistenter kommer inte heller visas då deras utmanaravdelning är deras huvudavdelning.
...


#### Visa scouter på en avdelning
Visar scouter på aktuell avdelning.
> Kräver ett tillägg såsom Dynamic Widgets.

**Formateringsval**
- Visa namn på scouterna på avdelningen (Ja/Nej)
  - Visar endast för inloggade ovasett inställning
- Visa respektive scouts patrulltillhörighet (Ja/Nej)
- Sortering
  - Förnamn (standard)
  - Efternamn
  - Grupperad i patruller (endast om man valt Ja på båda frågorna innan)

...
Denna wdiget kräver att sidans namn är samma som avdelningens namn i scoutnet
...

#### Visa medlemmar som fyller år
Visar de som fyller år idag.

**Formateringsval**
- Visar avdelning (Ja/Nej)
  - Visar avdelning på de som fyller år
- Visningsalternativ
  - Bara antal (standard
  - Förnamn
  - Förnamn samt första bokstaven i efternamnet.

