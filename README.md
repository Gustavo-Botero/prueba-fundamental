# _Prueba FUNDAMENTAL_
Este proyecto es una aplicación desarrollada en Laravel 10 configurada para ejecutarse mediante Docker. Aquí encontrarás los pasos necesarios para levantar el proyecto y los detalles de los endpoints disponibles en la API.

## **Docker**
Sigue estos pasos para configurar y levantar el entorno de desarrollo con Docker:

1 - Ingresar al directorio docker:

```sh
cd docker/
```

2- Compilar y Ejecutar los contenedores en segundo plano:

```sh
docker compose up -d --build
```

3- Ejecutar el siguiente comando para crear el archivo con las variables de entorno que se necesitan:

```sh
docker exec -i laravel_app cp .env.example .env
```

4- Instalar dependencias:

```sh
docker exec -i laravel_app composer install
```

5- Correr las migraciones;

```sh
docker exec -i laravel_app php artisan migrate
```

## **Endpoints de la API**
A continuación, se detalla la lista de endpoints disponibles junto con ejemplos de cómo consumirlos.
| **Endpoint**        | **Método** | **Middleware**       | **Descripción**                                               | **Cuerpo de la Petición**                                                                                                                                                 |
|---------------------|------------|----------------------|---------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `/api/register`     | `POST`     | `api`               | Registrar un nuevo usuario.                                   | ```json {"name": "John Doe", "email": "john@example.com", "password": "password", "password_confirmation": "password"}```                                              |
| `/api/login`        | `POST`     | `api`               | Iniciar sesión y obtener un token JWT.                        | ```json {"email": "john@example.com", "password": "password"}```                                                                                                       |
| `/api/logout`       | `POST`     | `jwt.auth`          | Cerrar sesión del usuario autenticado.                        | -                                                                                                                                                                       |
| `/api/tasks`        | `GET`      | `jwt.auth`          | Obtener todas las tareas del usuario autenticado.             | -                                                                                                                                                                       |
| `/api/tasks`        | `POST`     | `jwt.auth`          | Crear una nueva tarea.                                        | ```json {"title": "New Task", "description": "Task description"}```                                                                                                   |
| `/api/tasks/{id}`   | `GET`      | `jwt.auth`          | Obtener los detalles de una tarea específica.                 | -                                                                                                                                                                       |
| `/api/tasks/{id}`   | `PUT`      | `jwt.auth`          | Actualizar una tarea existente.                               | ```json {"title": "Updated Task", "description": "Updated description"}```                                                                                           |
| `/api/tasks/{id}`   | `DELETE`   | `jwt.auth`          | Eliminar una tarea existente.                                 | -                                                                                                                                                                       |
