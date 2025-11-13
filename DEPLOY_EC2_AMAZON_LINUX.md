# 🚀 Guía de Despliegue: Laravel Jetstream + Inertia en EC2 (Amazon Linux)

## 📋 Versiones del Proyecto

Tu proyecto está configurado con las siguientes versiones:

| Componente | Versión | Requisito Mínimo |
|------------|---------|-----------------|
| **PHP** | ^8.2 | 8.2+ |
| **Laravel** | ^12.0 | 12.0+ |
| **Jetstream** | ^5.3 | 5.3+ |
| **Inertia Laravel** | ^2.0 | 2.0+ |
| **Vue.js** | ^3.3.13 | 3.3+ |
| **Vite** | ^7.0.7 | 7.0+ |
| **PostgreSQL** | (recomendado) | 12+ |
| **Node.js** | (recomendado) | 20+ |
| **npm** | (recomendado) | 10+ |

---

## 🏗️ Requisitos de la Instancia EC2

### Especificaciones Recomendadas
- **OS**: Amazon Linux 2023 o 2
- **Tipo de Instancia**: t3.medium o superior (t2.medium para desarrollo)
- **Storage**: 30GB SSD mínimo
- **Memory**: 2GB mínimo (4GB recomendado)
- **vCPU**: 2 mínimo

### Puertos a Abrir en Security Group
- **80** (HTTP)
- **443** (HTTPS)
- **22** (SSH)
- **5432** (PostgreSQL si está en la misma instancia)

---

## 🔧 Instalación Manual (Paso a Paso)

### 1. Conectarse a la Instancia EC2
```bash
ssh -i tu-clave.pem ec2-user@tu-ip-ec2
```

### 2. Actualizar el Sistema
```bash
sudo yum update -y
sudo yum install -y git curl wget
```

### 3. Instalar PHP 8.2 y Extensiones Necesarias
```bash
# Agregar repositorio Amazon Linux Extras
sudo amazon-linux-extras install php8.2 -y

# Instalar extensiones PHP requeridas
sudo yum install -y php8.2-{fpm,cli,gd,pdo,pgsql,mbstring,json,xml,zip,curl,bcmath}

# Verificar PHP
php -v
```

### 4. Instalar Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### 5. Instalar Node.js y npm
```bash
# Usar NVM (recomendado) o descargar directamente
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
source ~/.bashrc
nvm install 20
nvm use 20

# Verificar
node -v
npm -v
```

### 6. Instalar PostgreSQL (o MySQL)
```bash
# PostgreSQL
sudo yum install -y postgresql15-server postgresql15-contrib postgresql15-devel

# Iniciar servicio
sudo systemctl start postgresql
sudo systemctl enable postgresql

# Crear usuario y base de datos (ejecutar como usuario postgres)
sudo -u postgres psql -c "CREATE USER laravel WITH PASSWORD 'tu_password';"
sudo -u postgres psql -c "CREATE DATABASE examen2 OWNER laravel;"
```

### 7. Instalar Nginx (Recomendado)
```bash
sudo yum install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### 8. Clonar el Repositorio
```bash
cd /home/ec2-user
git clone https://github.com/jacintoperez072-cyber/Examen2.git
cd Examen2
```

### 9. Instalar Dependencias PHP
```bash
composer install --optimize-autoloader --no-dev
```

### 10. Configurar Variables de Entorno
```bash
cp .env.example .env

# Editar .env
nano .env

# O si prefieres usar sed (reemplaza los valores):
sed -i 's|DB_CONNECTION=.*|DB_CONNECTION=pgsql|' .env
sed -i 's|DB_HOST=.*|DB_HOST=localhost|' .env
sed -i 's|DB_PORT=.*|DB_PORT=5432|' .env
sed -i 's|DB_DATABASE=.*|DB_DATABASE=examen2|' .env
sed -i 's|DB_USERNAME=.*|DB_USERNAME=laravel|' .env
sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=tu_password|' .env
sed -i 's|APP_URL=.*|APP_URL=http://tu-dominio-o-ip|' .env
```

### 11. Generar Clave de Aplicación
```bash
php artisan key:generate
```

### 12. Ejecutar Migraciones
```bash
php artisan migrate --force
```

### 13. Instalar Dependencias Node y Compilar Assets
```bash
npm install
npm run build
```

### 14. Permisos y Ownership
```bash
sudo chown -R nginx:nginx /home/ec2-user/Examen2
sudo chmod -R 775 /home/ec2-user/Examen2/storage
sudo chmod -R 775 /home/ec2-user/Examen2/bootstrap/cache
```

### 15. Configurar Nginx
```bash
sudo nano /etc/nginx/conf.d/examen2.conf
```

Pega el siguiente contenido:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name _;  # O tu dominio

    root /home/ec2-user/Examen2/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }

    client_max_body_size 50M;
}
```

### 16. Reiniciar Nginx y PHP-FPM
```bash
sudo systemctl restart nginx
sudo systemctl restart php-fpm
```

### 17. Verificar Estado
```bash
# Verificar que Nginx está corriendo
sudo systemctl status nginx

# Verificar que PHP-FPM está corriendo
sudo systemctl status php-fpm

# Probar conectividad a base de datos
php artisan tinker
>>> \DB::connection()->getPdo();
```

---

## 🤖 Script de Automatización (Bash)

Crea un archivo `deploy.sh` en tu instancia EC2:

