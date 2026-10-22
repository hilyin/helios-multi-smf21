#!/bin/sh
# Build dist/HeliosMulti21.zip for SMF's "Install a new theme -> From a file".
# SMF names the theme folder after the zip and extracts its contents into it, so files go at the zip root.
set -e
cd "$(dirname "$0")/.."
mkdir -p dist
rm -f dist/HeliosMulti21.zip
python3 - <<'PY'
import os, zipfile
src = 'HeliosMulti21'
with zipfile.ZipFile('dist/HeliosMulti21.zip', 'w', zipfile.ZIP_DEFLATED) as z:
    for root, dirs, files in os.walk(src):
        dirs.sort()
        for f in sorted(files):
            if f.startswith('minified_'):
                continue
            p = os.path.join(root, f)
            z.write(p, os.path.relpath(p, src))
print('dist/HeliosMulti21.zip:', len(zipfile.ZipFile('dist/HeliosMulti21.zip').namelist()), 'files')
PY
