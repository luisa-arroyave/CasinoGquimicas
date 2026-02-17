# Casino Gquímicas - Sistema web empresarial

Sistema base desarrollado con **Laravel** (última versión estable), **MySQL** y **TailwindCSS**, con autenticación, layout con menú lateral y navbar, y estructura preparada para roles y permisos.

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+ y npm (para compilar assets con Vite)
- MySQL 8+ (o XAMPP con MySQL)
- Extensiones PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, PDO_MySQL, Tokenizer, XML

## Instalación

### 1. Base de datos

Crear en MySQL la base de datos:

```sql
CREATE DATABASE casino_gquimicas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configurar en `.env` (ya viene preconfigurado para MySQL):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=casino_gquimicas
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Dependencias y clave de aplicación

Si no se generó la clave en la instalación:

```bash
php artisan key:generate
```

### 3. Migraciones y seeders

```bash
php artisan migrate
php artisan db:seed
```

El seeder crea los roles base: **Administrador** (`admin`) y **Usuario** (`user`).

### 4. Assets (TailwindCSS + Vite)

```bash
npm install
npm run build
```

Para desarrollo con recarga automática:

```bash
npm run dev
```

### 5. Servidor

Con XAMPP: colocar el proyecto en `htdocs` y acceder a `http://localhost/CasinoGquimicas/public`.

O con el servidor incluido en Laravel:

```bash
php artisan serve
```

Luego abrir `http://127.0.0.1:8000`.

## Estructura del proyecto

- **Autenticación**: login, registro y logout en `App\Http\Controllers\Auth\AuthController`.
- **Vistas**: Blade en `resources/views/` (layout en `layouts/app.blade.php`, auth en `auth/`, dashboard en `dashboard/`).
- **Layout**: menú lateral (sidebar) y barra superior (navbar), con diseño responsive.
- **Sesiones**: driver configurable en `.env` (`SESSION_DRIVER=database` por defecto); tablas `sessions` y `cache` creadas por migraciones.
- **CSRF**: protección activa en todas las peticiones POST del grupo de rutas `web`.

## Roles y permisos (estructura base)

- **Tablas**: `roles`, `permissions`, `permission_role`; en `users` se añade `role_id` (nullable).
- **Modelos**: `App\Models\Role`, `App\Models\Permission`; en `User` están `role()`, `hasRole($slug)` y `hasPermission($slug)`.
- **Middleware**:
  - `role:admin,manager` → exige que el usuario tenga uno de esos roles (por slug).
  - `permission:users.create` → exige que el usuario tenga ese permiso a través de su rol.

Ejemplo de uso en rutas:

```php
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware(['auth', 'role:admin']);

Route::get('/reports', [ReportController::class, 'index'])
    ->middleware(['auth', 'permission:reports.view']);
```

Los permisos se asignan a roles en la tabla pivote `permission_role`. Los módulos que consuman estos permisos se pueden implementar más adelante.

## Rutas principales

| Ruta           | Descripción                    | Acceso   |
|----------------|--------------------------------|----------|
| `/`            | Redirige a dashboard o login   | Público  |
| `/login`       | Formulario de inicio de sesión | Invitado |
| `/register`    | Formulario de registro         | Invitado |
| `/logout`      | Cerrar sesión (POST)           | Auth     |
| `/dashboard`   | Panel principal                | Auth     |

## Notas

- No se incluyen módulos de negocio complejos; solo la base del sistema (auth, layout, dashboard y estructura de roles/permisos).
- Para producción, configurar `APP_DEBUG=false`, `APP_ENV=production` y usar HTTPS.
