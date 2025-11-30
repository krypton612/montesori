# SISTEMA EMANUEL MONTESORI

Aplicación web basada en **Laravel** para la gestión académica y administrativa de una **Institución Pedadogica Emanuel Montesori**.  
El proyecto está organizado en módulos (estudiantes, inscripciones, pagos, evaluaciones, etc.) y se estructura mediante *milestones* en GitHub.
<p align="left">
  <a href="#"><img src="https://img.shields.io/badge/estado-en%20desarrollo-yellow" alt="Estado: en desarrollo"></a>
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-backend-777bb4?logo=php&logoColor=white" alt="PHP"></a>
  <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-framework-FF2D20?logo=laravel&logoColor=white" alt="Laravel"></a>
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

El sistema está construido sobre el ecosistema de **Laravel** y **FilamentPHP** convirtiendolo un framework completo:

- **Capa de aplicación (backend)**
  - PHP
  - Laravel (framework MVC)
  - Eloquent ORM para acceso a datos y modelado de entidades
  - Servicios / lógica de negocio de la institución

- **Capa de administración (panel interno)**
  - FilamentPHP  
    - Panel administrativo para gestionar entidades (estudiantes, inscripciones, pagos, etc.).
    - Definición de recursos, formularios, tablas y dashboards desde PHP.
    - Basado en **Laravel Livewire** y **Alpine.js** para generar interfaces reactivas.  
    - Se considera un *frontend de backend* porque construye la interfaz de gestión directamente sobre la capa de datos y lógica del servidor.

- **Capa de presentación pública (frontend)**
  - Blade (sistema de plantillas de Laravel) para vistas públicas y/o portal académico.
  - Vite para la compilación de assets (JS/CSS).
  - npm para la gestión de dependencias frontend.

- **Base de datos**
  - Motor SQL (MySQL/MariaDB, PostgreSQL u otro compatible).

- **Herramientas adicionales**
  - Composer para dependencias PHP.
  - PHPUnit / pruebas de Laravel para tests automatizados.

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

## ⚙️ Instalación (entorno local)

> 👀 Si vas a contribuir con cambios al repositorio, revisa primero la sección  
> [Contribuir (vía fork)](#-contribuir-vía-fork).

### 1. Clonar el repositorio

```bash
git clone https://github.com/krypton612/montesori.git
cd montesori
