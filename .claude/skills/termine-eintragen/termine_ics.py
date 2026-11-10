"""Termine als .ics-Datei für den Import in den Nextcloud-Kalender erzeugen.

Aufruf: python3 termine_ics.py <termine.txt> <ausgabe.ics>

Eine Zeile pro Termin, Felder mit | getrennt (Ort, Kategorien, Beschreibung optional):

  Datum | Zeit | Titel | Ort | Kategorien | Beschreibung

  Datum:  2026-12-05  oder mehrtägig  2026-11-28..2026-11-29
  Zeit:   leer (ganztägig), 14:00-17:00, oder nur Beginn 19:30 (dann 2 Stunden)
  Kategorien: mit Komma getrennt, z. B. Abteilung,Zeltlager

Leere Zeilen und Zeilen mit # werden übersprungen. Die UID wird aus Titel und Datum gebildet:
derselbe Termin erzeugt also immer dieselbe UID, und ein zweiter Import legt ihn nicht doppelt an.
"""
import hashlib
import sys
from datetime import date, datetime, timedelta, timezone

VTIMEZONE = """BEGIN:VTIMEZONE
TZID:Europe/Berlin
BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
END:STANDARD
END:VTIMEZONE"""


def text(wert):
    return wert.replace('\\', '\\\\').replace(';', '\\;').replace(',', '\\,').replace('\n', '\\n')


def falten(zeile):
    """Zeilen nach RFC 5545 auf 75 Bytes umbrechen."""
    roh = zeile.encode('utf-8')
    if len(roh) <= 75:
        return zeile
    teile, aktuell = [], b''
    for zeichen in zeile:
        b = zeichen.encode('utf-8')
        if len(aktuell) + len(b) > (75 if not teile else 74):
            teile.append(aktuell.decode('utf-8'))
            aktuell = b''
        aktuell += b
    teile.append(aktuell.decode('utf-8'))
    return '\r\n '.join(teile)


def termin(zeile, stempel):
    felder = [f.strip() for f in zeile.split('|')]
    felder += [''] * (6 - len(felder))
    datum, zeit, titel, ort, kategorien, beschreibung = felder[:6]
    if not titel:
        raise ValueError(f'Titel fehlt: {zeile}')

    von_s, _, bis_s = datum.partition('..')
    von = date.fromisoformat(von_s)
    bis = date.fromisoformat(bis_s) if bis_s else von

    z = [
        'BEGIN:VEVENT',
        'UID:smj-' + hashlib.sha1(f'{titel}|{datum}|{zeit}'.encode()).hexdigest()[:20] + '@smj-ulm.de',
        'DTSTAMP:' + stempel,
        'SUMMARY:' + text(titel),
    ]
    if not zeit:
        z += ['DTSTART;VALUE=DATE:' + von.strftime('%Y%m%d'),
              'DTEND;VALUE=DATE:' + (bis + timedelta(days=1)).strftime('%Y%m%d')]
    else:
        beginn_s, _, ende_s = zeit.partition('-')
        beginn = datetime.combine(von, datetime.strptime(beginn_s.strip(), '%H:%M').time())
        if ende_s:
            ende = datetime.combine(bis, datetime.strptime(ende_s.strip(), '%H:%M').time())
        else:
            ende = beginn + timedelta(hours=2) if bis == von else datetime.combine(bis, beginn.time())
        if ende <= beginn:
            raise ValueError(f'Ende liegt nicht nach dem Beginn: {zeile}')
        z += ['DTSTART;TZID=Europe/Berlin:' + beginn.strftime('%Y%m%dT%H%M%S'),
              'DTEND;TZID=Europe/Berlin:' + ende.strftime('%Y%m%dT%H%M%S')]
    if ort:
        z.append('LOCATION:' + text(ort))
    if kategorien:
        z.append('CATEGORIES:' + ','.join(text(k.strip()) for k in kategorien.split(',') if k.strip()))
    if beschreibung:
        z.append('DESCRIPTION:' + text(beschreibung))
    z.append('END:VEVENT')
    return z


def main():
    quelle, ziel = sys.argv[1], sys.argv[2]
    stempel = datetime.now(timezone.utc).strftime('%Y%m%dT%H%M%SZ')
    zeilen = ['BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//SMJ Ulm//Termine//DE', 'CALSCALE:GREGORIAN']
    zeilen += VTIMEZONE.split('\n')
    anzahl = 0
    with open(quelle, encoding='utf-8') as f:
        for zeile in f:
            zeile = zeile.strip()
            if not zeile or zeile.startswith('#'):
                continue
            zeilen += termin(zeile, stempel)
            anzahl += 1
    zeilen.append('END:VCALENDAR')
    with open(ziel, 'w', encoding='utf-8', newline='') as f:
        f.write('\r\n'.join(falten(z) for z in zeilen) + '\r\n')
    print(f'{anzahl} Termine -> {ziel}')


if __name__ == '__main__':
    main()