```bash
#!/bin/bash

# Script de despliegue automático para Laravel Jetstream + Inertia en EC2

set -e  # Exit on error

echo "=== Iniciando despliegue en EC2 ==="

# Variables
REPO_URL="https://github.com/jacintoperez072-cyber/Examen2.git"
PROJECT_PATH="/home/ec2-user/Examen2"
DB_HOST="localhost"
DB_PORT="5432"
DB_NAME="examen2"
DB_USER="laravel"
DB_PASS="tu_password"
APP_URL="http://tu-dominio-o-ip"

# 1. Actualizar sistema
echo "📦 Actualizando sistema..."
sudo yum update -y
sudo yum install -y git curl wget nginx

# 2. Instalar PHP 8.2
echo "🐘 Instalando PHP 8.2..."
sudo amazon-linux-extras install php8.2 -y
sudo yum install -y php8.2-{fpm,cli,gd,pdo,pgsql,mbstring,json,xml,zip,curl,bcmath}

# 3. Instalar Composer
echo "🎵 Instalando Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 4. Instalar Node.js
echo "📘 Instalando Node.js..."
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
nvm install 20
nvm use 20

# 5. Instalar PostgreSQL
echo "🗄️ Instalando PostgreSQL..."
sudo yum install -y postgresql15-server postgresql15-contrib
sudo systemctl start postgresql
sudo systemctl enable postgresql

# 6. Crear BD y usuario PostgreSQL
echo "🔐 Creando base de datos..."
sudo -u postgres psql -c "CREATE USER IF NOT EXISTS $DB_USER WITH PASSWORD '$DB_PASS';" || true
sudo -u postgres psql -c "ALTER USER $DB_USER WITH PASSWORD '$DB_PASS';" || true
sudo -u postgres psql -c "CREATE DATABASE IF NOT EXISTS $DB_NAME OWNER $DB_USER;" || true

# 7. Clonar repositorio
echo "📂 Clonando repositorio..."
if [ -d "$PROJECT_PATH" ]; then
    cd "$PROJECT_PATH"
    git pull origin master
else
    cd /home/ec2-user
    git clone "$REPO_URL"
fi

# 8. Instalar dependencias PHP
echo "📚 Instalando dependencias PHP..."
cd "$PROJECT_PATH"
composer install --optimize-autoloader --no-dev

# 9. Configurar .env
echo "⚙️ Configurando variables de entorno..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=pgsql|" .env
sed -i "s|DB_HOST=.*|DB_HOST=$DB_HOST|" .env
sed -i "s|DB_PORT=.*|DB_PORT=$DB_PORT|" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USER|" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|" .env
sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env

# 10. Generar APP_KEY
echo "🔑 Generando clave de aplicación..."
php artisan key:generate

# 11. Ejecutar migraciones
echo "🔄 Ejecutando migraciones..."
php artisan migrate --force

# 12. Instalar dependencias Node y compilar assets
echo "🎨 Compilando assets..."
npm install
npm run build

# 13. Permisos
echo "🔒 Asignando permisos..."
sudo chown -R nginx:nginx "$PROJECT_PATH"
sudo chmod -R 775 "$PROJECT_PATH/storage"
sudo chmod -R 775 "$PROJECT_PATH/bootstrap/cache"

# 14. Configurar Nginx
echo "🌐 Configurando Nginx..."
sudo tee /etc/nginx/conf.d/examen2.conf > /dev/null << 'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name _;

    root /home/ec2-user/Examen2/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }

    client_max_body_size 50M;
}
EOF

# 15. Iniciar servicios
echo "🚀 Iniciando servicios..."
sudo systemctl start php-fpm
sudo systemctl enable php-fpm
sudo systemctl start nginx
sudo systemctl enable nginx
sudo systemctl restart nginx

echo ""
echo "✅ ¡Despliegue completado exitosamente!"
echo "📍 Accede a: $APP_URL"
echo ""
```

---

## ✅ Verificaciones Finales

Después del despliegue, verifica que todo funciona:

```bash
# Verificar servicios activos
sudo systemctl status nginx
sudo systemctl status php-fpm
sudo systemctl status postgresql

# Revisar logs de Nginx
sudo tail -f /var/log/nginx/error.log

# Revisar logs de Laravel
tail -f /home/ec2-user/Examen2/storage/logs/laravel.log

# Probar conectividad a base de datos
cd /home/ec2-user/Examen2
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# Ejecutar health check
php artisan health
```

---

## 🔒 Configuración HTTPS (SSL/TLS)

### Usando Let's Encrypt + Certbot

```bash
sudo yum install -y certbot python3-certbot-nginx

# Obtener certificado (reemplaza tu-dominio.com)
sudo certbot certonly --nginx -d tu-dominio.com

# Auto-renovación
sudo systemctl enable certbot-renew.timer
sudo systemctl start certbot-renew.timer
```

Luego actualiza la configuración de Nginx en `/etc/nginx/conf.d/examen2.conf` para HTTPS.

---

## 🐛 Troubleshooting

### Error: "Class not found" después de deploy
```bash
cd /home/ec2-user/Examen2
php artisan optimize:clear
composer dump-autoload
```

### Error: "Migration not found"
```bash
php artisan migrate:refresh --seed
```

### Error: "Permission denied" en storage
```bash
sudo chown -R nginx:nginx /home/ec2-user/Examen2
sudo chmod -R 775 /home/ec2-user/Examen2/storage
```

### Nginx no sirve assets (CSS/JS vacíos)
```bash
cd /home/ec2-user/Examen2
npm run build
```

---

## 📞 Soporte

Si encuentras problemas, revisa:
1. Logs de Nginx: `/var/log/nginx/error.log`
2. Logs de Laravel: `/home/ec2-user/Examen2/storage/logs/laravel.log`
3. Estado de PHP-FPM: `sudo systemctl status php-fpm`
4. Estado de PostgreSQL: `sudo systemctl status postgresql`
