# 🧪 GUÍA DE PRUEBAS - GameCenter

## ✅ Verificación del Proyecto Completado

Esta guía te ayudará a verificar que todo el proyecto está funcionando correctamente.

---

## 🚀 PASO 1: Iniciar el Servidor

### Opción A (Automática):
Haz doble clic en: `start-server.bat`

### Opción B (Manual):
```powershell
cd c:\Users\Alumno1\Desktop\VIDEOJUEOS\TrabajoJuegos
php -S localhost:8000 -t public
```

**Esperado**: Deberías ver:
```
[Thu Jan  8 10:49:02 2026] PHP 8.2.12 Development Server 
(http://localhost:8000) started
```

---

## 🌐 PASO 2: Acceder a la Aplicación

Abre tu navegador y ve a: **http://localhost:8000**

**Esperado**: Se abre la página de login/registro con gradiente morado

---

## 👤 PASO 3: Prueba de Autenticación

### 3.1 - Opción A: Usar Usuario de Prueba
1. Haz clic en "Iniciar Sesión"
2. Email: `carlos@gamecenter.com`
3. Contraseña: `password123`
4. Haz clic en "Entrar"

**Esperado**: Deberías ver el panel de control con:
- Tu nombre (Carlos)
- Botones de 4 juegos
- Tabla de ranking

### 3.2 - Opción B: Crear Nueva Cuenta
1. Haz clic en "Crear Cuenta"
2. Email: `tumail@ejemplo.com`
3. Nombre: `Tu Nombre`
4. Contraseña: `password123`
5. Confirmar: `password123`
6. Haz clic en "Registrarse"

**Esperado**: Eres redirigido automáticamente al panel de control

---

## 🎮 PASO 4: Prueba de Juegos

### 4.1 - Jugar Clicker Game
1. En el panel, haz clic en "🖱️ Clicker Game"
2. Haz clic en "Comenzar Juego"
3. Haz clic lo máximo posible en el botón durante 10 segundos
4. El juego termina automáticamente

**Esperado**:
- Ves tu puntuación aumentar
- Al terminar, aparece: "¡Juego finalizado! Puntuación: XXX - Puntuación guardada ✓"
- El botón "Comenzar Juego" se activa nuevamente

### 4.2 - Jugar Simon Says
1. Vuelve al panel (haz clic en "Volver")
2. Haz clic en "🎨 Simon Says"
3. Haz clic en "Comenzar Juego"
4. Haz clic en los círculos de colores durante 15 segundos
5. El juego termina automáticamente

**Esperado**:
- Los círculos parpadean al hacer clic
- Tu puntuación aumenta (5-25 puntos por clic)
- Mensaje de finalización similar al anterior

### 4.3 - Jugar Flappy Bird
1. Vuelve al panel
2. Haz clic en "🐦 Flappy Bird"
3. Haz clic en "Comenzar Juego"
4. Haz clic para mantener el objeto volando durante 15 segundos
5. El juego termina (o antes si el objeto cae)

**Esperado**:
- Ves un objeto amarillo en la pantalla
- Tu puntuación aumenta en cada frame
- El mensaje de finalización aparece

### 4.4 - Jugar Typing Game
1. Vuelve al panel
2. Haz clic en "⌨️ Typing Game"
3. Haz clic en "Comenzar Juego"
4. Escribe la palabra que aparece (ej: "GAMER")
5. Si es correcta, se borra y genera una nueva
6. El juego dura 15 segundos

**Esperado**:
- Ves una palabra en grande (GAMER, JAVASCRIPT, JUEGO, etc.)
- Tu puntuación salta +100 por cada palabra correcta
- Puedes escribir múltiples palabras en 15 segundos

---

## 📊 PASO 5: Verificar Estadísticas

### 5.1 - Panel de Control
1. Vuelve a "http://localhost:8000/dashboard"
2. Verifica:
   - Tu nombre aparece arriba a la derecha
   - "Total de Puntos" muestra tu puntuación acumulada
   - "Juegos Jugados" muestra cuántas veces has jugado

**Esperado**: Los números coinciden con tus juegos

### 5.2 - Tabla de Ranking
En el mismo panel, ve la tabla "Top 10 Global":
- Debería mostrar usuarios y sus puntuaciones
- Si acabas de jugar, deberías aparecer en la tabla

