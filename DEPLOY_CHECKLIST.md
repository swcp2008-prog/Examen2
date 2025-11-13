# Pre-Despliegue Checklist: Laravel Jetstream + Inertia en EC2

## ✅ Preparación Local (Antes de subir a EC2)

- [ ] Asegurar que el código está commiteado y pusheado a GitHub
  ```bash
  git status
  git push origin master
  ```

- [ ] Verificar que `.env.example` está actualizado (sin valores sensibles)
  ```bash
  # Nunca comitear el .env real
  git status
  ```

- [ ] Compilar assets localmente (opcional, pero recomendado para verificar)
  ```bash
  npm install
  npm run build
  ```

- [ ] Verificar compatibilidad de versiones
  - PHP: `composer require --dry-run` (verificar no hay conflictos)
  - Node: `npm install --dry-run`

- [ ] Crear archivo `.env` de ejemplo con estructura correcta
  ```bash
  cp .env.example .env.production
  # Editarlo con placeholders en lugar de valores reales
  ```

---

## ✅ Preparación de la Instancia EC2

- [ ] Crear instancia EC2 (Amazon Linux 2023 o 2)
  - Tipo: `t3.medium` o superior
  - Storage: 30GB SSD
  - Security Group: Puertos 80, 443, 22 abiertos

- [ ] Obtener par de claves SSH y permisos correctos
  ```bash
  chmod 400 tu-clave.pem
  ```

- [ ] Conectarse a la instancia
  ```bash
  ssh -i tu-clave.pem ec2-user@TU-IP-EC2
  ```

- [ ] Verificar SO (debe ser Amazon Linux)
  ```bash
  cat /etc/os-release
  ```

---

## ✅ Despliegue Automático (Recomendado)

- [ ] Descargar el script `deploy.sh` a la instancia o copiar su contenido
  ```bash
  # Opción 1: Crear localmente y transferir
  scp -i tu-clave.pem deploy.sh ec2-user@TU-IP-EC2:~/

  # Opción 2: Crear directamente en EC2
  nano deploy.sh
  # (pegar contenido)
  ```

- [ ] Hacer el script ejecutable
  ```bash
  chmod +x deploy.sh
  ```

- [ ] Ejecutar el script
  ```bash
  ./deploy.sh https://github.com/jacintoperez072-cyber/Examen2.git \
              /home/ec2-user/Examen2 \
              TU_PASSWORD_DB \
              http://TU-IP-EC2
  ```

- [ ] Esperar a que termine y revisar mensajes de error

---

## ✅ Despliegue Manual (Si es necesario)

Si prefieres hacer paso a paso, sigue la guía `DEPLOY_EC2_AMAZON_LINUX.md`:

1. **Actualizar sistema**
   ```bash
   sudo yum update -y
   ```

2. **Instalar PHP 8.2, Composer, Node.js**
   ```bash
   sudo amazon-linux-extras install php8.2 -y
   # ... (ver documento)
   ```

3. **Instalar y configurar PostgreSQL**
   ```bash
   sudo yum install -y postgresql15-server
   # ...
   ```

4. **Clonar repositorio**
   ```bash
   git clone https://github.com/jacintoperez072-cyber/Examen2.git
   cd Examen2
   ```

5. **Instalar dependencias**
   ```bash
   composer install
   npm install
   npm run build
   ```

6. **Configurar .env y clave**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

7. **Migraciones**
   ```bash
   php artisan migrate --force
   ```

8. **Configurar Nginx**
   ```bash
   # (ver documento)
   ```

9. **Iniciar servicios**
   ```bash
   sudo systemctl start nginx
   sudo systemctl start php-fpm
   ```

---

## ✅ Verificaciones Post-Despliegue

**Conectarte nuevamente a EC2:**
```bash
ssh -i tu-clave.pem ec2-user@TU-IP-EC2
```

**Verificar servicios activos:**
```bash
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status postgresql
```

