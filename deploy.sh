#!/bin/bash

################################################################################
# Script de Despliegue Automático: Laravel Jetstream + Inertia en EC2 (Amazon Linux)
# 
# Uso: bash deploy.sh [REPO_URL] [PROJECT_PATH] [DB_PASS] [APP_URL]
# Ejemplo: bash deploy.sh https://github.com/jacintoperez072-cyber/Examen2.git /home/ec2-user/Examen2 miaysasha http://tu-ip-ec2
#
# Este script automatiza:
# - Actualización del sistema
# - Instalación de PHP 8.2, Composer, Node.js
# - Instalación de PostgreSQL
# - Clonación/actualización del repositorio
# - Instalación de dependencias (Composer + npm)
# - Configuración de .env
# - Migraciones de BD
# - Compilación de assets
# - Configuración de Nginx
# - Permisos correctos
#
################################################################################

set -e  # Exit on error

# ============================================================================
# FUNCIONES AUXILIARES
# ============================================================================

log_info() {
    echo "ℹ️  $1"
}

log_success() {
    echo "✅ $1"
}

log_error() {
    echo "❌ $1"
    exit 1
}

log_section() {
    echo ""
    echo "════════════════════════════════════════════════════════════════"
    echo "📦 $1"
    echo "════════════════════════════════════════════════════════════════"
}

# ============================================================================
# CONFIGURACIÓN
# ============================================================================

# Variables con valores por defecto (se pueden sobrescribir con argumentos)
REPO_URL="${1:-https://github.com/jacintoperez072-cyber/Examen2.git}"
PROJECT_PATH="${2:-/home/ec2-user/Examen2}"
DB_HOST="localhost"
DB_PORT="5432"
DB_NAME="examen2"
DB_USER="laravel"
DB_PASS="${3:-miaysasha}"
APP_URL="${4:-http://localhost}"

log_section "Iniciando Despliegue en EC2"
echo "Repository:    $REPO_URL"
echo "Project Path:  $PROJECT_PATH"
echo "Database:      $DB_NAME@$DB_HOST:$DB_PORT"
echo "App URL:       $APP_URL"
echo ""
read -p "¿Continuar? (s/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    log_error "Despliegue cancelado por el usuario"
fi

# ============================================================================
# 1. ACTUALIZAR SISTEMA
# ============================================================================

log_section "Actualizando Sistema"

log_info "Ejecutando yum update..."
sudo yum update -y > /dev/null 2>&1
log_success "Sistema actualizado"

log_info "Instalando herramientas básicas..."
sudo yum install -y git curl wget unzip > /dev/null 2>&1
log_success "Herramientas instaladas"

# ============================================================================
# 2. INSTALAR PHP 8.2
# ============================================================================

log_section "Instalando PHP 8.2"

log_info "Habilitando repositorio PHP 8.2..."
sudo amazon-linux-extras install -y php8.2 > /dev/null 2>&1

log_info "Instalando extensiones PHP..."
sudo yum install -y \
    php8.2-fpm \
    php8.2-cli \
    php8.2-gd \
    php8.2-pdo \
    php8.2-pgsql \
    php8.2-mbstring \
    php8.2-json \
    php8.2-xml \
    php8.2-zip \
    php8.2-curl \
    php8.2-bcmath \
    php8.2-opcache \
    > /dev/null 2>&1

log_success "PHP 8.2 instalado"
php -v

# ============================================================================
# 3. INSTALAR COMPOSER
# ============================================================================

log_section "Instalando Composer"

if command -v composer &> /dev/null; then
    log_success "Composer ya está instalado"
    composer --version
else
    log_info "Descargando Composer..."
    curl -sS https://getcomposer.org/installer | php > /dev/null 2>&1
    
    log_info "Moviendo Composer a ubicación global..."
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
    
    log_success "Composer instalado"
    composer --version
fi

# ============================================================================
# 4. INSTALAR NODE.JS (vía NVM)
# ============================================================================

log_section "Instalando Node.js 20 (vía NVM)"

export NVM_DIR="$HOME/.nvm"

if [ -s "$NVM_DIR/nvm.sh" ]; then
    log_success "NVM ya está instalado"
    . "$NVM_DIR/nvm.sh"
else
    log_info "Descargando NVM..."
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh 2>/dev/null | bash > /dev/null 2>&1
    . "$NVM_DIR/nvm.sh"
    log_success "NVM instalado"
fi

log_info "Instalando Node.js 20..."
nvm install 20 > /dev/null 2>&1
nvm use 20

log_success "Node.js instalado"
node -v
npm -v

# ============================================================================
# 5. INSTALAR POSTGRESQL
# ============================================================================

