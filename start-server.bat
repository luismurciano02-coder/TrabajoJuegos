@echo off
REM Iniciar el servidor Symfony en localhost:8000

cd /d "%~dp0"

echo.
echo ========================================
echo     GameCenter - Servidor Iniciando
echo ========================================
echo.
echo Puerto: 8000
echo URL: http://localhost:8000
echo.
echo Presiona CTRL+C para detener el servidor
echo.

php -S localhost:8000 -t public

pause
