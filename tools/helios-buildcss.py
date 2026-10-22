#!/usr/bin/env python3
"""Build Helios Multi's own stylesheets from SMF 2.1's default ones.

2.1 markup needs 2.1's structural CSS (grids, popups, dropdowns), so the theme ships its own
copy of each default stylesheet with the colours converted in place: dark text -> light,
light backgrounds/borders -> dark, text shadows removed, link colours -> Helios, and no row
hover highlights. SMF then loads these instead of the default theme's files. helios.css (hand
written) adds the Helios layout on top.

Usage: tools/helios-buildcss.py /path/to/forum/Themes [theme-dir]
       (theme-dir defaults to HeliosMulti21; re-run after an SMF update changes Themes/default/css)
"""
import colorsys
import os
import re
import sys

if len(sys.argv) < 2 or not os.path.isdir(os.path.join(sys.argv[1], 'default', 'css')):
	sys.exit(__doc__)
ROOT = sys.argv[1]
THEME_DIR = sys.argv[2] if len(sys.argv) > 2 else 'HeliosMulti21'
# default file -> theme file. jquery.sceditor.css is always loaded from the default theme, so its
# dark version goes in jquery.sceditor.theme.css, which SMF loads after it.
SOURCES = {
	'index.css': 'index.css',
	'responsive.css': 'responsive.css',
	'calendar.css': 'calendar.css',
	'admin.css': 'admin.css',
	'attachments.css': 'attachments.css',
	'report.css': 'report.css',
	'jquery-ui.datepicker.css': 'jquery-ui.datepicker.css',
	'jquery.timepicker.css': 'jquery.timepicker.css',
	'jquery.custom-scrollbar.css': 'jquery.custom-scrollbar.css',
	'slider.min.css': 'slider.min.css',
	'jquery.sceditor.css': 'jquery.sceditor.theme.css',
	'jquery.sceditor.default.css': 'jquery.sceditor.default.css',
}
OUT_DIR = os.path.join(ROOT, THEME_DIR, 'css')
# Extra rules appended to a generated file (the editor iframe has no page CSS to inherit from).
EXTRA = {
	'jquery.sceditor.default.css': 'html, body { background: #1a1a1a; color: #fff; }\na { color: #e0ffe0; }\n',
}
# Row hover highlights the old theme never had.
NO_HOVER_BG = re.compile(r'windowbg|\btr\b|topic|board|post|quickbuttons li')

NAMED = {'white': (255, 255, 255), 'black': (0, 0, 0), 'red': (255, 0, 0), 'green': (0, 128, 0),
         'blue': (0, 0, 255), 'gray': (128, 128, 128), 'grey': (128, 128, 128), 'silver': (192, 192, 192),
         'orange': (255, 165, 0), 'yellow': (255, 255, 0), 'navy': (0, 0, 128), 'maroon': (128, 0, 0)}
COLOUR = re.compile(r'#[0-9a-fA-F]{3,8}\b|rgba?\([^)]*\)|\b(?:' + '|'.join(NAMED) + r')\b')


def parse(c):
    """Return (r, g, b, alpha) in 0-1, or None."""
    c = c.strip().lower()
    if c in NAMED:
        r, g, b = NAMED[c]
        return r / 255, g / 255, b / 255, 1.0
    if c.startswith('#'):
        h = c[1:]
        if len(h) in (3, 4):
            h = ''.join(ch * 2 for ch in h)
        if len(h) not in (6, 8):
            return None
        a = int(h[6:8], 16) / 255 if len(h) == 8 else 1.0
        return int(h[0:2], 16) / 255, int(h[2:4], 16) / 255, int(h[4:6], 16) / 255, a
    m = re.match(r'rgba?\(([^)]*)\)', c)
    if m:
        parts = [p.strip() for p in m.group(1).replace('/', ',').split(',')]
        try:
            r, g, b = (float(p.rstrip('%')) * (2.55 if p.endswith('%') else 1) / 255 for p in parts[:3])
            a = float(parts[3]) if len(parts) > 3 else 1.0
        except ValueError:
            return None
        return r, g, b, a
    return None


