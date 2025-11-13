# 🚀 Despliegue en EC2: Guía Completa

## 📋 Resumen Rápido

Este proyecto Laravel 12 con Jetstream + Inertia puede desplegarse en una instancia EC2 de Amazon Linux de dos formas:

1. **Automática (Recomendado):** Ejecutar `deploy.sh` - instala todo en ~10 minutos
2. **Manual:** Seguir pasos en `DEPLOY_EC2_AMAZON_LINUX.md` - control total

---

## 📦 Versiones del Proyecto

```
PHP:                ^8.2
Laravel:            ^12.0
Jetstream:          ^5.3
Inertia (Laravel):  ^2.0
Vue.js:             ^3.3.13
Vite:               ^7.0.7
Node.js:            20+ (recomendado)
PostgreSQL:         12+ (recomendado)
```

---

## 🏁 Inicio Rápido (Despliegue Automático)

### Paso 1: Crear Instancia EC2
1. Ir a AWS Console → EC2 → Instancias
2. Lanzar instancia:
   - **AMI:** Amazon Linux 2023 o 2
   - **Tipo:** t3.medium o superior (t2.medium desarrollo)
   - **Storage:** 30GB SSD
   - **Security Group:** Puertos 80, 443, 22 abiertos

3. Descargar par de claves SSH

### Paso 2: Conectar a la Instancia
```bash
chmod 400 tu-clave.pem
ssh -i tu-clave.pem ec2-user@TU-IP-EC2
```

### Paso 3: Descargar y Ejecutar Script
```bash
# Opción A: Descargar desde GitHub (recomendado)
wget https://raw.githubusercontent.com/jacintoperez072-cyber/Examen2/master/deploy.sh
chmod +x deploy.sh

# Opción B: Crear archivo localmente y copiar via SCP
scp -i tu-clave.pem deploy.sh ec2-user@TU-IP-EC2:~/
```

### Paso 4: Ejecutar Despliegue
```bash
./deploy.sh https://github.com/jacintoperez072-cyber/Examen2.git \
            /home/ec2-user/Examen2 \
            tu_password_postgresql \
            http://tu-ip-ec2
```

El script te pedirá confirmación. Presiona `s` para continuar.

**⏱️ Tiempo esperado:** 10-15 minutos

### Paso 5: Verificar que Funciona
```bash
# Abrir navegador
http://TU-IP-EC2

# O verificar desde SSH
curl http://localhost
```

---

## 📖 Archivos Incluidos

| Archivo | Descripción |
|---------|------------|
| **deploy.sh** | Script bash automatizado para despliegue completo |
| **DEPLOY_EC2_AMAZON_LINUX.md** | Guía manual paso a paso (15 secciones) |
| **DEPLOY_CHECKLIST.md** | Checklist pre/post despliegue y troubleshooting |
| **README_DESPLIEGUE.md** | Este archivo |

---

## 🔧 Despliegue Manual (Si lo Prefieres)

Si no quieres usar el script automático, sigue estos pasos:

```bash
# 1. Actualizar sistema
sudo yum update -y

# 2. Instalar PHP 8.2
sudo amazon-linux-extras install php8.2 -y
sudo yum install -y php8.2-{fpm,cli,pgsql,mbstring,xml,zip,curl}

# 3. Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 4. Instalar Node.js
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
source ~/.bashrc
nvm install 20

# 5. Instalar PostgreSQL
sudo yum install -y postgresql15-server
sudo systemctl start postgresql
sudo systemctl enable postgresql

# 6. Clonar proyecto
git clone https://github.com/jacintoperez072-cyber/Examen2.git
cd Examen2

# 7. Instalar dependencias
composer install --no-dev
npm install && npm run build

# 8. Configurar .env
cp .env.example .env
# Edita .env con tu BD, APP_URL, etc.

# 9. Generar clave
php artisan key:generate

# 10. Migraciones
php artisan migrate --force

# 11. Instalar Nginx y configurar
sudo yum install -y nginx
# (Ver DEPLOY_EC2_AMAZON_LINUX.md para config Nginx)

# 12. Iniciar servicios
sudo systemctl start nginx php-fpm postgresql
```

**Documento completo:** `DEPLOY_EC2_AMAZON_LINUX.md`

---

## ✅ Verificación Post-Despliegue

```bash
# 1. Verificar servicios corriendo
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status postgresql

# 2. Verificar conectividad a BD
cd /home/ec2-user/Examen2
php artisan tinker
>>> DB::connection()->getPdo();  # Debe retornar objeto PDO
>>> exit

# 3. Ver logs si hay error
tail -f /home/ec2-user/Examen2/storage/logs/laravel.log
sudo tail -f /var/log/nginx/examen2-error.log

# 4. Health check
php artisan health

# 5. Acceder desde navegador
http://TU-IP-EC2
```

---

## 🔒 Configuración HTTPS (Opcional)

Si tienes un dominio, instala certificado SSL con Let's Encrypt:

```bash
sudo yum install -y certbot python3-certbot-nginx

# Obtener certificado
sudo certbot certonly --nginx -d tu-dominio.com

# Habilitar auto-renovación
sudo systemctl enable certbot-renew.timer
sudo systemctl start certbot-renew.timer

# Luego actualiza Nginx para redirigir HTTP → HTTPS
```

