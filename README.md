# Lumen Microservice Starter

This is a **Lumen-based** microservice project — a lightweight micro-framework by Laravel — designed for performance and rapid development.

## Features

- ✅ **Authentication via PIN**
- 📧 **Email sending** using Laravel Mail
- 🧵 **Queueable Jobs & Listeners** for background tasks
- 🐳 **Docker Ready** environment for containerization
- 🛡️ **Role & Permission Management** using [Spatie Laravel-Permission](https://github.com/spatie/laravel-permission)

---

### Setup For Dev/Local Machine Environment
- Ensure you have .env.local on your directory
```bash
  user@ubuntu:~/$: git clone git@github.com:dmfullante/lumen-base-project.git
  user@ubuntu:~/: cd ms-cardpayment-api
  user@ubuntu:~/ms-cardpayment-api$: cp .env.example .env.local
  user@ubuntu:~/ms-cardpayment-api$: composer install
```
- Build the Project using Docker Compose
```bash
  user@ubuntu:~/ms-cardpayment-api$: docker-compose -f docker-compose.local.yml build
  user@ubuntu:~/ms-cardpayment-api$: docker-compose -f docker-compose.local.yml up -d
```
- Shortcut Command Build And Deploy
```bash
  user@ubuntu:~/ms-cardpayment-api$: docker-compose -f docker-compose.local.yml up -d --build
```
- Enter to Docker Container and execute dumpo autoload
```bash
  user@ubuntu:~/ms-cardpayment-api$: docker exec -it lumen-base-project-lumen-1 bash
  root@2d571ff4afae:/var/www/html# composer dump-autoload
  root@2d571ff4afae:/var/www/html# php artisan migrate --seed
```

- App Endpoint [http://localhost:5177](http://localhost:5177)

