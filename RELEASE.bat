@echo off
TITLE Git Release Helper
cd /d "%~dp0"

echo ============================================================
echo  RELEASE - Pubblicazione Nuova Versione su GitHub
echo ============================================================
echo.
echo Versioni esistenti:
git tag --sort=-version:refname
echo.
set /p VER="Versione da rilasciare (es. v1.2.0): "
set /p MSG="Descrizione breve della release: "
echo.

echo [1/3] Creazione tag %VER%...
git tag -a %VER% -m "%VER%: %MSG%"
if errorlevel 1 (
    echo ERRORE: tag non creato. Verifica che la versione non esista gia'.
    pause
    exit /b 1
)

echo [2/3] Push tag su GitHub...
git push origin %VER%
if errorlevel 1 (
    echo ERRORE: push fallito. Verifica la connessione e i permessi.
    pause
    exit /b 1
)

echo [3/3] Apertura GitHub per completare le note di rilascio...
for /f %%i in ('git remote get-url origin') do set REPO_URL=%%i
set REPO_URL=%REPO_URL:https://github.com/=%
set REPO_URL=%REPO_URL:.git=%
start https://github.com/%REPO_URL%/releases/new?tag=%VER%

echo.
echo ============================================================
echo  Tag %VER% pubblicato! Completa le note su GitHub.
echo ============================================================
pause
