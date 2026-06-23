#!/usr/bin/env bash
# ConsentKit — packaging del plugin WordPress installabile.
# Produce dist/consentkit/ (cartella da caricare via FTP) e dist/consentkit.zip
# (da caricare da Plugin → Aggiungi nuovo → Carica plugin).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST="$ROOT/dist"
PLUGIN="$DIST/consentkit"

# 1) Assicura che il core sia copiato dentro l'adattatore WordPress.
bash "$ROOT/tools/build.sh"

# 2) Assembla la cartella del plugin (nome cartella = slug = consentkit).
rm -rf "$DIST"
mkdir -p "$PLUGIN"
cp -r "$ROOT/packages/wordpress/." "$PLUGIN/"
# include la licenza a livello plugin
cp "$ROOT/LICENSE" "$PLUGIN/LICENSE"

# 3) Crea lo zip (zip se disponibile, altrimenti lo fa PowerShell a parte).
cd "$DIST"
if command -v zip >/dev/null; then
  zip -rq consentkit.zip consentkit
  echo "  ✓ dist/consentkit.zip creato"
else
  echo "  ! 'zip' non disponibile: usa PowerShell -> Compress-Archive (vedi istruzioni)"
fi

echo "Pacchetto pronto in: $PLUGIN"
