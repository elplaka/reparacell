@echo off
cd C:\Users\Angel SD\Desktop\www\sistemas\social
echo Iniciando el servidor Laravel...
start cmd /k php artisan serve
echo Abriendo el explorador...
start http://127.0.0.1:8000/home