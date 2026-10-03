# smj-ulm.de – Neue Website

Neues Design und neue Funktionen für die WordPress-Seite der SMJ Ulm/Alb/Donau.
WordPress, Inhalte, Contact Form 7, Advanced CF7 DB, Send PDF, Newsletter und das Kalender-Plugin bleiben.
Ersetzt wird nur das alte Theme (Understrap), dazu kommt ein eigenes Plugin.

## Inhalt des Repos

| Ordner | Was |
|---|---|
| `wp-content/themes/smj-ulm/` | Neues Block-Theme: Design, Startseite, Diashow, Countdown, Brotkrümel |
| `wp-content/themes/smj-ulm-nacht/` | Entwurf 2 „Nacht“ (Kind-Theme): dunkel, Vollbild-Diashow, Tickets, Zeitstrahl |
| `wp-content/themes/smj-ulm-sommer/` | Entwurf 3 „Sommer“ (Kind-Theme): hell, Kachel-Raster, Laufband, schwebende Navigation |
| `wp-content/plugins/smj-ulm/` | Plugin „SMJ Ulm – Funktionen“: Anmeldungen, Termine, Newsletter-Eintrag |
| `legacy/` | Unveränderte Kopien der alten Live-Seite als Referenz (Theme, Kalender-Plugin, Code-Snippets) |
| `assets/` | Logo |

## Was das Plugin macht

**Anmeldungen** (Menüpunkt „Anmeldungen“ im Admin):
- Eine Anmeldung = Titel, Text, Beitragsbild, Auszug + rechts in der Seitenleiste: CF7-Formular, Datum der Aktion, letzter Tag, Ort, Beitrag.
- Die Anmeldeseite (`/anmeldung/<name>/`) zeigt Text, Formular und Eckdaten.
- **Ab dem Datum der Aktion ist die Anmeldung automatisch geschlossen**: Das Formular verschwindet (auch dort, wo es per Shortcode eingebunden ist), und der Server nimmt keine Einsendung mehr an.
- **21 Tage nach dem Datum werden alle Anmeldedaten automatisch gelöscht** (täglicher Job um 3 Uhr): Einträge in Advanced CF7 DB sowie Einträge und PDF-Dateien von Send PDF. Gelöscht wird nur, was vor dem Anmeldeschluss eingegangen ist; ein Formular kann also für mehrere Aktionen nacheinander benutzt werden.
- Block „Offene Anmeldungen“ für die Startseite; ohne offene Anmeldung ist er unsichtbar.

**Termine**: Block „Termine“ liest den Kalender, den das bestehende Plugin „SMJ Ulm/Alb/Donau Kalender“ stündlich lädt, und zeigt ihn im neuen Design (Anzahl, Zeitraum, Kategorien einstellbar).

**Newsletter**: Wer im Anmeldeformular „Newsletter: ja“ wählt, landet in Liste 1 des Newsletter-Plugins (aus dem Code-Snippet „NewsletterAnmeldung“ übernommen).

## Drei Entwürfe

Alle drei nutzen dasselbe Plugin und dieselben Inhalte; man wechselt nur unter Design → Themes.
Die Entwürfe „Nacht“ und „Sommer“ sind Kind-Themes von „SMJ Ulm“ – das Eltern-Theme muss daher installiert bleiben.

## Umstieg auf der Live-Seite

Vorher **Backup** von Dateien und Datenbank (webgo-Kundencenter).

1. `wp-content/plugins/smj-ulm/` und `wp-content/themes/smj-ulm/` per SFTP hochladen.
2. Plugin „SMJ Ulm – Funktionen“ aktivieren.
3. Code-Snippet „NewsletterAnmeldung“ deaktivieren (ist jetzt im Plugin).
4. Unter „Anmeldungen“ die laufenden Aktionen anlegen und jeweils Formular + Datum wählen.
5. Theme „SMJ Ulm“ aktivieren. Die Menüs „Hauptmenü“ und „Footer-Menü“ werden übernommen (Design → Menüs).
6. Im Website-Editor (Design → Editor → Startseite): Bilder für die Diashow wählen, Countdown-Datum eintragen, Links prüfen.
7. Unterseiten durchklicken. Inhalte mit Bootstrap-Klassen aus dem alten Theme (z. B. eigene Blöcke aus Genesis Custom Blocks) können anders aussehen und brauchen ggf. Nacharbeit.

Zurück zum alten Stand: altes Theme wieder aktivieren. Das Plugin kann aktiv bleiben.

## Empfehlungen unabhängig vom Umstieg

- **WP Mail SMTP** einrichten und aktivieren, sonst landen Bestätigungs-Mails oft im Spam.
- In Contact Form 7 **Cloudflare Turnstile** aktivieren (Integration). Die Formulare schicken Mails an die eingegebene Adresse; ohne Spamschutz kann das missbraucht werden (CF7 meldet „Unsichere E-Mail-Konfiguration“).
- In Send PDF prüfen, ob PDFs überhaupt auf dem Server gespeichert werden müssen.
- Gutenberg-Plugin, eines der beiden Medienordner-Plugins (FileBird / Real Media Library) und inaktive Plugins entfernen; offene Updates einspielen.

## Lokal testen

Getestet mit WordPress 7.1.2 (SQLite), Contact Form 7 6.1.7, Advanced CF7 DB, Send PDF for Contact Form 7, Newsletter und dem Kalender-Plugin aus `legacy/`: Anmeldung speichern, Newsletter-Eintrag, Ablehnung nach Anmeldeschluss, Löschung mit Stichtag, PDF-Löschung, Darstellung Desktop und Handy.
