# 🚀 INICIO RÁPIDO - Sistema de Gestión Académica

## Instalación en 5 minutos

```bash
# 1. Instalar dependencias
composer install && npm install

# 2. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 3. Base de datos (editar .env primero)
php artisan migrate
php artisan db:seed

# 4. Compilar assets
npm run build

# 5. Iniciar servidor
php artisan serve
```

**Acceso:** http://localhost:8000

## 👤 Credenciales de Prueba

| Rol | Email | Contraseña |
|-----|-------|-----------|
| Admin | admin@sistema.com | password123 |
| Coordinador | coordinador@sistema.com | password123 |
| Docentes | carlos.garcia@sistema.com | password123 |
| | maria.lopez@sistema.com | password123 |
| | pedro.rodriguez@sistema.com | password123 |
| | ana.martinez@sistema.com | password123 |

## 📋 Menú Principal

Después de iniciar sesión, encontrarás en la barra de navegación:

- **Dashboard** - Página principal
- **Usuarios** - Gestión de usuarios
- **Roles** - Gestión de roles
- **Aulas** - Gestión de aulas
- **Horarios** - Gestión de horarios
- **Materias** - Gestión de materias
- **Grupos** - Gestión de grupos
- **Asistencia** - Registro de asistencia
- **Bitácora** - Auditoría del sistema

## 🎯 Funcionalidades Principales

### Gestión de Usuarios
- Crear nuevo usuario
- Asignar rol (Admin, Coordinador, Docente)
- Cambiar estado (Activo/Inactivo)
- Cambiar contraseña

### Gestión Académica
- Crear/editar aulas con capacidad
- Asignar horarios a aulas
- Crear materias
- Crear grupos de estudiantes
- Asignar materias a grupos con horarios

### Asistencia
- Registrar asistencia individual
- Estados: Presente, Ausente, Retardo, Justificada
- Consultar asistencia por docente y grupo
- Exportar a CSV

### Reportes
- Reporte de asistencia
- Reporte de bitácora
- Exportación a Excel/PDF

## 🔍 Datos Iniciales

El sistema se carga con:
- ✅ 3 roles predefinidos
- ✅ 34 permisos configurados
- ✅ 5 usuarios de prueba
- ✅ 8 aulas
- ✅ 40 horarios
- ✅ 10 materias
- ✅ 6 grupos
- ✅ 4 docentes

## 🛠️ Comandos Útiles

```bash
# Resetear base de datos
php artisan migrate:fresh --seed

# Ver rutas
php artisan route:list

# Modo desarrollo (hot reload)
npm run dev

# Limpiar cache
php artisan cache:clear

# Regenerar autoloader
composer dump-autoload
```

## 📱 Características

✅ Autenticación segura con Jetstream
✅ Sistema de roles y permisos
✅ Registro de auditoría completo
✅ Interfaz responsive
✅ Validación en backend
✅ Exportación de datos
✅ Consultas avanzadas
✅ Manejo de errores

## 🔐 Seguridad

- Todos los endpoints están protegidos
- Validación de permisos en backend
- Contraseñas hasheadas con Bcrypt
- Registro de todas las acciones
- CSRF protection habilitado
- SQL Injection prevention

## ❓ Preguntas Frecuentes

### P: ¿Cómo cambio la contraseña?
R: Puedes cambiarla en el perfil de usuario o como administrador en la gestión de usuarios.

### P: ¿Cómo creo un nuevo rol?
R: Ve a Roles > Crear Rol, elige un nombre y asigna los permisos.

### P: ¿Dónde veo las auditorías?
R: En el menú principal, haz clic en "Bitácora".

### P: ¿Puedo exportar datos?
R: Sí, en Asistencia e Bitácora hay opciones de exportación.

### P: ¿Cómo agrego más aulas?
R: Ve a Aulas > Crear Aula, completa los datos y guarda.

## 📞 Soporte

Para reportar bugs o solicitar features, consulta la documentación:
- `INSTRUCCIONES.md` - Documentación completa
- `SETUP.md` - Guía detallada
- `RESUMEN_IMPLEMENTACION.md` - Detalles técnicos

---

**¡Sistema listo para usar! 🎉**

Más información en `INSTRUCCIONES.md`
