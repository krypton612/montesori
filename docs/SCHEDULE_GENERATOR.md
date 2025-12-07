# Generador de Horarios - Sistema Montesori

## 📋 Descripción

El **Generador de Horarios** es un módulo aislado del sistema Montesori que permite generar, validar y reorganizar horarios académicos para cursos, considerando múltiples restricciones como conflictos de profesores, disponibilidad de aulas y capacidades.

## 🎯 Características

- ✅ Generación automática de horarios para cursos
- ✅ Validación de conflictos en horarios existentes
- ✅ Reorganización de horarios actuales
- ✅ Respeto de restricciones duras (profesores, aulas, capacidad)
- ✅ Distribución equitativa de horas semanales
- ✅ Servicio completamente aislado e independiente

## 🧮 Problema NP-Completo

### Tipo de Problema: Constraint Satisfaction Problem (CSP)

La asignación de horarios académicos es un problema de **satisfacción de restricciones (CSP)** que pertenece a la clase de complejidad **NP-Completo**. Esto significa que:

#### 1. **Complejidad Computacional**
No existe un algoritmo conocido que pueda resolver el problema en tiempo polinomial para todos los casos. La complejidad crece exponencialmente con:
- Número de cursos (n)
- Número de aulas (m)
- Número de franjas horarias (t)
- Número de días disponibles (d)

La complejidad aproximada es: **O(n × m × t × d)**

#### 2. **Restricciones Duras (Hard Constraints)**
Estas restricciones **DEBEN** ser satisfechas:
- ❌ Un profesor no puede estar en dos lugares al mismo tiempo
- ❌ Un aula no puede albergar dos cursos simultáneamente
- ❌ Los horarios deben respetar la capacidad del aula
- ❌ Las horas deben estar dentro de los bloques horarios definidos
- ❌ Los cursos deben respetar su turno asignado (mañana o tarde)
- ✅ Distribución equitativa de clases a lo largo de la semana (no lineal)

#### 3. **Restricciones Blandas (Soft Constraints)**
Estas restricciones son **deseables** pero no obligatorias:
- ⚠️ Distribuir equitativamente las horas semanales
- ⚠️ Minimizar ventanas horarias para profesores
- ⚠️ Respetar preferencias de horarios
- ⚠️ Agrupar materias relacionadas

#### 4. **Sistema de Prioridades**
El algoritmo prioriza las materias según su carga horaria:
- **Materias prioritarias** (horas_semanales > 4): Se asignan primero y reciben las primeras horas del turno
  - Turno mañana: 08:00-09:00, 09:00-10:00, etc.
  - Turno tarde: 14:00-15:00, 15:00-16:00, etc.
- **Materias regulares** (horas_semanales ≤ 4): Se asignan después en cualquier hora disponible del turno

Este sistema garantiza que las materias más importantes tengan los mejores horarios.

#### 5. **Enfoque de Solución**

El servicio implementa un **algoritmo heurístico con backtracking y distribución equitativa**:

```
1. Ordenar cursos por prioridad (materias con más horas semanales primero)

Para cada curso (en orden de prioridad):
    Calcular distribución ideal: horas / días de la semana
    
    Para cada día de la semana:
        Asignar hasta N horas por día (distribución equitativa)
        
        Para cada bloque horario (según prioridad de materia):
            Buscar aula disponible
            Verificar restricciones:
                - ¿Profesor libre?
                - ¿Aula libre?
                - ¿Capacidad suficiente?
                - ¿Turno correcto?
            Si todas las restricciones se cumplen:
                Asignar horario
            Si no:
                Continuar buscando
    
    Si no se pueden asignar todas las horas:
        Reportar conflicto
```

**Características del algoritmo:**
- **Distribución no lineal**: Las clases se reparten equitativamente entre los días de la semana
- **Sistema de prioridades**: Las materias con más horas semanales se procesan primero
- **Asignación por bloques**: No se llenan todos los bloques de un día antes de pasar al siguiente
- **Respeto de turnos**: Mañana (08:00-13:00) y tarde (14:00-19:00) separados

**Nota**: Este algoritmo puede no encontrar la solución óptima en todos los casos, pero garantiza que todas las restricciones duras se respeten y que la distribución sea equitativa.

**Ejemplo de distribución**:

```
ANTES (Lineal - Incorrecto):
Curso con 10 horas semanales:
- Lunes 08:00-09:00, 09:00-10:00, 10:00-11:00, 11:00-12:00, 12:00-13:00
- Martes 08:00-09:00, 09:00-10:00, 10:00-11:00, 11:00-12:00, 12:00-13:00

AHORA (Distribuido - Correcto):
Curso con 10 horas semanales:
- Lunes 08:00-09:00, 09:00-10:00
- Martes 08:00-09:00, 09:00-10:00
- Miércoles 08:00-09:00, 09:00-10:00
- Jueves 08:00-09:00, 09:00-10:00
- Viernes 08:00-09:00, 09:00-10:00
```

#### 6. **Escalabilidad**

