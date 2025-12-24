# SISTEMA EMANUEL MONTESORI 1.0.1

Aplicación web basada en **Laravel** y el ecosistema de **FilamentPHP** para la gestión académica y administrativa de la **Institución Pedagógica Infantil "Emanuel Montesori"**.  
El proyecto está organizado en módulos (estudiantes, inscripciones, pagos, evaluaciones, etc.) y se estructura mediante *milestones* en GitHub.  
**FilamentPHP** actúa como plugin de frontend y backend para la construcción del panel administrativo sobre los modelos y servicios de Laravel.

<p align="left">
  <a href="#"><img src="https://img.shields.io/badge/estado-en%20desarrollo-yellow" alt="Estado: en desarrollo"></a>
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-backend-777bb4?logo=php&logoColor=white" alt="PHP"></a>
  <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-framework-FF2D20?logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/FilamentPHP-admin%20panel-0ea5e9" alt="FilamentPHP"></a>
  <a href="#"><img src="https://img.shields.io/badge/milestone-documentaci%C3%B3n%20del%20repositorio-blue" alt="Milestone: Documentación del repositorio"></a>
</p>

---

## 🎯 Objetivos del proyecto

El proyecto **EMANUEL MONTESORI** tiene como objetivos principales:

- Centralizar la información académica y administrativa de la institución.
- Definir una estructura clara de entidades educativas (gestiones/años, ciclos, cursos, aulas y niveles).
- Gestionar estudiantes, apoderados y su relación.
- Controlar inscripciones/matrículas por gestión, curso y aula, incluyendo cupos y requisitos.
- Administrar la asignación académica de profesores a cursos y materias.
- Registrar evaluaciones y notas, y calcular promedios.
- Gestionar pagos, deudas y el estado del estudiante (habilitado/bloqueado).
- Ofrecer un panel de reportes y dashboard con estadísticas relevantes.
- Implementar un núcleo de seguridad con usuarios, roles y permisos.
- Implementacion continua basada en CI y DC con docker compose

Estos objetivos se desglosan en los siguientes milestones principales del proyecto (entre otros):

- **Documentación del repositorio**  
- **Módulo Gestión de Entidades Educativas**  
- **Módulo Estudiantes y Apoderados**  
- **Módulo Inscripciones / Matrículas**  
- **Módulo Profesores / Asignación Académica**  
- **Módulo Evaluaciones / Notas**  
- **Módulo Pagos / Finanzas**  
- **Módulo Reportes / Dashboard**  
- **Módulo Núcleo / Seguridad / Usuarios**  

> Para más detalles puedes revisar la sección de *Milestones* en GitHub.

---

## 🏛 Arquitectura y tecnologías

El sistema está construido sobre el ecosistema de **Laravel** y un conjunto de herramientas modernas para backend y frontend.

- **Backend**
  - **PHP 8.x** – Lenguaje principal del servidor.
  - **Laravel** – Framework MVC para la lógica de negocio, routing, middleware, colas, etc.
  - **Eloquent ORM** – Mapeo objeto–relacional para acceso y gestión de datos.
  - **FilamentPHP** – Plugin de administración que se integra directamente con Laravel para construir paneles y recursos sobre los modelos de Eloquent.
  - **PHPUnit / Pruebas de Laravel** – Para pruebas automatizadas de la aplicación.
  - **Composer** – Gestor de dependencias PHP.

- **Frontend (administrativo y público)**
  - **FilamentPHP** (plugin frontend y backend a la vez)  
    - Usa **Laravel Livewire** para componentes dinámicos sin escribir JavaScript explícito.
    - Utiliza **Alpine.js** para interactividad ligera en el navegador.
    - Se apoya en **Tailwind CSS** (por defecto) para estilos del panel administrativo.
  - **Blade** – Sistema de plantillas de Laravel para vistas públicas o secciones personalizadas.
  - **Vite** – Empaquetador y servidor de desarrollo para assets (JS, CSS).
  - **npm** – Gestor de dependencias y scripts para el frontend.

- **Base de datos**
  - Motor SQL compatible (por ejemplo **MySQL/MariaDB** o **PostgreSQL**) para el almacenamiento persistente de la información.

- **Herramientas adicionales**
  - **Git** para control de versiones.
  - Entornos locales como **Docker / Laravel Sail**, **XAMPP**, **Laragon**, etc. (opcionales según preferencia).

En conjunto, **FilamentPHP** actúa como un puente entre el backend (Laravel/Eloquent) y el frontend (Livewire/Alpine/Tailwind), permitiendo construir rápidamente interfaces administrativas modernas sobre la lógica de negocio del sistema.

---

## 📌 Estado del proyecto

- Rama principal de desarrollo: **`develop`**.  
- El proyecto se encuentra en una fase inicial: se están definiendo los modelos base (por ejemplo, el modelo de Usuario) y la estructura de módulos mediante milestones e issues.

Revisa los **Issues** y **Milestones** del repositorio para conocer el estado actual de cada módulo.

---

## ✅ Requisitos previos

Para ejecutar el proyecto en local necesitas:

- **Git**
- **PHP 8.x** (con las extensiones requeridas por Laravel)
- **Composer**
- **Node.js** y **npm**
- Un motor de **base de datos** (MySQL/MariaDB, PostgreSQL, etc.)

Además, debes contar con algún entorno local, por ejemplo:

- Laravel Sail / Docker  
- XAMPP, Laragon, WAMP, etc.  
- Laravel Valet (en macOS)

---

## ⚙️ Instalación (entorno local)

> 👀 Si vas a contribuir con cambios al repositorio, revisa primero la sección  
> [Contribuir (vía fork)](#-contribuir-vía-fork).

### 1. Clonar el repositorio

```bash
git clone https://github.com/krypton612/montesori.git
cd montesori
```
ejecutar los siguientes comandos para instalar dependencias y levantar el proyecto en entorno local.

## 1. Instalar dependencias de PHP
- composer install

## 2. Configurar entorno de la aplicación
- cp .env.example .env
- php artisan key:generate

Editar el archivo .env para configurar la base de datos y otros parámetros (APP_NAME, APP_URL, etc.).

## 3. Ejecutar migraciones y seeders
- php artisan migrate
- php artisan db:seed

# o en un solo paso:
- php artisan migrate --seed

## 4. Crear enlace de almacenamiento
- php artisan storage:link

## 5. Instalar dependencias de frontend
- npm install

## 6. Compilar assets
Modo desarrollo:
- npm run dev

Compilación para producción:
- npm run build

## 7. Levantar el servidor de Laravel
- php artisan serve

La aplicación quedará disponible, por defecto, en:
http://localhost:8000