log_section "Instalando PostgreSQL"

log_info "Instalando paquetes PostgreSQL..."
sudo yum install -y \
    postgresql15-server \
    postgresql15-contrib \
    postgresql15-devel \
    > /dev/null 2>&1

log_info "Iniciando servicio PostgreSQL..."
sudo systemctl start postgresql > /dev/null 2>&1
sudo systemctl enable postgresql > /dev/null 2>&1

log_success "PostgreSQL instalado y ejecutándose"
sudo systemctl status postgresql --no-pager

# ============================================================================
# 6. CREAR BASE DE DATOS Y USUARIO POSTGRESQL
# ============================================================================

log_section "Configurando Base de Datos PostgreSQL"

log_info "Creando usuario '$DB_USER' en PostgreSQL..."
sudo -u postgres psql -c "CREATE USER IF NOT EXISTS $DB_USER WITH PASSWORD '$DB_PASS';" > /dev/null 2>&1 || true
sudo -u postgres psql -c "ALTER USER $DB_USER WITH PASSWORD '$DB_PASS';" > /dev/null 2>&1

log_info "Creando base de datos '$DB_NAME'..."
sudo -u postgres psql -c "CREATE DATABASE IF NOT EXISTS $DB_NAME OWNER $DB_USER;" > /dev/null 2>&1

log_success "Base de datos configurada"
sudo -u postgres psql -l | grep "$DB_NAME"

# ============================================================================
# 7. INSTALAR NGINX
# ============================================================================

log_section "Instalando y Configurando Nginx"

log_info "Instalando Nginx..."
sudo yum install -y nginx > /dev/null 2>&1

log_success "Nginx instalado"
sudo systemctl start nginx > /dev/null 2>&1
sudo systemctl enable nginx > /dev/null 2>&1
nginx -v

# ============================================================================
# 8. CLONAR O ACTUALIZAR REPOSITORIO
# ============================================================================

log_section "Clonando/Actualizando Repositorio"

if [ -d "$PROJECT_PATH/.git" ]; then
    log_info "Repositorio ya existe, actualizando..."
    cd "$PROJECT_PATH"
    git pull origin master > /dev/null 2>&1
    log_success "Repositorio actualizado"
else
    log_info "Clonando repositorio..."
    mkdir -p "$(dirname "$PROJECT_PATH")"
    cd "$(dirname "$PROJECT_PATH")"
    git clone "$REPO_URL" "$(basename "$PROJECT_PATH")" > /dev/null 2>&1
    log_success "Repositorio clonado"
fi

cd "$PROJECT_PATH"

# ============================================================================
# 9. INSTALAR DEPENDENCIAS PHP
# ============================================================================

log_section "Instalando Dependencias PHP (Composer)"

log_info "Ejecutando composer install..."
composer install --optimize-autoloader --no-dev --no-interaction > /dev/null 2>&1
log_success "Dependencias PHP instaladas"

# ============================================================================
# 10. CONFIGURAR .ENV
# ============================================================================

log_section "Configurando Variables de Entorno (.env)"

if [ ! -f "$PROJECT_PATH/.env" ]; then
    log_info "Creando .env desde .env.example..."
    cp "$PROJECT_PATH/.env.example" "$PROJECT_PATH/.env"
fi

log_info "Configurando parámetros de base de datos..."
sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=pgsql|" "$PROJECT_PATH/.env"
sed -i "s|^DB_HOST=.*|DB_HOST=$DB_HOST|" "$PROJECT_PATH/.env"
sed -i "s|^DB_PORT=.*|DB_PORT=$DB_PORT|" "$PROJECT_PATH/.env"
sed -i "s|^DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" "$PROJECT_PATH/.env"
sed -i "s|^DB_USERNAME=.*|DB_USERNAME=$DB_USER|" "$PROJECT_PATH/.env"
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|" "$PROJECT_PATH/.env"

log_info "Configurando URL de aplicación..."
sed -i "s|^APP_URL=.*|APP_URL=$APP_URL|" "$PROJECT_PATH/.env"

log_success ".env configurado"

# ============================================================================
# 11. GENERAR APP_KEY
# ============================================================================

log_section "Generando Clave de Aplicación"

log_info "Ejecutando: php artisan key:generate"
php artisan key:generate --no-interaction > /dev/null 2>&1
log_success "Clave de aplicación generada"

# ============================================================================
# 12. EJECUTAR MIGRACIONES
# ============================================================================

log_section "Ejecutando Migraciones de Base de Datos"

log_info "Ejecutando: php artisan migrate --force"
php artisan migrate --force > /dev/null 2>&1
log_success "Migraciones completadas"

