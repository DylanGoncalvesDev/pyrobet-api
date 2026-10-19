# PyroBet API - Motor Gamificado de Predicciones Deportivas

**PyroBet API** es el motor central (backend) de una plataforma de apuestas y predicciones deportivas. 

Su función principal es gestionar usuarios, ligas, equipos y partidos, permitiendo a los competidores registrar sus pronósticos de partidos. El sistema cuenta con un **cerebro automatizado de puntuación** que, al finalizar cada partido, evalúa los resultados reales y reparte puntos automáticamente a los usuarios según la precisión de sus predicciones, generando una tabla de posiciones (Ranking Global) en tiempo real.

---

## Matriz de Reglas de Negocio Gamificadas
La API evalúa de forma automatizada cada pronóstico bajo **5 reglas exactas de negocio** cuando un partido pasa a estar finalizado (`finished`):
*   **8 Puntos:** Marcador exacto perfecto (Ganador/Empate + Goles de ambos equipos).
*   **6 Puntos:** Acierto de goles de ambos equipos pero fallo en el resultado global (Especial de empates lógicos).
*   **4 Puntos:** Acierto de ganador/empate + Goles de al menos un equipo de forma exacta.
*   **2 Puntos:** Fallo de resultado global, pero acierto de goles de un equipo de forma aislada.
*   **1 Punto:** Acierto exclusivo del resultado seco (Quién gana o empata) sin pegar a ningún gol exacto.

---

## 🛠️ Guía de Instalación

### Paso 0: Preparación del Entorno Operativo
Antes de clonar el código, la computadora necesita el "motor" para interpretar PHP y el gestor de dependencias Composer. Puedes configurar tu máquina usando herramientas automatizadas o de forma manual por consola:

