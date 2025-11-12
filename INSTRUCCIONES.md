# Sistema de Gestión Académica

Sistema completo de gestión académica desarrollado con Laravel 12, Vue 3 e Inertia.js

## Casos de Uso Implementados

✅ **CU01:** Iniciar sesión  
✅ **CU02:** Cerrar sesión  
✅ **CU03:** Gestionar usuarios (CRUD)  
✅ **CU04:** Gestionar roles y permisos  
✅ **CU05:** Cambiar contraseña  
✅ **CU06:** Gestionar Bitácora (auditoría)  
✅ **CU07:** Asignar Horario a GrupoMateria  
✅ **CU08:** Eliminar Horario  
✅ **CU09:** Gestionar Materia (CRUD)  
✅ **CU10:** Gestionar Grupo (CRUD)  
✅ **CU11:** Gestionar Aula (CRUD)  
✅ **CU12:** Asignar horario docente  
✅ **CU13:** Generación de Horarios Docente (automático)  
✅ **CU14:** Registrar asistencia  
✅ **CU15:** Consultar horarios semanales  
✅ **CU16:** Consultar aulas disponibles  
✅ **CU17:** Consultar asistencia por Docente y Grupo  
✅ **CU18:** Generar reporte en PDF  
✅ **CU19:** Generar reporte en Excel  

## Requisitos

- PHP 8.2+
- Composer
- Node.js y npm
- Base de datos (MySQL, PostgreSQL, etc.)

## Instalación

### 1. Clonar el repositorio
```bash
git clone <repository-url>
cd Practica1
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Instalar dependencias de Node.js
```bash
npm install
```

### 4. Configurar el archivo .env
```bash
cp .env.example .env
php artisan key:generate
```

Edita `.env` y configura tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_academico
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Ejecutar migraciones y seeders
```bash
php artisan migrate
php artisan db:seed
```

### 6. Generar activos frontend
```bash
npm run dev
```

### 7. Iniciar servidor de desarrollo
```bash
php artisan serve
```

## Credenciales de Prueba

Después de ejecutar los seeders, puedes acceder con:

### Administrador
- **Email:** admin@sistema.com
- **Contraseña:** password123

### Coordinador
- **Email:** coordinador@sistema.com
- **Contraseña:** password123

### Docentes
- **Email:** carlos.garcia@sistema.com
- **Email:** maria.lopez@sistema.com
- **Email:** pedro.rodriguez@sistema.com
- **Email:** ana.martinez@sistema.com
- **Contraseña:** password123 (para todos)

## Estructura del Proyecto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controladores (Usuarios, Roles, Aulas, etc.)
│   │   └── Middleware/      # Middleware de autenticación y permisos
│   ├── Models/              # Modelos Eloquent
│   ├── Policies/            # Políticas de autorización
│   └── Services/            # Servicios (BitacoraService)
├── database/
│   ├── migrations/          # Migraciones de BD
│   └── seeders/             # Seeders de datos
├── resources/
│   └── js/
│       ├── Pages/           # Componentes de páginas
│       ├── Layouts/         # Layouts
│       └── Components/      # Componentes reutilizables
├── routes/
│   └── web.php              # Rutas web
└── config/                  # Configuración
```

## Modelos y Relaciones

### Usuario
- Pertenece a un Rol
- Puede ser Docente
- Tiene muchos registros en Bitácora

### Rol
- Tiene muchos Permisos
- Tiene muchos Usuarios

### Docente
- Pertenece a Usuario
- Tiene muchas asignaciones de GrupoMateria
- Tiene muchos registros de Asistencia

### Grupo
- Tiene muchas Materias (a través de GrupoMateria)
- Tiene muchas Asistencias

### Materia
- Tiene muchos Grupos (a través de GrupoMateria)

### GrupoMateria
- Pertenece a Grupo y Materia
- Tiene un Horario
- Tiene muchos Docentes
- Tiene muchas Asistencias

### Horario
- Pertenece a Aula
- Es usado por GrupoMateria

### Asistencia
- Pertenece a GrupoMateria
- Pertenece a Docente

### Bitácora
- Registra todas las acciones del sistema
- Pertenece a Usuario

## Características Principales

### Gestión de Permisos
- Sistema basado en Roles y Permisos
- Validación en backend usando Gates
- Tres roles predefinidos: Administrador, Coordinador, Docente

### Auditoría (Bitácora)
- Registro automático de todas las acciones
- Información de IP y user-agent
- Exportación a CSV

### Gestión de Horarios
- Asignación de horarios a aulas
- Asignación de horarios a grupos-materias
- Consulta de aulas disponibles
- Generación automática de horarios para docentes

### Gestión de Asistencia
- Registro individual o en lote
- Consulta por docente y grupo
- Exportación a CSV

### Reportes
- Reportes de asistencia
- Reportes de bitácora
- Exportación a PDF y Excel

## Rutas API