**Esperado**: Tu usuario aparece en el ranking

---

## 🏆 PASO 6: Prueba de Ranking

1. En el panel, busca un botón de "Ranking" (si existe)
2. O navega a: `http://localhost:8000/ranking`

**Esperado**:
- Ves una página con el título "🏆 Ranking Global"
- Hay botones para cada juego (Clicker Game, Simon Says, etc.)
- Ves el Top 3 con medallas 🥇🥈🥉
- Ves una tabla del Top 10 para cada juego

### 6.1 - Filtrar por Juego
1. Haz clic en uno de los botones de juego (ej: "Clicker Game")
2. El botón cambia de color (se vuelve purpura)
3. El ranking se actualiza para ese juego específico

**Esperado**: Solo ves puntuaciones de ese juego

---

## 🔌 PASO 7: Prueba de API (Opcional)

Si tienes Postman o curl, prueba los endpoints:

### Registro:
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","nombre":"Test","password":"pass123"}'
```

**Esperado**: Respuesta JSON con id, email, token

### Login:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"carlos@gamecenter.com","password":"password123"}'
```

**Esperado**: Respuesta JSON con token

### Obtener Juegos:
```bash
curl http://localhost:8000/api/juegos
```

**Esperado**: Array JSON con 4 juegos

### Guardar Puntuación:
```bash
curl -X POST http://localhost:8000/api/puntuaciones \
  -H "Content-Type: application/json" \
  -d '{"token":"TU_TOKEN_AQUI","juego_id":1,"puntuacion":500}'
```

**Esperado**: Respuesta con id y fecha

### Obtener Rankings:
```bash
curl http://localhost:8000/api/ranking/1
```

**Esperado**: Array de puntuaciones del juego 1

---

## ❌ RESOLUCIÓN DE PROBLEMAS

### El servidor no inicia
```
Error: PHP not found
```
**Solución**: Asegúrate de que PHP esté en el PATH

### La página no carga
```
Error: Connection refused on localhost:8000
```
**Solución**: 
1. Verifica que el servidor esté corriendo
2. Recarga la página (Ctrl+R)
3. Intenta con http://localhost:8000 exactamente

### Los juegos no ahorran puntuación
**Posibles causas**:
- El token no es válido
- La BD no está actualizada
- El navegador no permite localStorage

**Solución**: 
1. Abre la consola del navegador (F12)
2. Verifica si hay errores
3. Intenta con otro navegador

### No veo el ranking
**Solución**:
1. Verifica que hayas jugado al menos una partida
2. Recarga la página
3. Abre la consola para ver errores

---

## ✅ CHECKLIST DE VERIFICACIÓN

- [ ] Servidor inicia sin errores
- [ ] Página de login carga correctamente
- [ ] Puedo hacer login con carlos@gamecenter.com
- [ ] Panel de control muestra 4 juegos
- [ ] Clicker Game funciona (10 segundos)
- [ ] Simon Says funciona (15 segundos)
- [ ] Flappy Bird funciona (15 segundos)
- [ ] Typing Game funciona (15 segundos)
- [ ] Las puntuaciones se guardan
- [ ] El ranking muestra usuarios
- [ ] Puedo filtrar ranking por juego
- [ ] Puedo cerrar sesión
- [ ] Los usuarios de prueba funcionan
- [ ] Puedo registrar un nuevo usuario
- [ ] La API retorna datos correctamente

---

## 📝 USUARIOS DE PRUEBA DISPONIBLES

```
Email: carlos@gamecenter.com    | Password: password123
Email: maria@gamecenter.com     | Password: password123
Email: juan@gamecenter.com      | Password: password123
Email: andrea@gamecenter.com    | Password: password123
Email: luis@gamecenter.com      | Password: password123
Email: sofia@gamecenter.com     | Password: password123
Email: pedro@gamecenter.com     | Password: password123
Email: laura@gamecenter.com     | Password: password123
```

---

## 🎉 RESULTADO ESPERADO

Si todos los pasos funcionan correctamente:
- ✅ La aplicación está 100% funcional
- ✅ Todos los requisitos están cumplidos
- ✅ Los 4 juegos funcionan correctamente
- ✅ El sistema de puntuaciones está operativo
- ✅ El ranking se actualiza en tiempo real

**¡Felicidades! El proyecto está completamente implementado.** 🎮

