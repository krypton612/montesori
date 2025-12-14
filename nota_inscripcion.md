## Contexto general del nuevo enfoque de inscripción

El sistema pasa de una inscripción centrada en **grupo** a una inscripción **basada en curso**.  
El **curso** es ahora la unidad mínima y obligatoria de inscripción.  
El **grupo** sigue existiendo, pero es **opcional** y solo aplica a inscripciones regulares.

Reglas clave:
- `curso_id` → **obligatorio siempre**
- `grupo_id` → **opcional**
  - `grupo_id != null` → inscripción **regular**
  - `grupo_id == null` → inscripción **irregular** (sin grupo, pero válida)

---

## Tabla: estudiantes

### Propósito
Almacena la información base del estudiante.  
No define modalidad de inscripción ni pertenencia a grupos o cursos de forma directa.

### Impacto con el nuevo enfoque
- **No cambia estructuralmente**
- Un estudiante puede:
  - Estar inscrito en múltiples cursos
  - Tener inscripciones regulares e irregulares en paralelo

### Relación clave
- 1 estudiante → N inscripciones

---

## Tabla: cursos

### Propósito
Representa la **unidad académica inscribible** (materia, nivel, modalidad, etc.).

### Rol en el nuevo enfoque
- Se convierte en el **eje central** de la inscripción.
- Toda inscripción **debe** apuntar a un curso.

### Reglas
- Un curso puede existir:
  - Con grupos asociados (clases regulares)
  - Sin grupos (clases personalizadas, refuerzos, casos especiales)

### Relación clave
- 1 curso → N inscripciones
- 1 curso → N grupos (opcional)

---

## Tabla: grupos

### Propósito
Agrupa estudiantes dentro de un curso bajo un esquema regular  
(ej: “Primero A”, “Primero B”).

### Nuevo rol
- **Entidad auxiliar**
- Solo se usa cuando la inscripción es regular

### Reglas
- Un grupo **siempre pertenece a un curso**
- Un grupo **no es obligatorio** para que exista una inscripción

### Relación clave
- 1 grupo → 1 curso
- 1 grupo → N inscripciones (solo regulares)

---

## Tabla: inscripciones

### Propósito
Vincula al estudiante con un curso en una gestión determinada.

### Cambios clave
Esta tabla concentra toda la lógica del nuevo enfoque.

### Campos relevantes
- `estudiante_id` → obligatorio
- `curso_id` → **obligatorio**
- `grupo_id` → **nullable**
- `gestion_id` → obligatorio

### Interpretación lógica
- `grupo_id != null`
  - Inscripción regular
  - El estudiante pertenece a un grupo del curso
- `grupo_id == null`
  - Inscripción irregular
  - El estudiante está inscrito al curso sin grupo asignado

⚠️ Importante:
El hecho de no tener grupo **no invalida** la inscripción.

---

## Tabla: gestion

### Propósito
Define el período académico (año, semestre, ciclo).

### Impacto
- No cambia conceptualmente
- Sigue delimitando la validez temporal de cursos e inscripciones

### Relación clave
- 1 gestión → N inscripciones
- 1 gestión → N cursos (según diseño)

---

## Conclusión técnica clara

- El **curso** es la verdad absoluta de la inscripción
- El **grupo** es una capa organizativa opcional
- El sistema soporta:
  - Inscripciones regulares (curso + grupo)
  - Inscripciones irregulares (solo curso)
- No se necesitan grupos “especiales” para casos individuales
- Se elimina el acoplamiento forzado estudiante → grupo

Este diseño es más flexible, escalable y realista para una institución educativa.
