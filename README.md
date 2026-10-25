# Helios Multi for SMF 2.1

A port of **Helios Multi**, the dark, multi-colour SMF 2.0 theme by **Bloc** (© 2011 Blocweb), to
[Simple Machines Forum](https://www.simplemachines.org) 2.1.

It keeps the 2.0 look and layout (framed "pods", the stats strip, the capped menu bar, the colour
variants) on top of SMF 2.1's markup, so 2.1 features like popups, alerts, likes and the responsive
layout keep working.

- Tested on SMF 2.1.8 with PHP 8.3.
- Six colour variants: blue, brown, golden, green, red and silver. Members choose one when they
  pick the theme, and admins set the default.
- Self-contained: the theme ships its own dark copies of SMF 2.1's stylesheets and a full image set.

## Install

1. Download `HeliosMulti21.zip` from the [Releases](https://github.com/hilyin/helios-multi-smf21/releases) page.
   Keep that file name: SMF names the theme folder after the zip.
2. In your forum: **Admin → Configuration → Themes and Layout → Install a new theme → From a file**,
   choose the zip, then **Install**.
3. Make it the default theme (or let members pick it) under **Themes and Layout → Manage and Install**.

Or copy the `HeliosMulti21` folder into your forum's `Themes/` directory and use
**Install a new theme → From a directory** with that path.

## Theme settings

Under **Themes and Layout → Theme settings → Helios Multi**, along with SMF's usual settings:

| Setting | What it does |
|---|---|
| Helios: extra menu link text / URL | Adds one custom link to the main menu after Calendar, for logged-in members. For example, a guild DKP or attendance site. Leave either one empty to hide it. |

If your forum root has a `favicon.png` or an `apple-touch-icon.png`, the theme links them automatically.

## After an SMF update

The theme's stylesheets in `css/` (all except `helios.css` and `index_<colour>.css`) are
**generated** dark copies of `Themes/default/css`. If an SMF update changes the default
stylesheets, regenerate them:

```sh
python3 tools/helios-buildcss.py /path/to/forum/Themes HeliosMulti21
```

Make your own CSS changes in `css/helios.css` (layout) or `css/index_<colour>.css` (per-colour
images) so they survive regenerating.

## Credits and license

- **Helios Multi** design and graphics: © 2011 Blocweb (Bloc). The original BSD-style license is in
  [`HeliosMulti21/licence.txt`](HeliosMulti21/licence.txt) and must stay with the theme.
- Templates and generated stylesheets are based on SMF 2.1's default theme: © Simple Machines,
  BSD license ([`LICENSE-SMF.txt`](LICENSE-SMF.txt)).
- SMF 2.1 port: hilyin, 2026.

See [`LICENSE.md`](LICENSE.md) for details, and [`CHANGELOG.md`](CHANGELOG.md) for what changed from the 2.0 theme.

This port isn't affiliated with or endorsed by Bloc/Blocweb or Simple Machines.
