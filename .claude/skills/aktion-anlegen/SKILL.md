---
name: aktion-anlegen
description: Erstellt alle Texte und Codes, die für eine neue Aktion/Anmeldung der SMJ Ulm in WordPress nötig sind (CF7-Formular, Bestätigungsmail, Send-PDF-Vorlage, Beitrag bzw. Anmeldung, Beitragsbilder), fertig zum Kopieren. IMMER nutzen, sobald der Nutzer Eckdaten einer Aktion, Veranstaltung, Freizeit, eines Wochenendes oder eine Einladung (Text oder Bild) schickt – auch ohne ausdrückliche Bitte und auch mitten in der Website-Entwicklung.
---

# Neue Aktion anlegen

Ziel: Der Nutzer soll in WordPress **nur noch kopieren und einfügen**. Jede Ausgabe steht in einem eigenen
Codeblock, in der Reihenfolge, in der er sie in WordPress braucht. Antworte auf Deutsch.

## 1. Eckdaten herauslesen

Aus Nachricht, Einladungstext oder Einladungsbild herauslesen:

| Platzhalter | Bedeutung | Beispiel |
|---|---|---|
| `{{AKTION}}` | voller Name mit Jahr | ADVENTure Weekend 2026 |
| `{{AKTION_KURZ}}` | Name im Fließtext ("zum …", "während des …") | ADVENTure Weekend |
| `{{ZEITRAUM}}` | TT.MM.–TT.MM.JJJJ | 28.11.–29.11.2026 |
| `{{BEITRAG}}` | mit €-Zeichen, ohne Leerzeichen | 20€ |
| `{{KONTAKT_NAME}}`, `{{KONTAKT_TELEFON}}`, `{{KONTAKT_MAIL}}` | Ansprechperson | |
| `{{VERWENDUNGSZWECK}}` | = `{{AKTION}}` | ADVENTure Weekend 2026 |
| `{{JAHR}}` | Jahr der Aktion | 2026 |
| `{{SCHLUSSSATZ}}` | passend zur Dauer: "In Vorfreude auf ein geniales Wochenende." / "…einen genialen Tag." / "…ein geniales Zeltlager." | |
| `{{LOGO_URL}}` | Standard: `https://smj-ulm.de/wp-content/uploads/2026/08/bawue_logo.jpg` | |
| `{{KONTOINHABER}}`, `{{IBAN}}`, `{{BANK}}` | Bankverbindung, siehe unten | |
| Ort, Zielgruppe/Alter, Anmeldeschluss, Besonderheiten | für Beitragstext und Anmeldung | |

**Nichts erfinden.** Fehlt eine Angabe, trotzdem alles erzeugen, an der Stelle `⚠ BITTE ERGÄNZEN: …` einsetzen
und die fehlenden Punkte am Anfang der Antwort in einer kurzen Liste nennen.

**Kontaktdaten und Bankverbindung stehen bewusst nicht im Repository** (das Repo ist öffentlich auf GitHub,
persönliche Daten gehören dort nicht hin). Name, Telefon und Mail der Ansprechperson sowie die Bankverbindung
aus der Nachricht des Nutzers nehmen. Steht die Bankverbindung früher im selben Gespräch, diese verwenden.
Kommt sie gar nicht vor, Platzhalter setzen und den Nutzer kurz daran erinnern.

## 2. Ausgabe (immer vollständig, in dieser Reihenfolge)

**A. Contact Form 7** – Hinweis voranstellen: *Kontakt → Formulare → beim letzten Anmeldeformular auf
„Duplizieren“ klicken* (dann bleiben Absender, Empfänger, Send-PDF-Einstellungen und Advanced-CF7-DB-Anbindung
erhalten) und nur folgende Felder ersetzen:

1. **Titel:** `Anmeldung {{AKTION}}`
2. **Reiter „Formular“:** `vorlagen/formular.txt` mit eingesetzten Werten.
   `{{ZUSATZFELDER}}` nur füllen, wenn die Eckdaten zusätzliche Abfragen nennen (z. B. T-Shirt-Größe,
   Abholung, Schwimmer); sonst die Zeile ersatzlos entfernen. Feldnamen ohne Umlaute und Leerzeichen.
