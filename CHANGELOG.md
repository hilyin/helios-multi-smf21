# Changelog

## 2.1.5 (2026-10-26)

### Fixed
- **Footer on phones:** the footer lines are centered instead of right-aligned, and the footer text
  sits vertically centered in its bar.

## 2.1.4 (2026-10-26)

### Fixed
- **iPhone zoomed in on page load:** Safari focused the header login's username field as the page
  loaded and zoomed in. The viewport tag now has `maximum-scale=1`, which stops that.

## 2.1.3 (2026-10-25)

### Fixed
- **Header login spacing:** the Log in button now has the same gap after the password field as
  the one between the username and password fields.

## 2.1.2 (2026-10-25)

### Fixed
- **Header login after using the login popup:** the header form failed with "Token verification
  failed" if the login popup (or a login page in another tab) had been opened first, because SMF
  keeps only one login token per visitor. The form now fetches a fresh token the same way the popup
  does just before it submits.

## 2.1.1 (2026-10-25)

### Removed
- **Upcoming Events line** above the board index, and its theme setting. It was a forum-specific
  addition in the 2.0 theme this port was taken from, not part of Bloc's Helios Multi.

### 2.0 behaviour restored
- **Forum Stats** box in the Info Center has a "[More Stats]" link again.
- **Guest login in the header:** username and password boxes, a Log in button and "Stay logged in"
  (ticked by default) under the welcome line.
- **Calendar menu button** is a plain link, without 2.1's hover sub-menu. "Post Event" is still on
  the calendar page.

### Fixed
- **Calendar grid:** days no longer alternate in a checkerboard (2.1's `.windowbg:nth-of-type`
  striping hit the day cells), and every day border is the same colour.

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

### New in the port
- **Menu bar at narrow widths:** the menu wraps onto a second row inside the menu box from 561px
  up, instead of spilling over the content below. The ` | ` separators sit between items only,
  never at the start or end of a row, and the ≤560px mobile popup has none.
- **Theme settings:** an optional extra main-menu link (text and URL) for logged-in members.
- **Icons:** links the forum's `favicon.png` and `apple-touch-icon.png` if they exist in the forum root.
- **Credits:** Bloc's copyright notice and `licence.txt` kept, and the footer credit links to it.
