@echo off
cd /d "%~dp0"
echo Instalando dependencias...
call npm install
if errorlevel 1 (
    echo Error: npm no encontrado. Instala Node.js desde https://nodejs.org y vuelve a ejecutar.
    pause
    exit /b 1
)
echo.
echo Compilando assets (Tailwind + Vite)...
call npm run build
if errorlevel 1 (
    echo Error al compilar.
    pause
    exit /b 1
)
echo.
echo Listo. Recarga la pagina de login en el navegador.
pause
