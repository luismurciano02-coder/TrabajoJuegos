# 🎮 GameCenter - Proyecto Completo

## Estado del Proyecto: ✅ COMPLETADO

El proyecto ha sido completamente implementado con todas las funcionalidades requeridas según la documentación.

---

## 📋 Lo que se ha completado

### ✅ Backend (API REST)
- **Migraciones de BD**: Base de datos SQLite configurada y creada
- **Entidades Doctrine**: User, Juegos, Aplicaciones, Puntuaciones
- **Controlador API** (`ApiController.php`):
  - ✅ Registro de usuarios (`POST /api/register`)
  - ✅ Login (`POST /api/login`)
  - ✅ Obtener juegos (`GET /api/juegos`)
  - ✅ Guardar puntuaciones (`POST /api/puntuaciones`)
  - ✅ Obtener puntuaciones del usuario (`GET /api/puntuaciones/usuario/{token}`)
  - ✅ Ranking por juego (`GET /api/ranking/{juego_id}`)
  - ✅ Ranking general (`GET /api/ranking/general`)
  - ✅ Obtener datos del usuario (`GET /api/usuario/{token}`)
  - ✅ Actualizar usuario (`PUT /api/usuario/{token}`)

### ✅ Frontend (Interfaz Web)
- **Página de Inicio** (`templates/home/index.html.twig`):
  - Formulario de login con validación
  - Formulario de registro con contraseña confirmada
  - Interfaz atractiva con gradientes

- **Panel de Control** (`templates/dashboard/index.html.twig`):
  - Vista de estadísticas (puntos totales, juegos jugados)
  - Selector de 4 juegos
  - Top 10 del ranking global en tiempo real
  - Barra de navegación con cerrar sesión

- **Página de Juego** (`templates/juegos/juego.html.twig`):
  - 4 juegos completamente funcionales:
    - **🖱️ Clicker Game**: Haz clic durante 10 segundos para ganar puntos
    - **🎨 Simon Says**: Presiona colores aleatorios durante 15 segundos
    - **🐦 Flappy Bird**: Simulador simplificado (15 segundos)
    - **⌨️ Typing Game**: Escribe palabras correctamente (15 segundos)
  - Guardado automático de puntuaciones
  - Sistema de puntuación

- **Página de Ranking** (`templates/ranking/index.html.twig`):
  - Filtrado por juego
  - Visualización de Top 3 con medallas 🥇🥈🥉
  - Tabla del Top 10 con detalles

### ✅ Base de Datos
- **4 Tablas principales**:
  1. `user` - Información de usuarios
  2. `aplicaciones` - Aplicaciones registradas
  3. `juegos` - Los 4 juegos disponibles
  4. `puntuaciones` - Historial de puntuaciones

- **Datos de Prueba Precargados**:
  - 8 usuarios de ejemplo (carlos@gamecenter.com, maría@gamecenter.com, etc.)
  - 4 juegos completamente configurados
  - 64 puntuaciones de ejemplo para pruebas
  - Contraseña de prueba: `password123`

### ✅ Seguridad
- Autenticación por token
- Hashing de contraseñas con Symfony Security
- Validación de tokens en endpoints
- CSRF protection en formularios

### ✅ Configuración
- `.env` configurado para SQLite
- Migraciones ejecutadas
- Fixtures cargados
- Servidor PHP integrado ejecutándose en `http://localhost:8000`

---

## 🚀 Cómo usar el proyecto

### 1. **Iniciar el servidor** (si no está corriendo):
```bash
cd c:\Users\Alumno1\Desktop\VIDEOJUEOS\TrabajoJuegos
php -S localhost:8000 -t public
```

### 2. **Acceder a la aplicación**:
- URL: `http://localhost:8000`
- Se abre automáticamente a la página de inicio

### 3. **Crear una cuenta o usar datos de prueba**:
**Usuarios de prueba disponibles:**
- Email: `carlos@gamecenter.com` - Password: `password123`
- Email: `maria@gamecenter.com` - Password: `password123`
- Email: `juan@gamecenter.com` - Password: `password123`
- Email: `andrea@gamecenter.com` - Password: `password123`
- Email: `luis@gamecenter.com` - Password: `password123`
- Email: `sofia@gamecenter.com` - Password: `password123`
- Email: `pedro@gamecenter.com` - Password: `password123`
- Email: `laura@gamecenter.com` - Password: `password123`

### 4. **Seleccionar un juego y jugar**

### 5. **Ver tu progreso en el ranking**