#### Camino A: Instalación Automática con Laravel Herd (Recomendado)
*   **Windows / Mac:** Descarga e instala **[Laravel Herd](https://laravel.com)**. Es una herramienta oficial que instala PHP 8.3/8.4 nativo en un segundo, configurando los entornos de forma automática y transparente sin tocar la terminal. Si estás en Windows, descarga también **[Composer](https://getcomposer.org)** de forma manual.

#### Camino B: Instalación Manual con PHP y Composer Tradicional

*   **En Windows:** Descarga los binarios oficiales de **[PHP para Windows](https://php.net)** (Versión 8.3 o 8.4 Thread Safe), agrégalo a las Variables de Entorno del Sistema (`PATH`) y luego instala **[Composer](https://getcomposer.org)**.

*   **En Mac (Intel / Apple Silicon M1/M2/M3):** Abre tu terminal e instala todo limpiamente mediante el gestor Homebrew ejecutando:
    ```bash
    brew install php composer
    ```

#### Validar la Instalación Global:
Independientemente del camino que hayas elegido, abre una terminal nueva y confirma que tu sistema reconozca ambos comandos de par en par:
```bash
php --version       # Debe devolver PHP 8.3.x o 8.4.x
composer --version  # Debe devolver Composer 2.x
```

---

### Pasos para la Puesta en Marcha en Consola

#### Paso 1: Clonar el Repositorio e Instalar Dependencias
Al descargar el código desde GitHub, notarás que no incluye la carpeta `vendor` (el núcleo del framework) para no pesar megabytes innecesarios. Debemos forzar su descarga:
```bash
# 1. Clonar el repositorio oficial desde tu cuenta de GitHub
git clone https://github.com

# 2. Entrar de inmediato a la raíz física del proyecto
cd pyrobet-api

# 3. Descargar e instalar Laravel y todos sus paquetes de dependencias de par en par
composer install
```

#### Paso 2: Configuración del Archivo `.env` y la Base de Datos SQLite
Laravel jamás guarda claves, contraseñas ni rutas de conexión dentro del código por pura seguridad informática. En su lugar, lee un archivo de texto oculto en la raíz del proyecto llamado **`.env`** (Variables de Entorno).

1. **Crear tu copia local del archivo:**
   * **En Windows (PowerShell):** `copy .env.example .env`
   * **En Mac / Linux (Terminal):** `cp .env.example .env`
2. **Configurar la Conexión de Datos:** Abre ese nuevo archivo **`.env`** en tu VS Code. Busca la sección de base de datos y asegúrate de dejarla exactamente así para usar **SQLite**:
   ```text
   DB_CONNECTION=sqlite
   ```
3. **Generar la firma criptográfica única de tu servidor:**
   ```bash
   php artisan key:generate
   ```

#### Paso 3: Inicialización de Tablas y Motor de Seguridad (Passport)
Ejecuta estos comandos para construir tu base de datos y levantar las tablas transaccionales (incluyendo usuarios, partidos, apuestas y el sistema de sesiones de Laravel 13):
```bash
# 1. Crear el esquema de tablas en limpio en tu archivo SQLite local
php artisan migrate:fresh

# 2. Instalar el búnker de Passport (Genera las llaves de cifrado para emitir tus Bearer Tokens)
php artisan passport:client --personal
```

#### Paso 4: Encender la API (Elige según tu entorno)
*   **Si usas PHP tradicional por terminal o Herd en Windows:** Pon a escuchar al servidor levantando el comando nativo de Laravel en tu consola:
    ```bash
    php artisan serve
    ```
    *E ingresa directo a tu búnker interactivo de Swagger en la dirección: `http://localhost:8000/api/documentation`*.
*   **Si usas Laravel Herd en Mac:** Tu servidor Nginx corre de forma permanente en segundo plano. No necesitas ejecutar el comando `serve`, puedes entrar directo a través del enrutador mapeado por la herramienta: `http://localhost/api/documentation`.

---

## Solución a Complicaciones Críticas en la Instalación

Si  experimentan bloqueos durante la instalación, aquí están las soluciones definitivas de nivel Senior para resolverlos al instante:

### Complicación 1: Error `Driver [sqlite] not supported` o `Missing extension pdo_sqlite`
*   **Causa:** Ocurre si estás instalando el entorno de forma manual en Windows/Mac y tu PHP no tiene habilitada la extensión interna para leer archivos SQLite.
*   **Solución en Windows:** Abre tu archivo `php.ini`, busca la línea `;extension=pdo_sqlite` y quítale el punto y coma ( `;` ) inicial para activarla. Guarda el archivo y reinicia la consola.
*   **Solución en Mac:** Si instalaste PHP con Homebrew, la extensión viene nativa. Si te da error, reinstala el paquete de datos ejecutando `brew reinstall php`.

### Complicación 2: Error de Permisos en Mac/Linux (`Permission Denied` / `Error 403`)
*   **Síntoma:** Al intentar compilar la documentación de Swagger o registrar datos, la consola de Mac estalla diciendo que no tiene permisos de escritura en el disco.
*   **Causa:** Los sistemas Unix (Mac/Linux) protegen las carpetas de fábrica e impiden de forma estricta que Laravel escriba archivos temporales e inyecte datos en la caché.
*   **Solución:** Dale permisos plenos de lectura y escritura al framework corriendo este comando en la terminal de tu Mac:
    ```bash
    sudo chmod -R 775 storage bootstrap/cache
    ```

### Complicación 3: El comando `php artisan serve` da error de puerto ocupado (`Address already in use`)

*   **Causa:** Tienes otra terminal fantasma encendida en segundo plano ocupando el puerto `:8000` de tu computadora.
*   **Solución:** Puedes apagar el proceso anterior o forzar a Laravel a encender la API en un puerto libre alternativo usando la bandera de red en tu consola:
    ```bash
    php artisan serve --port=8001
    ```
    *Luego accede a tu Swagger cambiando el puerto al final de la URL: `http://localhost:8001/api/documentation`*.

### Complicación 4: Error `SQLSTATE[HY000]: General error: 1 no such table: sessions`
*   **Causa:** En Laravel 13, el middleware global rastrea las visitas guardando metadatos en la tabla `sessions`. Al limpiar la base de datos, si el servidor está encendido, Windows bloquea el archivo `.sqlite` e impide que la tabla se cree de forma correcta.
*   **Solución:** Nuestra migración base ya incluye la inyección atómica de la tabla de sesiones. Asegúrate de apagar el servidor con `Ctrl + C`, ejecutar `php artisan migrate:fresh` en limpio y volver a encender el comando `serve`.

---

## Documentación y Consumo de la API

### Bloque 1: El Control de Accesos (Autenticación)
Para que un usuario pueda guardar apuestas y sumar puntos, lo primero que debe hacer el Frontend es gestionar su identidad:
*   **Registro de Usuarios (`/api/register`):** El usuario rellena el formulario con su nombre, correo y contraseña. El Frontend envía estos datos a la API y el servidor crea un nuevo competidor en la base de datos SQLite con el rol básico de `user`.
*   **Inicio de Sesión (`/api/login`):** El usuario introduce sus credenciales. Si coinciden, la API le entrega una llave digital única e invisible llamada **Bearer Token (Laravel Passport)**. El Frontend debe guardar esta llave en la memoria del navegador (`localStorage`) para mantener la sesión abierta.
*   **Cierre de Sesión (`/api/logout`):** Cuando el usuario decide salir, el Frontend envía la llave de vuelta a la API para destruirla, asegurando que nadie más pueda usar esa cuenta.

### Bloque 2: Administración del Torneo (Solo Administradores)
Son los endpoints que utiliza el panel de control para crear las reglas del juego. Requieren que el usuario tenga el rol de `admin` y envíe su llave Bearer:
*   **Competiciones (`/api/competitions`):** Permite crear, listar y editar las ligas. También incluye la opción de **Eliminar una competición** por completo si el torneo se cancela.
*   **Equipos (`/api/teams`):** Permite dar de alta y modificar los clubes o selecciones. Incluye la acción de **Eliminar un equipo** de la base de datos si deja de participar en la plataforma.

### Bloque 3: El Motor del Juego y las Apuestas (Usuarios y Administradores)
Aquí es donde ocurre la magia de PyroBet y se conecta el flujo de datos:
*   **Listar Partidos (`/api/matches` - GET):** Es una ruta libre. El Frontend la consulta para mostrar en la pantalla todos los encuentros agendados.
*   **Crear, Editar y Eliminar Partidos (`/api/matches`):** Exclusivo de administradores. Permite agendar partidos, actualizar el marcador real o **Eliminar un partido** de la persistencia por completo en caso de suspensión.
*   **Guardar y Modificar Predicciones (`/api/predictions`):** El usuario autenticado (enviando su llave Bearer) guarda su pronóstico de goles. También tiene la opción de **Eliminar una predicción propia** si decide retirar su pronóstico antes de que inicie el juego.

### Bloque 4: La Recompensa (El Cerebro Gamificado)
*   **El Reparto Automático de Puntos:** En el momento exacto en que el administrador marca un partido como finalizado (`finished`), la API activa su cerebro matemático por detrás. Compara los goles reales con las predicciones de todos los usuarios y calcula cuántos puntos ganó cada uno según las **5 reglas estrictas de precisión**.
*   **Consultar el Ranking Global (`/api/ranking`):** El Frontend consulta este endpoint para mostrar la gran tabla de posiciones. La API procesa de forma síncrona la base de datos, suma los puntos totales de cada competidor y devuelve la lista ordenada de mayor a menor en tiempo real.

### Swagger:

*   **Documentación Automatizada:** Compilada bajo la especificación global **OpenAPI 3.1.0** utilizando el paquete `darkaonline/l5-swagger`.
*   **Sintaxis Nativa de Laravel 13:** Toda la documentación utiliza **Atributos nativos de PHP 8.3+** (`#[OA\Info]`), garantizando la compatibilidad con el compilador moderno.
*   **Ruta Local de la API Interactiva:** 
     `http://localhost:8000/api/documentation`

---
