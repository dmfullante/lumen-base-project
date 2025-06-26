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
- Build and the Image and Run the `containers` to docker
```bash
  user@ubuntu:~/$: git clone https://github.com/dmfullante/lumen-base-project.git
  user@ubuntu:~/: cd lumen-base-project
  user@ubuntu:~/lumen-base-project$: cp .env.example .env
  user@ubuntu:~/lumen-base-project$: composer install
  user@ubuntu:~/lumen-base-project$: docker-compose build
  user@ubuntu:~/lumen-base-project$: docker-compose up -d
  user@ubuntu:~/lumen-base-project$: docker exec -it lumen-base-project-lumen-1 bash
  root@2d571ff4afae:/var/www/html# composer dump-autoload
  root@2d571ff4afae:/var/www/html# php artisan migrate --seed
```

- App Endpoint [http://localhost:5177](http://localhost:5177)