# ============================================================================
# 13. INSTALAR DEPENDENCIAS NODE Y COMPILAR ASSETS
# ============================================================================

log_section "Compilando Assets (npm)"

log_info "Ejecutando npm install..."
npm install > /dev/null 2>&1

log_info "Ejecutando npm run build..."
npm run build > /dev/null 2>&1

log_success "Assets compilados"

# ============================================================================
# 14. CONFIGURAR PERMISOS
# ============================================================================

log_section "Configurando Permisos"

log_info "Asignando ownership a nginx..."
sudo chown -R nginx:nginx "$PROJECT_PATH"

log_info "Asignando permisos a storage y bootstrap/cache..."
sudo chmod -R 775 "$PROJECT_PATH/storage"
sudo chmod -R 775 "$PROJECT_PATH/bootstrap/cache"

log_success "Permisos configurados"

# ============================================================================
# 15. CONFIGURAR NGINX
# ============================================================================

log_section "Configurando Nginx"

log_info "Creando configuración de Nginx..."
sudo tee /etc/nginx/conf.d/examen2.conf > /dev/null << 'NGINX_CONFIG'
server {
    listen 80;
    listen [::]:80;
    server_name _;

    root /home/ec2-user/Examen2/public;
    index index.php index.html index.htm;

    # Logs
    access_log /var/log/nginx/examen2-access.log;
    error_log /var/log/nginx/examen2-error.log;

    # Optimizaciones
    client_max_body_size 50M;
    gzip on;
    gzip_types text/plain text/css text/javascript application/json application/javascript;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        include fastcgi_params;

        # Timeouts
        fastcgi_connect_timeout 60;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
    }

    # Bloquear acceso a archivos ocultos
    location ~ /\. {
        deny all;
    }

    # Optimizar caching de assets estáticos
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 31536000s;
        cache_control "public, immutable";
    }
}
NGINX_CONFIG

log_success "Configuración de Nginx creada"

log_info "Verificando sintaxis de Nginx..."
sudo nginx -t

log_info "Reiniciando Nginx..."
sudo systemctl restart nginx

log_success "Nginx configurado y reiniciado"

# ============================================================================
# 16. INICIAR/REINICIAR SERVICIOS
# ============================================================================

log_section "Iniciando Servicios"

log_info "Iniciando/Reiniciando PHP-FPM..."
sudo systemctl start php-fpm > /dev/null 2>&1
sudo systemctl enable php-fpm > /dev/null 2>&1
sudo systemctl restart php-fpm > /dev/null 2>&1

log_info "Verificando estado de servicios..."
echo ""
echo "--- Nginx Status ---"
sudo systemctl status nginx --no-pager
echo ""
echo "--- PHP-FPM Status ---"
sudo systemctl status php-fpm --no-pager
echo ""
echo "--- PostgreSQL Status ---"
sudo systemctl status postgresql --no-pager

# ============================================================================
# 17. VERIFICACIONES FINALES
# ============================================================================

log_section "Verificaciones Finales"

log_info "Probando conectividad a la base de datos..."
cd "$PROJECT_PATH"
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Conexión OK'; } catch (Exception \$e) { echo 'Error: ' . \$e->getMessage(); }" || true

echo ""
log_info "Ejecutando health check de Laravel..."
php artisan health --no-interaction 2>/dev/null || log_info "Health check no disponible"

echo ""

# ============================================================================
# RESUMEN FINAL
# ============================================================================

log_section "✅ ¡DESPLIEGUE COMPLETADO EXITOSAMENTE!"

cat << EOF

📍 Acceso a la aplicación:
   URL: $APP_URL

🗄️  Base de datos:
   Host:     $DB_HOST
   Port:     $DB_PORT
   Database: $DB_NAME
   User:     $DB_USER

📁 Rutas importantes:
   Proyecto:  $PROJECT_PATH
   Logs:      $PROJECT_PATH/storage/logs/laravel.log
   Public:    $PROJECT_PATH/public

🔍 Próximos pasos:
   1. Accede a $APP_URL en tu navegador
   2. Revisa logs si hay problemas: tail -f $PROJECT_PATH/storage/logs/laravel.log
   3. Para SSH logs: sudo tail -f /var/log/nginx/examen2-error.log

📝 Comandos útiles:
   - Redirigir a HTTPS: sudo certbot certonly --nginx -d tu-dominio.com
   - Ver logs Nginx: sudo tail -f /var/log/nginx/examen2-error.log
   - Optimizar aplicación: php artisan optimize
   - Limpiar caché: php artisan optimize:clear

EOF

log_success "Despliegue completado en: $(date)"