Ver detalles en `DEPLOY_EC2_AMAZON_LINUX.md` (Sección: Configuración HTTPS)

---

## 📝 Variables de Entorno (.env) Importantes

```bash
# Aplicación
APP_NAME=Examen2
APP_ENV=production
APP_DEBUG=false
APP_URL=http://tu-ip-o-dominio

# Base de Datos
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=examen2
DB_USERNAME=laravel
DB_PASSWORD=tu_password_segura

# Sesión
SESSION_DRIVER=database

# Cache
CACHE_DRIVER=database

# Queue
QUEUE_CONNECTION=database
```

---

## 🚨 Troubleshooting

### "404 Not Found"
- Verifica Nginx está corriendo: `sudo systemctl status nginx`
- Revisa logs: `sudo tail -f /var/log/nginx/examen2-error.log`
- Confirma que PHP-FPM está corriendo: `sudo systemctl status php-fpm`

### "CSS/JS en blanco" (Assets no cargan)
```bash
cd /home/ec2-user/Examen2
npm run build
sudo systemctl restart nginx
```

### "Database connection refused"
```bash
# Verificar PostgreSQL
sudo systemctl status postgresql

# Verificar credenciales en .env
cat .env | grep DB_

# Verificar conexión
cd /home/ec2-user/Examen2
php artisan tinker
>>> DB::connection()->getPdo();
```

### "Permission denied" en storage
```bash
sudo chown -R nginx:nginx /home/ec2-user/Examen2
sudo chmod -R 775 /home/ec2-user/Examen2/storage
```

**Más troubleshooting:** Ver `DEPLOY_CHECKLIST.md`

---

## 🔄 Actualizar Proyecto (Después de cambios en GitHub)

```bash
cd /home/ec2-user/Examen2

# Obtener cambios
git pull origin master

# Actualizar dependencias si es necesario
composer update --no-dev

# Compilar assets si hay cambios
npm install && npm run build

# Ejecutar nuevas migraciones
php artisan migrate --force

# Limpiar y recompilar caché
php artisan optimize:clear
php artisan optimize

# Reiniciar servicios
sudo systemctl restart nginx php-fpm
```

---

## 📊 Monitoreo y Logs

```bash
# Logs de Laravel (tiempo real)
tail -f /home/ec2-user/Examen2/storage/logs/laravel.log

# Logs de Nginx (error)
sudo tail -f /var/log/nginx/examen2-error.log

# Logs de Nginx (acceso)
sudo tail -f /var/log/nginx/examen2-access.log

# Logs de PHP-FPM
sudo tail -f /var/log/php-fpm/www-error.log
```

---

## 🛠️ Comandos Útiles

```bash
# Entrar a Tinker (CLI de Laravel)
cd /home/ec2-user/Examen2 && php artisan tinker

# Ver rutas
php artisan route:list

# Ver migraciones pendientes
php artisan migrate:status

# Ejecutar seeder
php artisan db:seed

# Reiniciar cola (si usas jobs)
php artisan queue:restart

# Clearar todos los cachés
php artisan optimize:clear

# Compilar (es equivalente a varios comandos)
php artisan optimize
```

---

## 💾 Backup

**Script de backup automático:**

```bash
#!/bin/bash
# backup.sh

BACKUP_DIR="/home/ec2-user/backups"
mkdir -p $BACKUP_DIR

# Backup de BD
sudo -u postgres pg_dump examen2 | gzip > $BACKUP_DIR/db_$(date +%Y%m%d_%H%M%S).sql.gz

# Backup de proyecto
tar -czf $BACKUP_DIR/project_$(date +%Y%m%d_%H%M%S).tar.gz /home/ec2-user/Examen2

# Mantener solo últimos 7 backups
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completado: $(date)"
```

**Ejecutar periódicamente con cron:**
```bash
crontab -e
# Añadir: 0 2 * * * /home/ec2-user/backup.sh  # Diario a las 2 AM
```

---

## 📞 Ayuda y Documentación

- **Documentación Laravel:** https://laravel.com/docs/12
- **Documentación Jetstream:** https://jetstream.laravel.com
- **Documentación Inertia:** https://inertiajs.com
- **AWS EC2:** https://docs.aws.amazon.com/ec2/

---

## ✨ Resumen Final

| Paso | Comando |
|------|---------|
| Conectar a EC2 | `ssh -i tu-clave.pem ec2-user@TU-IP-EC2` |
| Descargar script | `wget https://raw.githubusercontent.com/jacintoperez072-cyber/Examen2/master/deploy.sh` |
| Hacer ejecutable | `chmod +x deploy.sh` |
| Ejecutar despliegue | `./deploy.sh [REPO] [PATH] [PASSWORD] [URL]` |
| Verificar | `http://TU-IP-EC2` |
| Ver logs | `tail -f /home/ec2-user/Examen2/storage/logs/laravel.log` |

---

**¡Listo! Tu aplicación debería estar en línea en ~15 minutos.** 🎉

Si encuentras problemas, revisa `DEPLOY_CHECKLIST.md` o contacta con soporte.
