"""Beitragsbilder aus einer Einladung erzeugen.

Aufruf: python3 bild.py <einladung.jpg> <ausgabeordner> <dateiname-ohne-endung>

Erzeugt
  <name>-alte-seite-1600x640.jpg   (5:2, altes Theme: Bild komplett, Seiten unscharf erweitert)
  <name>-neue-seite-1600x1000.jpg  (16:10, neues Theme: mittig zugeschnitten bzw. erweitert)
"""
import sys
from pathlib import Path

from PIL import Image, ImageDraw, ImageEnhance, ImageFilter


def erweitern(src, breite, hoehe):
    """Bild komplett in die Mitte setzen, Ränder mit weichgezeichnetem, entsättigtem Bild füllen."""
    hg = src.resize((breite, max(hoehe, round(src.height * breite / src.width))), Image.LANCZOS)
    oben = (hg.height - hoehe) // 2
    hg = hg.crop((0, oben, breite, oben + hoehe)).filter(ImageFilter.GaussianBlur(90))
    hg = ImageEnhance.Brightness(ImageEnhance.Color(hg).enhance(0.35)).enhance(0.97)

    vg = src.resize((round(src.width * hoehe / src.height), hoehe), Image.LANCZOS)
    if vg.width > breite:
        vg = src.resize((breite, round(src.height * breite / src.width)), Image.LANCZOS)

    maske = Image.new('L', vg.size, 255)
    d = ImageDraw.Draw(maske)
    rand = 40
    for i in range(rand):
        a = int(255 * i / rand)
        d.line([(i, 0), (i, vg.height)], fill=a)
        d.line([(vg.width - 1 - i, 0), (vg.width - 1 - i, vg.height)], fill=a)
    hg.paste(vg, ((breite - vg.width) // 2, (hoehe - vg.height) // 2), maske)
    return hg


def zuschneiden_oder_erweitern(src, breite, hoehe, toleranz=0.08):
    """Liegt das Seitenverhältnis nah genug am Ziel, mittig zuschneiden, sonst erweitern."""
    ziel = breite / hoehe
    ist = src.width / src.height
    if abs(ist - ziel) / ziel > toleranz:
        return erweitern(src, breite, hoehe)
    faktor = max(breite / src.width, hoehe / src.height)
    s = src.resize((round(src.width * faktor), round(src.height * faktor)), Image.LANCZOS)
    x, y = (s.width - breite) // 2, (s.height - hoehe) // 2
    return s.crop((x, y, x + breite, y + hoehe))


def main():
    quelle, ziel, name = Path(sys.argv[1]), Path(sys.argv[2]), sys.argv[3]
    ziel.mkdir(parents=True, exist_ok=True)
    src = Image.open(quelle).convert('RGB')
    erweitern(src, 1600, 640).save(ziel / f'{name}-alte-seite-1600x640.jpg', quality=90)
    zuschneiden_oder_erweitern(src, 1600, 1000).save(ziel / f'{name}-neue-seite-1600x1000.jpg', quality=90)
    print(src.size, '->', ziel)


if __name__ == '__main__':
    main()