### Autenticación
- `POST /login` - Iniciar sesión
- `POST /logout` - Cerrar sesión
- `POST /usuarios/cambiar-contrasena` - Cambiar contraseña

### Usuarios
- `GET /usuarios` - Listar usuarios
- `POST /usuarios` - Crear usuario
- `GET /usuarios/{id}/edit` - Editar usuario
- `PUT /usuarios/{id}` - Actualizar usuario
- `DELETE /usuarios/{id}` - Eliminar usuario

### Roles
- `GET /roles` - Listar roles
- `POST /roles` - Crear rol
- `GET /roles/{id}/edit` - Editar rol
- `PUT /roles/{id}` - Actualizar rol
- `DELETE /roles/{id}` - Eliminar rol

### Aulas
- `GET /aulas` - Listar aulas
- `POST /aulas` - Crear aula
- `GET /aulas/{id}/edit` - Editar aula
- `PUT /aulas/{id}` - Actualizar aula
- `DELETE /aulas/{id}` - Eliminar aula
- `GET /aulas/disponibles` - Consultar aulas disponibles

### Horarios
- `GET /horarios` - Listar horarios
- `POST /horarios` - Crear horario
- `GET /horarios/{id}/edit` - Editar horario
- `PUT /horarios/{id}` - Actualizar horario
- `DELETE /horarios/{id}` - Eliminar horario
- `GET /horarios/semanales` - Consultar horarios semanales

### Materias
- `GET /materias` - Listar materias
- `POST /materias` - Crear materia
- `GET /materias/{id}/edit` - Editar materia
- `PUT /materias/{id}` - Actualizar materia
- `DELETE /materias/{id}` - Eliminar materia

### Grupos
- `GET /grupos` - Listar grupos
- `POST /grupos` - Crear grupo
- `GET /grupos/{id}/edit` - Editar grupo
- `PUT /grupos/{id}` - Actualizar grupo
- `DELETE /grupos/{id}` - Eliminar grupo

### Asistencia
- `GET /asistencias` - Listar asistencias
- `POST /asistencias` - Crear asistencia
- `POST /asistencias/registrar-grupo` - Registrar asistencia en lote
- `GET /asistencias/por-docente-grupo` - Consultar por docente y grupo
- `POST /asistencias/exportar` - Exportar a CSV

### Docentes
- `GET /docentes` - Listar docentes
- `POST /docentes/{id}/asignar-grupo-materia` - Asignar grupo-materia
- `DELETE /docentes/{id}/desasignar-grupo-materia/{grupoMateria}` - Desasignar
- `GET /docentes/{id}/horarios` - Ver horarios del docente

### Reportes
- `GET /reportes/asistencia-pdf` - Reporte de asistencia (PDF)
- `GET /reportes/asistencia-excel` - Reporte de asistencia (Excel)
- `GET /reportes/bitacora-pdf` - Reporte de bitácora (PDF)
- `GET /reportes/bitacora-excel` - Reporte de bitácora (Excel)
- `GET /reportes/horarios-semanales` - Horarios semanales

### Bitácora
- `GET /bitacora` - Ver bitácora
- `POST /bitacora/exportar` - Exportar bitácora

## Seeders Disponibles

El proyecto incluye seeders para poblar la base de datos con datos de prueba:

- **RolSeeder:** Crea roles (Administrador, Coordinador, Docente)
- **PermisoSeeder:** Crea permisos
- **RolPermisoSeeder:** Asigna permisos a roles
- **UsuarioSeeder:** Crea usuarios de prueba
- **AulaSeeder:** Crea aulas
- **HorarioSeeder:** Crea horarios
- **MateriaSeeder:** Crea materias
- **GrupoSeeder:** Crea grupos
- **GrupoMateriaSeeder:** Asigna materias a grupos
- **DocenteSeeder:** Crea registros de docentes
- **DocenteGrupoMateriaSeeder:** Asigna grupo-materias a docentes

## Desarrollo Futuro

Características sugeridas a implementar:

1. Integración de PDF con librerías como DomPDF o mPDF
2. Integración de Excel con Maatwebsite/Excel
3. Sistema de notificaciones
4. Calendario interactivo
5. Dashboard con gráficos estadísticos
6. Generación automática de horarios con algoritmos más complejos
7. Importación de datos desde Excel
8. Sistema de justificaciones de ausencias
9. Portal para estudiantes
10. Comunicación directa entre docentes y coordinadores

## Notas Importantes

- Todos los endpoints están protegidos por autenticación y autorización
- El sistema registra todas las acciones en la bitácora
- Los permisos se validan mediante Gates en el backend
- Los seeders deben ejecutarse antes de usar el sistema
- Las contraseñas se hashean usando Bcrypt
- Se utiliza Inertia.js para comunicación seamless entre Laravel y Vue

## Soporte

Para más información sobre cómo extender o modificar este sistema, consulta:

- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Inertia.js](https://inertiajs.com/)
- [Documentación de Vue 3](https://vuejs.org/)

## Licencia

Este proyecto está bajo licencia MIT.