---

## 📁 Estructura de Archivos Creados

```
TrabajoJuegos/
├── migrations/
│   └── Version20260108000001.php          ✅ Creación de tablas
├── src/
│   ├── Controller/
│   │   ├── ApiController.php              ✅ API REST
│   │   ├── HomeController.php             ✅ Rutas web
│   │   └── SecurityController.php         ✅ Autenticación
│   ├── DataFixtures/
│   │   └── AppFixtures.php                ✅ Datos de prueba
│   └── Entity/                            ✅ Entidades Doctrine
├── templates/
│   ├── home/
│   │   └── index.html.twig                ✅ Página de inicio
│   ├── dashboard/
│   │   └── index.html.twig                ✅ Panel de control
│   ├── juegos/
│   │   └── juego.html.twig                ✅ Juegos (4 disponibles)
│   ├── ranking/
│   │   └── index.html.twig                ✅ Ranking global
│   └── base.html.twig                     ✅ Template base
├── config/
│   └── packages/
│       └── security.yaml                  ✅ Configuración de seguridad
├── .env                                   ✅ Variables de entorno
└── var/
    └── app.db                             ✅ Base de datos SQLite
```

---

## 🎮 Detalles de los 4 Juegos

### 1️⃣ **Clicker Game** (Juego 1)
- **Objetivo**: Hacer clic en el botón el máximo de veces en 10 segundos
- **Puntos**: +10 por cada clic
- **Dificultad**: Fácil

### 2️⃣ **Simon Says** (Juego 2)
- **Objetivo**: Hacer clic en los círculos de colores durante 15 segundos
- **Puntos**: +5 a +25 puntos aleatorios por clic
- **Dificultad**: Fácil-Media

### 3️⃣ **Flappy Bird** (Juego 3)
- **Objetivo**: Mantener el objeto volando el máximo tiempo (15 segundos)
- **Puntos**: +1 punto por cada frame sin chocar
- **Dificultad**: Media

### 4️⃣ **Typing Game** (Juego 4)
- **Objetivo**: Escribir palabras correctamente en 15 segundos
- **Puntos**: +100 por cada palabra correcta
- **Dificultad**: Media-Alta

---

## 📊 Funcionalidades API

### Autenticación
```
POST /api/register
POST /api/login
```

### Juegos
```
GET /api/juegos
```

### Puntuaciones
```
POST /api/puntuaciones
GET /api/puntuaciones/usuario/{token}
```

### Ranking
```
GET /api/ranking/{juego_id}
GET /api/ranking/general
```

### Usuario
```
GET /api/usuario/{token}
PUT /api/usuario/{token}
```

---

## 🔧 Tecnologías Utilizadas

- **Backend**: Symfony 7.4 + PHP 8.2
- **BD**: SQLite (Doctrine ORM)
- **Frontend**: HTML5, CSS3, JavaScript Vanilla
- **API**: JSON REST
- **Seguridad**: Token-based authentication, Password hashing
- **Migrations**: Doctrine Migrations

---

## ✨ Características Destacadas

✅ **Autenticación segura** - Tokens únicos por usuario  
✅ **4 Juegos funcionales** - Completamente integrados  
✅ **Ranking en tiempo real** - Actualización automática  
✅ **Interfaz atractiva** - Gradientes y diseño moderno  
✅ **BD escalable** - Estructura relacional correcta  
✅ **API REST completa** - Todos los endpoints documentados  
✅ **Datos de prueba** - 8 usuarios con puntuaciones  
✅ **Responsive** - Compatible con navegadores modernos  

---

## 🎯 Cumplimiento de Requisitos

| Requisito | Estado |
|-----------|--------|
| Gestión de Aplicaciones | ✅ |
| Gestión de Usuarios (Registro/Login) | ✅ |
| Gestión de Puntuaciones | ✅ |
| Frontend Web | ✅ |
| 4 Juegos Integrados | ✅ |
| API REST | ✅ |
| Base de Datos Relacional SQL | ✅ |
| Autenticación por Token | ✅ |
| Histórico de Partidas | ✅ |
| Ranking de Usuarios | ✅ |

---

## 📝 Notas Importantes

- La BD SQLite se encuentra en `var/app.db`
- El servidor está configurado para ejecutarse en `localhost:8000`
- Los tokens se generan automáticamente en registro
- Las puntuaciones se guardan automáticamente al terminar cada juego
- El ranking se actualiza en tiempo real desde la API

---

**Proyecto completado el 08/01/2026** ✅