**Verificar conectividad a BD:**
```bash
cd /home/ec2-user/Examen2
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit
```

**Verificar logs:**
```bash
# Nginx errors
sudo tail -f /var/log/nginx/examen2-error.log

# Laravel logs
tail -f /home/ec2-user/Examen2/storage/logs/laravel.log
```

**Probar aplicación:**
- Abre navegador: `http://TU-IP-EC2`
- Verifica que carga sin errores
- Prueba login/logout
- Verifica assets (CSS, JS) cargan correctamente

**Health check de Laravel:**
```bash
cd /home/ec2-user/Examen2
php artisan health
```

---

## ✅ Configuración HTTPS (Opcional pero Recomendado)

**Si tienes un dominio, configura HTTPS con Let's Encrypt:**

```bash
sudo yum install -y certbot python3-certbot-nginx

# Obtener certificado
sudo certbot certonly --nginx -d tu-dominio.com

# Automatizar renovación
sudo systemctl enable certbot-renew.timer
sudo systemctl start certbot-renew.timer
```

**Luego actualiza `/etc/nginx/conf.d/examen2.conf` para redirigir HTTP a HTTPS.**

---

## ✅ Optimizaciones Post-Despliegue

**Optimizar aplicación Laravel:**
```bash
cd /home/ec2-user/Examen2

# Compilar rutas, caché, etc.
php artisan optimize

# Precargar archivos (PHP 7.4+)
php artisan route:cache
php artisan view:cache
php artisan config:cache

# Ejecutar queue workers (si usas colas)
php artisan queue:work &
```

**Configurar logs rotativos:**
```bash
# Laravel lo hace automáticamente (ver config/logging.php)
# Verificar que storage/logs está limpio periódicamente
```

**Configurar Cron Job para Laravel Scheduler (si es necesario):**
```bash
# Editar crontab
crontab -e

# Añadir esta línea para ejecutar schedule cada minuto
* * * * * cd /home/ec2-user/Examen2 && php artisan schedule:run >> /dev/null 2>&1
```

---

## ✅ Backup y Mantenimiento

**Crear scripts de backup:**
```bash
# Backup de BD
sudo -u postgres pg_dump examen2 > /home/ec2-user/backups/db_$(date +%Y%m%d).sql

# Backup de proyecto
tar -czf /home/ec2-user/backups/project_$(date +%Y%m%d).tar.gz /home/ec2-user/Examen2
```

**Mantener actualizado:**
```bash
# Pull cambios
cd /home/ec2-user/Examen2
git pull origin master

# Actualizar dependencias PHP
composer update --no-dev

# Ejecutar migraciones nuevas
php artisan migrate --force

# Recompilar assets si hay cambios
npm install && npm run build

# Limpiar caché
php artisan optimize:clear
php artisan optimize
```

---

## 🚨 Troubleshooting Común

| Problema | Causa Probable | Solución |
|----------|----------------|----------|
| **404 en acceso a app** | Nginx no sirve correctamente | Ver logs: `sudo tail -f /var/log/nginx/examen2-error.log` |
| **CSS/JS no carga (vacío)** | Assets no compilados | `npm run build` en el proyecto |
| **Error de BD** | Credenciales incorrectas | Verificar `.env` y PostgreSQL corriendo |
| **Permission denied en storage** | Permisos incorrectos | `sudo chown -R nginx:nginx /home/ec2-user/Examen2` |
| **500 Internal Server Error** | Error en aplicación | `tail -f /home/ec2-user/Examen2/storage/logs/laravel.log` |
| **PHP-FPM no inicia** | Puerto en uso o error de config | `sudo systemctl status php-fpm -l` |

---

## 📞 Contacto y Soporte

Si tienes dudas, revisa:
1. Los logs (primer paso siempre)
2. El documento `DEPLOY_EC2_AMAZON_LINUX.md`
3. La documentación oficial de Laravel: https://laravel.com/docs/12
4. Troubleshooting de Jetstream: https://jetstream.laravel.com/introduction.html
