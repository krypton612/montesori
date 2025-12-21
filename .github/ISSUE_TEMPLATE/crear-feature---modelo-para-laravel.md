---
name: Crear feature - modelo para laravel
about: Permite crear issues especificos para el area de base de datos.
title: "[FEATURE]"
labels: enhancement, settings
assignees: ''

---

# 🚀 Feature: Creación de Modelo y Migración en Laravel

## 📘 Descripción
Describa brevemente el propósito del modelo y su migración.  
Ejemplo: “Crear el modelo **Student** con su respectiva migración y relaciones principales.”

---

## 🧱 Tabla / Estructura del Modelo
Complete los campos necesarios para generar la migración:

**Nombre del modelo:**  
**Nombre de la tabla:**  

### 🗂️ Columnas
Liste cada columna en formato simplificada:

para generar una migracion con los atributos basicos ejecutar

`php artisan make:migration create_nombreTabla_table --table=nombre_tabla`

| **Nombre columna** | **Tipo** | **Nullable** | **Default** | **Unique** | **Comentarios** |
|--------------------|----------|--------------|-------------|------------|------------------|
| id         | int   | no           | n+1           | si         | Llave primaria |
| ...          | ...   | ...           | ...           | ...         | ... |
| created_at             | datetime     | null           | ahora      | no         | Cuando se crea el registro |
| updated_at             | datetime     | null           | ahora      | no         | Cuando se actualiza el registro |


### 🔗 Relaciones
- **belongsTo:**  
- **hasMany:**  
- **belongsToMany:**  
- **hasOne:**  

---

## 🎯 Criterios de Aceptación

Describa qué debe cumplirse para considerar terminada esta feature:

- [ ] **Migración creada** con todas sus columnas correctamente definidas  
- [ ] **Modelo creado** con `fillable`, `casts` y relaciones  
- [ ] **Factory creado** (si aplica)  
- [ ] **Seeder creado** (si aplica)  
- [ ] **Pruebas básicas** para validar creación del modelo (opcional)  
- [ ] Cumple estándares del proyecto (nombres, orden, consistencia)

---

## ⚠️ Dependencias
Indique si este feature depende de otros issues, módulos o tablas.

- **Este feature posee dependencias?**  
  - [ ] Sí  
  - [ ] No  

**Si la respuesta es SÍ:**  
> 🔥 *Debe revisar y validar primero los issues con dependencias: **#1** y **#4***  
> Hasta que ambos estén resueltos o aprobados, este issue no puede cerrarse.

Liste las dependencias específicas aquí:  
- #__  
- #__  

---

## 📎 Notas Técnicas
Agregue información adicional: convenciones, índices, restricciones, triggers, etc.

---

## 📷 Anexos (si aplica)
Adjunte diagramas, ERDs o imágenes relevantes.

---

## ✍️ Autor
**Creado por:**  [Ronald Diaz](https://github.com/krypton612)
