---
name: termine-eintragen
description: Macht aus Terminen (Liste, Jahresplan, Text, Bild, Tabelle) eine .ics-Datei, die der Nutzer in den freigegebenen Nextcloud-Kalender der SMJ Ulm importiert; von dort erscheinen sie automatisch auf smj-ulm.de. IMMER nutzen, wenn der Nutzer Termine schickt oder in den Kalender eintragen will.
---

# Termine in den Kalender

Die Website zeigt die Termine aus dem Nextcloud-Kalender `smj_ulm_kalender` (Felix Betz), den der Nutzer als
freigegebenen Kalender mit Schreibrecht hat (Hauptkalender des Plugins „SMJ Ulm Kalender“, stündlicher Abgleich).
Statt jeden Termin einzeln einzutragen, importiert er eine .ics-Datei. Es gibt nur diesen einen Kalender:
öffentliche und interne Termine stehen gemeinsam darin und werden über Kategorien getrennt.

## Kategorien (so wird der Kalender seit Jahren gepflegt)

- `Teilnehmer` = **öffentlich**, erscheint auf der Startseite. Nur Termine für Kinder/Jugendliche/Eltern.
  Fehlt `Teilnehmer`, ist der Termin intern (Leitertreffen, Schulungen für Leiter, Konferenzen).
- `SMJ` immer, `MJF` zusätzlich bei gemeinsamen Aktionen mit der Mädchenjugend, `Zeltlager` bei allem rund ums Lager.
- Beispiele: Zeltlager `SMJ,Zeltlager,Teilnehmer`; Zeltlagervorbereitung `SMJ,Zeltlager`; Führungskreis `SMJ`;
  Fasnet, Kreuzweg, Käppelesfest, Weihnachtsfeier `SMJ,MJF,Teilnehmer`; AdventureWeekend `SMJ,Teilnehmer`;
  Gruppenleiterschulung `SMJ,Teilnehmer`.
- Titel, Orte und Beschreibungen wiederkehrender Termine aus den Vorjahren übernehmen, wenn der Nutzer einen
  Export des Kalenders geschickt hat; dann auch kennzeichnen, dass der Ort aus dem Vorjahr stammt.

## Doppelte vermeiden

Termine, die schon im Kalender stehen, nicht noch einmal erzeugen. Hat der Nutzer einen Export des Kalenders
geschickt (Kalender → „…“ → Exportieren), neue Termine dagegen prüfen (Datum überschneidet sich + ähnlicher Titel)
und Abweichungen (anderes Datum, fehlende Kategorie `Teilnehmer`) als Liste zum Korrigieren in der Nextcloud nennen.
Die Bündnisandacht ist ein monatlicher Serientermin (18., 18–21 Uhr) und wird nie erzeugt.
Ohne Export: den Nutzer bitten, vor dem Import kurz in den Kalender zu schauen.

## Ablauf

1. Termine aus der Nachricht herauslesen und als Tabelle zurückgeben (Datum, Zeit, Titel, Ort, Kategorie),
   damit der Nutzer sie prüfen kann. Nichts erfinden: fehlt eine Uhrzeit, ganztägig; fehlt das Jahr, das
   nächste passende Jahr nehmen und das in der Tabelle kennzeichnen.
2. **Kategorien** nach dem Schema oben. Ist unklar, ob ein Termin öffentlich ist, ohne `Teilnehmer` erzeugen und
   das in der Tabelle kennzeichnen.
3. Datei im Scratchpad schreiben (eine Zeile pro Termin, Format siehe Kopf von `termine_ics.py`) und ausführen:

   ```bash
   python3 .claude/skills/termine-eintragen/termine_ics.py <scratchpad>/termine.txt <scratchpad>/termine-<anlass>.ics
   ```

4. Die .ics mit SendUserFile schicken und die Import-Schritte dazuschreiben:
   - Nextcloud → Kalender → unten links **„Kalender-Einstellungen“** (Zahnrad) → **„Kalender importieren“**
   - Datei wählen, als Ziel `smj_ulm_kalender` auswählen → Importieren
   - Auf der Website erscheinen die Termine spätestens nach einer Stunde, sofort mit
     WordPress → SMJ Ulm Kalender → Einstellungen → „Aktualisiere Kalender“.
5. Hinweis: Dieselben Termine ein zweites Mal zu importieren erzeugt keine Doppelten (feste UID). Wurde ein Termin
   inzwischen geändert (anderer Titel oder anderes Datum), ist es ein neuer Termin – den alten in der Nextcloud löschen.

## Regeln

- Keine Zugangsdaten zur Nextcloud erfragen; der Import durch den Nutzer ist der vorgesehene Weg.
- Die Kalender-Feed-URL nie erfragen oder committen.
- .ics-Dateien und Terminlisten nicht ins Repo legen, nur in den Scratchpad.
