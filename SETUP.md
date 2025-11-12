# Guía de Instalación y Ejecución

## Pasos de Instalación

### 1. Instalar Dependencias de PHP
```bash
composer install
```

### 2. Instalar Dependencias de Node.js
```bash
npm install
```

### 3. Configurar Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar Base de Datos en `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_academico
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Ejecutar Migraciones
```bash
php artisan migrate
```

### 6. Ejecutar Seeders
```bash
php artisan db:seed
```

### 7. Compilar Assets Frontend
```bash
npm run build
```

Para desarrollo con hot reload:
```bash
npm run dev
```

### 8. Iniciar Servidor
```bash
php artisan serve
```

El sistema estará disponible en: `http://localhost:8000`

## Credenciales de Prueba

### Administrador
- **Email:** admin@sistema.com
- **Contraseña:** password123

### Coordinador
- **Email:** coordinador@sistema.com
- **Contraseña:** password123

### Docentes (todos con contraseña: password123)
- carlos.garcia@sistema.com
- maria.lopez@sistema.com
- pedro.rodriguez@sistema.com
- ana.martinez@sistema.com

## Datos de Prueba Creados por Seeders

### Aulas
- 8 aulas (A-101 a B-203)
- Tipos: Salón, Laboratorio, Auditorio
- Capacidades varían de 20 a 100 personas

### Horarios
- 40 horarios distribuidos en diferentes aulas
- Días: Lunes a Viernes
- Franjas horarias: 08:00 a 17:15

### Materias
- 10 materias diferentes
- Códigos: MAT-101, FIS-101, QUI-101, PRO-101, etc.

### Grupos
- 6 grupos de estudiantes
- Códigos: TI-2024-A, IS-2024-B, BD-2024-A, etc.

### Docentes
- 4 docentes registrados
- Especialidades: Matemática, Física, Química, Programación

### Relaciones
- Cada grupo tiene 3-4 materias asignadas
- Cada docente tiene 2-3 grupos-materias asignados
- Cada grupo-materia tiene un horario y aula asignados

## Funcionalidades Principales

### ✅ Autenticación
- Login con email y contraseña
- Logout seguro
- Cambio de contraseña

### ✅ Gestión de Usuarios
- Crear, editar y eliminar usuarios
- Asignar roles a usuarios
- Estados: activo/inactivo

### ✅ Gestión de Roles y Permisos
- Crear roles personalizados
- Asignar permisos a roles
- Validación de permisos en el backend

### ✅ Gestión de Aulas
- CRUD completo de aulas
- Consultar disponibilidad de aulas
- Filtrado por tipo y capacidad

### ✅ Gestión de Horarios
- Crear y asignar horarios a aulas
- Horarios para grupos-materias
- Consultar horarios semanales

### ✅ Gestión de Materias y Grupos
- CRUD de materias
- CRUD de grupos
- Asignar materias a grupos

### ✅ Gestión de Docentes
- Asignar grupos-materias a docentes
- Ver horarios del docente
- Generación automática de horarios

### ✅ Control de Asistencia
- Registrar asistencia individual
- Registrar asistencia en lote
- Consultar por docente y grupo
- Exportar a CSV

### ✅ Reportes
- Reportes de asistencia (PDF, Excel, CSV)
- Reportes de bitácora
- Horarios semanales

### ✅ Bitácora y Auditoría
- Registro automático de todas las acciones
- Información de usuario, IP, fecha/hora
- Filtrado y búsqueda
- Exportación a CSV

## Estructura de Carpetas Importantes

```
app/
├── Http/
│   ├── Controllers/          # Todos los controladores
│   └── Middleware/           # Middleware de permisos
├── Models/                   # 11 modelos Eloquent
├── Policies/                 # Políticas de autorización
└── Services/                 # BitacoraService

database/
├── migrations/               # 12 migraciones
└── seeders/                  # 11 seeders

resources/
└── js/
    ├── Pages/                # Páginas Vue por módulo
    ├── Layouts/              # Layout principal
    └── Components/           # Componentes reutilizables

routes/
└── web.php                   # 50+ rutas definidas
```

## Rutas Disponibles

### Autenticación (Jetstream)
- `/login` - Iniciar sesión
- `/logout` - Cerrar sesión

### Gestión Administrativa
- `/usuarios` - CRUD de usuarios
- `/roles` - CRUD de roles
- `/bitacora` - Auditoría del sistema

### Gestión Académica
- `/aulas` - CRUD de aulas
- `/horarios` - CRUD de horarios
- `/materias` - CRUD de materias
- `/grupos` - CRUD de grupos
- `/grupo-materias` - Asignaciones grupo-materia
- `/docentes` - Gestión de docentes
- `/asistencias` - Gestión de asistencia

### Reportes
- `/reportes/asistencia-pdf` - PDF de asistencia
- `/reportes/asistencia-excel` - Excel de asistencia
- `/reportes/bitacora-pdf` - PDF de bitácora
- `/reportes/bitacora-excel` - Excel de bitácora

## Tecnologías Utilizadas

### Backend
- **Laravel 12** - Framework PHP
- **Laravel Jetstream** - Autenticación y gestión de equipos
- **Inertia.js** - Enrutador SPA
- **Eloquent ORM** - Base de datos

### Frontend
- **Vue 3** - Framework JavaScript
- **Inertia.js** - Adaptador para Laravel
- **Tailwind CSS** - Estilos

### Base de Datos
- **MySQL/PostgreSQL** - Almacenamiento de datos
- **Laravel Migrations** - Versionado de esquema

### Librerías Adicionales
- **barryvdh/laravel-dompdf** - Generación de PDF
- **maatwebsite/excel** - Exportación a Excel

## Notas Importantes

1. **Permisos**: El sistema está basado en roles y permisos. Revisa `RolPermisoSeeder` para ver qué permisos tiene cada rol.

2. **Bitácora**: Todas las acciones se registran automáticamente. Revisa `BitacoraService` para ver cómo funciona.

3. **Validación**: Todas las validaciones se hacen en el backend. Los formularios Vue son reactivos.

4. **Errores**: Los errores de validación se muestran en los formularios automáticamente.

5. **Migración**: Para resetear todo y empezar de nuevo:
   ```bash
   php artisan migrate:fresh --seed
   ```

## Solución de Problemas

### Error: "SQLSTATE[HY000]: General error"
- Ejecuta `php artisan migrate:fresh --seed`

### Error: "Class not found"
- Ejecuta `composer dump-autoload`

### Error: Assets no se cargan
- Ejecuta `npm run build` o `npm run dev`

### Error: Port 8000 en uso
- Usa `php artisan serve --port=8001`

## Próximos Pasos

Para extender el sistema:

1. Crear más seeders para datos de prueba
2. Implementar validaciones más complejas
3. Agregar filtros avanzados
4. Integrar gráficos estadísticos
5. Crear un portal para estudiantes
6. Implementar notificaciones
7. Agregar importación de Excel

## Contacto y Soporte

Este es un proyecto de demostración. Para más información, consulta la documentación oficial de:
- [Laravel](https://laravel.com)
- [Vue 3](https://vuejs.org)
- [Inertia.js](https://inertiajs.com)
