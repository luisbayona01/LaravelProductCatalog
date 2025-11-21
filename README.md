# Laravel Product Catalog

Catálogo de productos desarrollado con Laravel 11, JWT Authentication y base de datos.

## Requisitos Previos

- PHP 8.2 o superior
- Composer
- Node.js (para Vite)
- Una base de datos (MySQL/SQLite)

## Instalación

### 1. Clonar o descargar el proyecto

```bash
git clone https://github.com/luisbayona01/LaravelProductCatalog.git
cd LaravelProductCatalog
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias de Node.js

```bash
npm install
```

### 4. Configurar variables de entorno

Copia el archivo `.env.example` a `.env`:

```bash
cp .env.example .env
```

Luego edita el archivo `.env` y configura:
- `APP_KEY`: Genera una clave de aplicación
- Conexión a base de datos (DB_CONNECTION, DB_HOST, DB_DATABASE, etc.)
- Configuración de JWT

Genera la clave de la aplicación:

```bash
php artisan key:generate
```

### 5. Configurar JWT (JSON Web Tokens)

Genera la clave secreta para JWT:

```bash
php artisan jwt:secret
```

Este comando generará automáticamente un `JWT_SECRET` en tu archivo `.env`.

### 6. Ejecutar migraciones

Crea las tablas en la base de datos:

```bash
php artisan migrate
```

#### Migraciones disponibles:
- `create_users_table` - Tabla de usuarios
- `create_categories_table` - Tabla de categorías
- `create_products_table` - Tabla de productos
- `create_product_images_table` - Tabla de imágenes de productos
- `create_personal_access_tokens_table` - Tokens de acceso personal
- `create_cache_table` - Tabla de caché
- `create_jobs_table` - Tabla de trabajos en cola

### 7. Ejecutar seeders

Carga datos iniciales en la base de datos:

```bash
php artisan db:seed
```

#### Seeders disponibles:
- **DatabaseSeeder** - Ejecuta todos los seeders
- **CategoriesSeeder** - Carga categorías de ejemplo


Si deseas ejecutar un seeder específico:

```bash
php artisan db:seed --class=CategoriesSeeder

```

### 8. Compilar assets

Compila los archivos de CSS y JavaScript con Vite:

```bash
npm run dev
```

Para producción:

```bash
npm run build
```

### 9. Iniciar el servidor

```bash
php artisan serve
```

El servidor estará disponible en `http://localhost:8000`

## Estructura del Proyecto

- `app/Models` - Modelos de Eloquent
- `app/Http/Controllers` - Controladores de la aplicación
- `database/migrations` - Archivos de migración
- `database/seeders` - Archivos de seeders
- `routes/api.php` - Rutas de API
- `resources/views` - Vistas Blade

## Autenticación

El proyecto utiliza **JWT (JSON Web Tokens)** para la autenticación de API.

### Obtener un token:

```bash
POST /api/login
{
  "email": "user@example.com",
  "password": "password"
}
```

### Usar el token:

Incluye el token en el header de las peticiones:

```
Authorization: Bearer <your-jwt-token>
```

## Modelos Principales

- **User** - Usuarios del sistema
- **Category** - Categorías de productos
- **Product** - Productos del catálogo
- **ProductImage** - Imágenes asociadas a productos

## Testing

Ejecuta los tests con:

```bash
php artisan test
```

## Solución de Problemas

**Error de permisos en storage:**
```bash
php artisan storage:link
```

**Limpiar caché:**
```bash
php artisan cache:clear
php artisan config:clear
```

**Revertir última migración:**
```bash
php artisan migrate:rollback
```

## Licencia

MIT License