3. **Reiter „E-Mail“:**
   - Empfänger: `[Mail]`
   - Betreff: `Anmeldebestätigung {{AKTION}}`
   - Zusätzliche Header: `Reply-To: {{KONTAKT_MAIL}}` (Antworten der Eltern gehen direkt an die Ansprechperson)
   - Nachrichtentext: `vorlagen/mail.txt`
   - Häkchen bei **„HTML-Format verwenden“** (wegen `<strong>` und Logo)
   - Absender **nicht** ändern.
4. **Reiter „Send PDF“:** Inhalt `vorlagen/pdf.html`; Dateiname `Anmeldung-{{AKTION ohne Leerzeichen}}-[Nachname]`.
   Zusatzfelder aus dem Formular als `<li>…: [Feldname]</li>` in `{{ZUSATZZEILEN}}`, sonst leer.

**B. Beitrag auf der jetzigen Seite** (altes Theme, Beiträge → Erstellen):
- Titel: `Anmeldung: {{AKTION}}`
- Text: 3–5 kurze Sätze an die Jugendlichen in Du-Form (was, wann, wo, für wen, Beitrag), nur mit Fakten
  aus den Eckdaten, danach `Beitrag: <strong>{{BEITRAG}}</strong>` und darunter der Hinweis, den
  CF7-Shortcode des neuen Formulars einzufügen (Shortcode steht in der Formularliste).
- Kategorie wie bisherige Anmeldungen.

**C. Anmeldung auf der neuen Seite** (Plugin „SMJ Ulm – Funktionen“, Menü *Anmeldungen → Neu*), sobald die
neue Seite live ist – immer mit ausgeben:
- Titel: `{{AKTION}}`, Text: wie B ohne Shortcode und ohne Beitragszeile, Auszug: ein Satz (max. ~140 Zeichen).
- Seitenleiste: Formular = `Anmeldung {{AKTION}}`, Datum der Aktion = erster Tag, Letzter Tag = letzter Tag
  (bei eintägigen Aktionen leer), Ort, Teilnahmebeitrag = `{{BEITRAG}}` (z. B. `20 €`).
- Hinweis: Anmeldung schließt automatisch am ersten Tag, Daten werden 21 Tage danach gelöscht.

**D. Beitragsbild** – hat der Nutzer ein Einladungsbild geschickt, damit beide Formate erzeugen:

```bash
python3 .claude/skills/aktion-anlegen/bild.py <bild> <scratchpad>/bilder <kurzname>
```

Falls Pillow fehlt oder kaputt ist: `python3 -m venv <scratchpad>/venv && <scratchpad>/venv/bin/pip install pillow`
und das Skript mit `<scratchpad>/venv/bin/python` starten. Ergebnis ansehen (Read), dann beide Dateien mit SendUserFile schicken: 1600×640 für die jetzige Seite,
1600×1000 für die neue. Hinweis: als neue Datei hochladen, in WordPress **nicht** zuschneiden.
Ohne Bild: Format kurz nennen (5:2 alt, 16:10 neu, Motiv mittig).

**E. Checkliste zum Schluss** (kurz): Testanmeldung mit eigener Mail → Mail angekommen, PDF im Anhang,
Eintrag in Advanced CF7 DB sichtbar → Testeintrag dort löschen.

## Regeln

- Vorlagentexte (Datenschutz, Schadensfreispruch, Fotos, Erste Hilfe) wörtlich übernehmen. Der Fotos-Absatz
  im PDF muss dem im Formular entsprechen.
- Personenbezogene Daten aus den Eckdaten nie in Dateien im Repo schreiben oder committen.
- Die Ausgabe nicht kürzen und nicht auf frühere Antworten verweisen – jede Aktion bekommt alle Blöcke komplett.
