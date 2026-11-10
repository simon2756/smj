---
name: termine-eintragen
description: Macht aus Terminen (Liste, Jahresplan, Text, Bild, Tabelle) eine .ics-Datei, die der Nutzer in den freigegebenen Nextcloud-Kalender der SMJ Ulm importiert; von dort erscheinen sie automatisch auf smj-ulm.de. IMMER nutzen, wenn der Nutzer Termine schickt oder in den Kalender eintragen will.
---

# Termine in den Kalender

Die Website zeigt die Termine aus einem Nextcloud-Kalender (Hauptkalender des Plugins „SMJ Ulm Kalender“,
stündlicher Abgleich). Der Nutzer hat den Kalender in der Nextcloud als freigegebenen Kalender mit Schreibrecht.
Statt jeden Termin einzeln einzutragen, importiert er eine .ics-Datei.

## Ablauf

1. Termine aus der Nachricht herauslesen und als Tabelle zurückgeben (Datum, Zeit, Titel, Ort, Kategorie),
   damit der Nutzer sie prüfen kann. Nichts erfinden: fehlt eine Uhrzeit, ganztägig; fehlt das Jahr, das
   nächste passende Jahr nehmen und das in der Tabelle kennzeichnen.
2. **Kategorie** ist wichtig, weil die Website-Listen danach filtern. Bekannt: `Abteilung`, `Zeltlager`.
   Ist unklar, welche passt, `Abteilung` nehmen und das sagen.
3. Datei im Scratchpad schreiben (eine Zeile pro Termin, Format siehe Kopf von `termine_ics.py`) und ausführen:

   ```bash
   python3 .claude/skills/termine-eintragen/termine_ics.py <scratchpad>/termine.txt <scratchpad>/termine-<anlass>.ics
   ```

4. Die .ics mit SendUserFile schicken und die Import-Schritte dazuschreiben:
   - Nextcloud → Kalender → unten links **„Kalender-Einstellungen“** (Zahnrad) → **„Kalender importieren“**
   - Datei wählen, als Ziel den **freigegebenen SMJ-Kalender** auswählen → Importieren
   - Auf der Website erscheinen die Termine spätestens nach einer Stunde, sofort mit
     WordPress → SMJ Ulm Kalender → Einstellungen → „Aktualisiere Kalender“.
5. Hinweis: Dieselben Termine ein zweites Mal zu importieren erzeugt keine Doppelten (feste UID). Wurde ein Termin
   inzwischen geändert (anderer Titel oder anderes Datum), ist es ein neuer Termin – den alten in der Nextcloud löschen.

## Regeln

- Keine Zugangsdaten zur Nextcloud erfragen; der Import durch den Nutzer ist der vorgesehene Weg.
- Die Kalender-Feed-URL nie erfragen oder committen.
- .ics-Dateien und Terminlisten nicht ins Repo legen, nur in den Scratchpad.
