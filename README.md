# montesori

Aplicación web basada en **Laravel** para la gestión académica y administrativa de una **institución educativa pública**.  
El proyecto está organizado en módulos (estudiantes, inscripciones, pagos, evaluaciones, etc.) y se estructura mediante *milestones* en GitHub.

Este archivo forma parte del milestone **“Documentación del repositorio”**.

---

## Objetivos del proyecto

El proyecto **montesori** busca:

- Centralizar la información académica y administrativa de la institución.
- Definir una estructura clara de entidades educativas (gestiones/años, ciclos, cursos, aulas y niveles).
- Gestionar estudiantes, apoderados y su relación.
- Controlar inscripciones/matrículas por gestión, curso y aula, incluyendo cupos y requisitos.
- Administrar la asignación académica de profesores a cursos y materias.
- Registrar evaluaciones y notas, y calcular promedios.
- Gestionar pagos, deudas y el estado del estudiante (habilitado/bloqueado).
- Ofrecer un panel de reportes y dashboard con estadísticas relevantes.
- Implementar un núcleo de seguridad con usuarios, roles y permisos.

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

## Tecnologías

El proyecto está construido sobre:

- **Framework**: [Laravel](https://laravel.com/)
- **Lenguaje**: PHP
- **Vistas**: Blade
- **Empaquetador frontend**: Vite
- **Gestión de dependencias backend**: Composer
- **Gestión de dependencias frontend**: npm
- **Base de datos**: motor SQL (por ejemplo MySQL/MariaDB o PostgreSQL)

> La versión exacta de PHP, Laravel y otras dependencias se puede consultar en el archivo `composer.json`.

---

## Estado del proyecto

- Rama principal de desarrollo: **`develop`**.
- El proyecto se encuentra en una fase inicial: se están definiendo los modelos base (por ejemplo, el modelo de Usuario) y la estructura de módulos mediante milestones e issues.

Revisa los **Issues** y **Milestones** del repositorio para conocer el estado actual de cada módulo.

---

## Requisitos previos

Para ejecutar el proyecto en local necesitas:

- **Git**
- **PHP 8.x** (con las extensiones requeridas por Laravel)
- **Composer**
- **Node.js** y **npm**
- Un motor de **base de datos** (MySQL/MariaDB, PostgreSQL, etc.)

Adicionalmente:

- Configurar un entorno local (por ejemplo, Laravel Sail, Docker, XAMPP, Laragon, Valet, etc.) según tus preferencias.

---

## Instalación (entorno local)

### 1. Clonar el repositorio

Si solo quieres probar el proyecto (sin contribuir):

```bash
git clone https://github.com/krypton612/montesori.git
cd montesori
