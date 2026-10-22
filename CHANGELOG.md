# Changelog

## 2.1.0 (2026-10-22): SMF 2.1 port

First release for SMF 2.1, ported from Helios Multi 2.0 for SMF 2.0 (Bloc, 2011; theme id
`bloc:heliosmulti`). Tested on SMF 2.1.8 and PHP 8.3.

### Ported from 2.0
- **Layout rebuilt on SMF 2.1's markup.** The templates are based on 2.1's default theme, so 2.1
  features keep working: popups, alerts, likes, the responsive layout and the mobile menu.
- **Self-contained stylesheets.** `tools/helios-buildcss.py` generates dark copies of 2.1's
  default stylesheets:
  - dark text becomes light, and light backgrounds and borders become dark
  - text shadows and row-hover highlights are removed
  - links use the Helios colours

  `css/helios.css` adds the Helios layout on top.
- **Colour variants as SMF 2.1 theme variants:** blue, brown, golden, green, red and silver
  (`css/index_<colour>.css`).
- **Pod frames and strips:** the framed boxes and strips were stitched into PNGs from the 2.0
  theme's GIF slices.
- **Full image set:** 2.1's images, with the 2.0 theme's message, topic and group icons converted
  to PNG under 2.1's file names.
- **2.0 behaviour restored:**
  - Header shows the avatar and greeting, instead of 2.1's user and alert dropdowns and header search.
  - Main menu brings back Help, Profile and My Messages, in the 2.0 order.
  - Collapse arrow for the header.
  - ` | `-separated link tree.
  - Left-hand sidebar menu on admin, profile and personal-message pages.
  - Big red "Upcoming Events:" line above the board index.

### New in the port
- **Menu bar at narrow widths:** the menu wraps onto a second row inside the menu box from 561px
  up, instead of spilling over the content below. The ` | ` separators sit between items only,
  never at the start or end of a row, and the ≤560px mobile popup has none.
- **Upcoming Events line:** only today's events are large and underlined, as in 2.0. Event links
  stay red after being visited, and the edit link is a ⚔️ icon, sized up for today's events.
- **Theme settings:**
  - an optional extra main-menu link (text and URL) for logged-in members
  - which membergroups see the Upcoming Events line
- **Icons:** links the forum's `favicon.png` and `apple-touch-icon.png` if they exist in the forum root.
- **Credits:** Bloc's copyright notice and `licence.txt` kept, and the footer credit links to it.
