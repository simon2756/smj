# smj-ulm.de

WordPress-Seite der SMJ Ulm/Alb/Donau (Hosting webgo). Aufbau des Repos und Umstieg: siehe `README.md`.
Antworten auf Deutsch.

## Neue Aktionen – immer automatisch

Schickt der Nutzer Eckdaten einer Aktion oder eine Einladung (Text oder Bild), **sofort den Skill
`aktion-anlegen` nutzen** (`.claude/skills/aktion-anlegen/SKILL.md`) und alle Texte und Codes für WordPress
fertig zum Kopieren ausgeben – auch ungefragt und auch mitten in der Arbeit an der Website.

## Termine – immer automatisch

Schickt der Nutzer Termine (Liste, Jahresplan, Bild), **den Skill `termine-eintragen` nutzen**
(`.claude/skills/termine-eintragen/SKILL.md`): .ics-Datei zum Import in den freigegebenen Nextcloud-Kalender
erzeugen, von dem die Website ihre Termine liest.

## Datenschutz im Repo

Das Repository ist öffentlich. Keine Namen mit Kontaktdaten, Telefonnummern, Mailadressen, Bankverbindungen,
Anmeldedaten oder Kalender-Feed-URLs committen; `wp-config.php` nie.