def fmt(r, g, b, a):
    r, g, b = (max(0, min(255, round(x * 255))) for x in (r, g, b))
    return '#%02x%02x%02x' % (r, g, b) if a >= 1 else 'rgba(%d, %d, %d, %.2f)' % (r, g, b, a)


def remap(c, kind):
    """kind: 'text' lightens dark colours, 'bg'/'border' darkens light ones. None = leave alone."""
    p = parse(c)
    if not p:
        return None
    r, g, b, a = p
    h, l, s = colorsys.rgb_to_hls(r, g, b)
    if kind == 'text':
        if l >= 0.6:
            return None
        l2 = 0.93 - l * 0.45
    else:
        if l <= 0.45 or a < 0.15:
            return None
        l2 = (0.10 + (1 - l) * 0.45) if kind == 'bg' else (0.22 + (1 - l) * 0.35)
        s = min(s, 0.35)
    return fmt(*colorsys.hls_to_rgb(h, l2, s), a)


def recolour(value, kind):
    changed = False

    def sub(m):
        nonlocal changed
        new = remap(m.group(0), kind)
        if new:
            changed = True
            return new
        return m.group(0)
    out = COLOUR.sub(sub, value)
    return out if changed else None


def is_link(selector):
    last = re.split(r'[\s>+~]+', selector.strip())[-1]
    return bool(re.match(r'a(?![\w-])', last))


def is_hover(selector):
    return ':hover' in selector or ':focus' in selector


def convert(selectors, body):
    """Return body with colours converted, or None if nothing changed."""
    sels = [x.strip() for x in selectors.split(',') if x.strip()]
    hover_only = sels and all(is_hover(x) for x in sels)
    out, changed = [], False
    for decl in body.split(';'):
        if ':' not in decl:
            out.append(decl)
            continue
        prop, val = decl.split(':', 1)
        p = re.sub(r'/\*.*?\*/', '', prop, flags=re.S).strip().lower()
        new = None
        if p == 'color':
            if all(is_link(x) for x in sels):
                new = ' #fff080' if hover_only else ' #e0ffe0'
            else:
                new = recolour(val, 'text')
        elif p in ('background', 'background-color', 'background-image'):
            if hover_only and NO_HOVER_BG.search(selectors):
                changed = True
                continue
            new = recolour(val, 'bg')
        elif p.startswith('border') and not any(w in p for w in ('radius', 'width', 'style', 'collapse', 'spacing')):
            new = recolour(val, 'border')
        elif p in ('text-shadow', 'box-shadow') and val.strip() != 'none':
            new = ' none' if p == 'text-shadow' else recolour(val, 'border')
        if new is not None and new != val:
            out.append(prop + ':' + new)
            changed = True
        else:
            out.append(decl)
    return ';'.join(out) if changed else None


RULE = re.compile(r'([^{}]+)\{([^{}]*)\}')


def rewrite(css):
    count = 0

    def sub(m):
        nonlocal count
        sel, body = m.group(1), m.group(2)
        head = sel.strip()
        if head.startswith('@') and not head.startswith('@media'):
            return m.group(0)
        new = convert(re.sub(r'/\*.*?\*/', '', sel, flags=re.S), body)
        if new is None:
            return m.group(0)
        count += 1
        return sel + '{' + new + '}'
    return RULE.sub(sub, css), count


def main():
    for src, dst in SOURCES.items():
        with open(os.path.join(ROOT, 'default', 'css', src)) as f:
            css, n = rewrite(f.read())
        banner = '/* GENERATED by bin/helios-buildcss.py from Themes/default/css/%s - edit helios.css instead. */\n' % src
        with open(os.path.join(OUT_DIR, dst), 'w') as f:
            f.write(banner + css + '\n' + EXTRA.get(src, ''))
        print('%-28s -> %-28s %d rules recoloured' % (src, dst, n))


if __name__ == '__main__':
    main()