Para un ejemplo típico:
- 20 cursos
- 10 aulas
- 9 bloques horarios por día
- 5 días a la semana

El espacio de búsqueda es: **20 × 10 × 9 × 5 = 9,000 combinaciones posibles**

Con cursos de 4 horas semanales cada uno: **9,000^4 ≈ 6.5 × 10^15 combinaciones totales**

## 🚀 Uso

### Como Servicio (en Código)

```php
use App\Services\ScheduleGeneratorService;
use App\Models\Curso;

// Instanciar el servicio
$scheduleGenerator = new ScheduleGeneratorService();

// Obtener cursos a procesar
$cursos = Curso::with(['materia', 'profesor'])
    ->where('gestion_id', 2024)
    ->where('habilitado', true)
    ->get();

// Generar horarios
$result = $scheduleGenerator->generateSchedules($cursos);

if ($result['success']) {
    // Aplicar horarios a la base de datos
    $scheduleGenerator->applySchedules($result['schedules'], $clearExisting = true);
    
    echo "✅ Horarios generados: " . count($result['schedules']);
} else {
    echo "❌ Conflictos encontrados:";
    foreach ($result['conflicts'] as $conflict) {
        echo "\n  • " . $conflict;
    }
}
```

### Como Comando Artisan

#### 1. Generar horarios para cursos específicos

```bash
php artisan schedule:generate --curso=1 --curso=2 --curso=3
```

#### 2. Generar horarios para una gestión completa

```bash
php artisan schedule:generate --gestion=2024
```

#### 3. Validar horarios existentes

```bash
php artisan schedule:generate --validate
```

#### 4. Reorganizar horarios existentes

```bash
php artisan schedule:generate --reorganize --curso=1 --curso=2
```

#### 5. Generar y aplicar automáticamente

```bash
php artisan schedule:generate --gestion=2024 --apply
```

### Opciones del Comando

| Opción | Descripción | Ejemplo |
|--------|-------------|---------|
| `--gestion=ID` | Filtrar cursos por gestión | `--gestion=2024` |
| `--curso=ID` | Procesar cursos específicos (puede repetirse) | `--curso=1 --curso=2` |
| `--apply` | Aplicar los horarios generados a la BD | `--apply` |
| `--validate` | Solo validar horarios existentes | `--validate` |
| `--reorganize` | Reorganizar horarios existentes | `--reorganize` |

## 📊 Métodos del Servicio

### `generateSchedules($cursos, $options = [])`

Genera horarios para un conjunto de cursos.

**Parámetros:**
- `$cursos` - Collection o array de objetos Curso
- `$options` - Array de opciones adicionales (reservado para futuras extensiones)

**Retorna:**
```php
[
    'success' => bool,
    'schedules' => [
        [
            'curso_id' => 1,
            'aula_id' => 5,
            'dia' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '09:00',
        ],
        // ... más horarios
    ],
    'conflicts' => [
        'Mensaje de conflicto 1',
        'Mensaje de conflicto 2',
    ]
]
```

### `validateExistingSchedules($gestionId = null)`

Valida los horarios existentes y detecta conflictos.

**Parámetros:**
- `$gestionId` - (Opcional) ID de gestión para filtrar cursos

**Retorna:**
```php
[
    'conflicts' => [
        [
            'type' => 'classroom', // o 'professor'
            'message' => 'Descripción del conflicto',
            'horarios' => [1, 2], // IDs de horarios en conflicto
        ],
        // ... más conflictos
    ],
    'statistics' => [
        'total_schedules' => 120,
        'professor_conflicts' => 2,
        'classroom_conflicts' => 3,
    ]
]
```

### `applySchedules($schedules, $clearExisting = false)`

Aplica horarios generados a la base de datos.

**Parámetros:**
- `$schedules` - Array de horarios a aplicar
- `$clearExisting` - Si debe eliminar horarios existentes de los mismos cursos

**Retorna:** `bool` - true si se aplicaron correctamente, false en caso de error

### `reorganizeSchedules($cursoIds)`

Reorganiza horarios de cursos específicos.

**Parámetros:**
- `$cursoIds` - Array de IDs de cursos a reorganizar

**Retorna:** Mismo formato que `generateSchedules()`

## 🔧 Configuración

### Días de la Semana

Por defecto, el generador trabaja con:
```php
['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']
```

### Bloques Horarios

El generador tiene diferentes bloques horarios según el turno:

**Turno Mañana:**
```php
['08:00', '09:00'],
['09:00', '10:00'],
['10:00', '11:00'],
['11:00', '12:00'],
['12:00', '13:00'],
```

**Turno Tarde:**
```php
['14:00', '15:00'],
['15:00', '16:00'],
['16:00', '17:00'],
['17:00', '18:00'],
['18:00', '19:00'],
```

El servicio detecta automáticamente el turno del curso basándose en el campo `hora_inicio` del modelo `Turno`:
- Si `hora_inicio` es antes de las 14:00, se considera turno de mañana
- Si `hora_inicio` es a las 14:00 o después, se considera turno de tarde
- Si el curso no tiene turno asignado, puede usar cualquier bloque horario

Estos valores están definidos como constantes en la clase `ScheduleGeneratorService` y pueden ser modificados según las necesidades de la institución.

## 🧪 Pruebas

El módulo incluye una suite completa de pruebas:

```bash
# Ejecutar todas las pruebas del servicio
php artisan test --filter ScheduleGeneratorServiceTest

# Ejecutar una prueba específica
php artisan test --filter test_generates_schedules_for_single_course
```

### Cobertura de Pruebas

- ✅ Generación de horarios para un curso
- ✅ Generación de horarios para múltiples cursos
- ✅ Detección de conflictos de profesor
- ✅ Detección de conflictos de aula
- ✅ Respeto de capacidad de aulas
- ✅ Validación de horarios existentes
- ✅ Aplicación de horarios a la base de datos
- ✅ Reorganización de horarios
- ✅ Respeto del turno de mañana
- ✅ Respeto del turno de tarde
- ✅ Separación de cursos por turno
- ✅ Distribución equitativa a lo largo de la semana
- ✅ Sistema de prioridades para materias importantes

## 📁 Estructura de Archivos

```
app/
├── Services/
│   └── ScheduleGeneratorService.php    # Servicio principal
├── Console/
│   └── Commands/
│       └── GenerateSchedulesCommand.php # Comando Artisan
tests/
└── Feature/
    └── Services/
        └── ScheduleGeneratorServiceTest.php # Pruebas
docs/
└── SCHEDULE_GENERATOR.md                # Esta documentación
```

## 🔐 Seguridad y Transacciones

El servicio utiliza transacciones de base de datos al aplicar horarios:

```php
try {
    DB::beginTransaction();
    // Aplicar cambios
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    // Manejar error
}
```

Esto garantiza la integridad de los datos en caso de errores.

## 🚧 Limitaciones Conocidas

1. **Algoritmo No Óptimo**: Debido a la naturaleza NP-Completa del problema, el algoritmo puede no encontrar la mejor solución en todos los casos.

2. **Escalabilidad**: Con un número muy grande de cursos (>100), el tiempo de procesamiento puede aumentar significativamente.

3. **Restricciones Blandas**: Actualmente solo se implementan restricciones duras. Las restricciones blandas (preferencias) no están consideradas.

4. **Bloques Horarios Fijos**: Los bloques de tiempo están predefinidos y no pueden variar por día o curso.

## 🔮 Futuras Mejoras

- [ ] Implementar restricciones blandas con sistema de pesos
- [ ] Algoritmo genético para optimización
- [ ] Interfaz gráfica para visualización de horarios
- [ ] Exportación a PDF y Excel
- [ ] Consideración de preferencias de profesores
- [ ] Optimización de ventanas horarias
- [ ] Bloques horarios personalizables por día
- [ ] Manejo de materias con duraciones variables (2 horas consecutivas)

## 📝 Ejemplo Completo

```php
<?php

use App\Services\ScheduleGeneratorService;
use App\Models\Curso;

// Crear instancia del servicio
$service = new ScheduleGeneratorService();

// Obtener cursos de la gestión 2024
$cursos = Curso::with(['materia', 'profesor'])
    ->where('gestion_id', 2024)
    ->where('habilitado', true)
    ->get();

// Generar horarios
$resultado = $service->generateSchedules($cursos);

// Verificar resultado
if ($resultado['success']) {
    echo "✅ Generación exitosa!\n";
    echo "📊 Total de horarios generados: " . count($resultado['schedules']) . "\n";
    
    // Aplicar a la base de datos
    if ($service->applySchedules($resultado['schedules'], true)) {
        echo "💾 Horarios guardados en la base de datos\n";
    }
} else {
    echo "❌ No se pudieron generar todos los horarios\n";
    echo "⚠️ Conflictos encontrados:\n";
    foreach ($resultado['conflicts'] as $conflicto) {
        echo "  • " . $conflicto . "\n";
    }
}

// Validar horarios después de aplicarlos
$validacion = $service->validateExistingSchedules(2024);

echo "\n📈 Estadísticas:\n";
echo "  • Total de horarios: " . $validacion['statistics']['total_schedules'] . "\n";
echo "  • Conflictos de profesor: " . $validacion['statistics']['professor_conflicts'] . "\n";
echo "  • Conflictos de aula: " . $validacion['statistics']['classroom_conflicts'] . "\n";
```

## 👥 Contribuciones

Este módulo está diseñado para ser extensible y mantenible. Al agregar nuevas funcionalidades:

1. Mantener el servicio aislado e independiente
2. Agregar pruebas para nuevas funcionalidades
3. Documentar cambios en este README
4. Respetar las convenciones de código del proyecto

## 📄 Licencia

Este módulo es parte del Sistema Montesori y está sujeto a la misma licencia del proyecto principal.

---

**Nota**: Este módulo implementa una solución práctica al problema de asignación de horarios. Para casos de uso muy específicos o con requisitos complejos, puede ser necesario ajustar el algoritmo o implementar técnicas más avanzadas de optimización.
