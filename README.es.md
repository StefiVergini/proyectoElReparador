**Leer en otros idiomas:** [English](README.md)

# 🛠️ El Reparador - Sistema de Gestión

## 🎬 Demo Rápida
![Ingreso - Crear Presupuesto - Respuesta Autómatica en Sistema - Token - Evento de Calendario Automático](screenshots/FromLogin-ToCalendar.gif)

## 💼 Sobre el Proyecto

Este proyecto fue desarrollado como parte de mi experiencia como Desarrolladora Full-Stack, con el objetivo de diseñar un sistema de gestión completo para un negocio real de reparaciones.

Refleja mi capacidad para construir aplicaciones modulares utilizando PHP y MySQL, aplicar principios de programación orientada a objetos y desarrollar soluciones que abordan necesidades concretas del negocio, como la gestión de empleados, seguimiento de pedidos, manejo de documentación y planificación de tareas.

Durante el desarrollo, me enfoqué en escribir código claro y mantenible, organizando el sistema por dominios de negocio para facilitar su comprensión y escalabilidad.

---

## 📌 Descripción General

**El Reparador** es un sistema web modular diseñado para gestionar las operaciones diarias de un negocio de reparaciones.

La aplicación está organizada en módulos funcionales, lo que permite una clara separación de responsabilidades y facilita futuras ampliaciones.

---

## 💡 Contexto del Negocio

### Problema

Los negocios de reparación suelen enfrentar dificultades para:

* Realizar el seguimiento del estado de las reparaciones
* Gestionar la comunicación con los clientes
* Manejar aprobaciones y organizar tareas de forma eficiente

### Solución

Este sistema centraliza las operaciones mediante:

* Automatización de la comunicación con clientes a través de correos electrónicos
* Gestión del ciclo completo de reparación (desde ingreso hasta finalización)
* Integración de la planificación según disponibilidad de técnicos

---
## 🎬 Demo Flujo de Reparación Completa
Flujo de trabajo de reparación integral con aprobaciones automatizadas por correo electrónico y notificaciones.
![End-to-end repair workflow](screenshots/WorkFlowRepair-EndToEnd.gif)

---

## 📸 Capturas de Pantalla

### Login

![Login](screenshots/login.png)
Autenticación segura con acceso restringido.

---

### Panel Principal

![Dashboard](screenshots/dashboard.png)
Vista centralizada del sistema.

---

### Gestión de Empleados

![Employees](screenshots/employees.png)
Administración de empleados, categorías e historial.

---

### Formularios y Gestión de Datos

![Form](screenshots/form.png)
Formularios dinámicos para crear y actualizar registros.

---

### Calendario y Eventos

![Calendar](screenshots/calendar.png)
Seguimiento de eventos con alertas visuales según vencimientos.

---

## 🚀 Funcionalidades

* Arquitectura modular organizada por dominios de negocio
* Gestión de empleados, proveedores y pedidos
* Gestión de productos / electrodomésticos
* Calendario interactivo con alertas de eventos
* Exportación de datos en PDF / Excel
* Sistema de notificaciones
* Integración con envío de correos electrónicos
* Autenticación y control de accesos

---

## 🛠️ Tecnologías Utilizadas

**Frontend**

* HTML5
* CSS3
* JavaScript

**Backend**

* PHP (Programación Orientada a Objetos)
* PDO (acceso seguro a base de datos)

**Base de Datos**

* MySQL

---

## 🧩 Estructura del Proyecto

```bash
/proyectoElReparador

├── calendario/         
├── clientes/           
├── electrodomesticos/  
├── empleados/          
├── fpdf/               
├── login/              
├── mail/               
├── notificaciones/     
├── pedidos/            
├── PHPMailer/          
├── proveedores/        
├── reportes/           
├── static/             
├── stock/              
│
├── conexionPDO.php     
├── cambiarPass.php     
├── cerrar_sesion.php   
├── perfil.php          
├── perfil_logica.php   
│
├── el_reparador_db.sql 
├── README.es.md
├── README.md
```

---

## ⚙️ Instalación y Configuración

### 🔧 Requisitos

* XAMPP
* Apache y MySQL en ejecución

---

### 📥 Instalación

1. Clonar o descargar el repositorio:

```bash
git clone https://github.com/StefiVergini/proyectoElReparador.git
```

2. Ubicar el proyecto en el servidor local:

Ejemplo:

```
C:/xampp/htdocs/php/proyectoElReparador
```

3. Crear una base de datos en phpMyAdmin:

* Acceder a: http://localhost/phpmyadmin/
* Crear base de datos:

```
el_reparador_db
```

4. Importar la base de datos:

* Utilizar el archivo:

```
el_reparador_db.sql
```

---

### 🔐 Variables de Entorno

Este proyecto utiliza variables de entorno para manejar datos sensibles (por ejemplo, credenciales de email).

Creá un archivo `.env` en la raíz del proyecto con lo siguiente:

MAIL_USERNAME=tu_email@gmail.com  
MAIL_PASSWORD=tu_contraseña_de_aplicacion  

> Asegurate de que este archivo no se suba a GitHub.

---

### ▶️ Ejecución del Proyecto

1. Iniciar Apache y MySQL desde XAMPP

2. Abrir en el navegador:

```
http://localhost/php/proyectoElReparador/login/index.php
```

o

```
http://localhost/php/proyectoElReparador/login/login.php
```

---

### 🔐 Credenciales de Prueba

Podés ingresar con:

* **Email:** [ernesabato@gmail.com](mailto:ernesabato@gmail.com)
* **Contraseña:** 123hola

> Credenciales de prueba con fines demostrativos

---

### 🧪 Prueba de Envío de Emails (Opcional)

Para probar la funcionalidad de correos:

* Crear un cliente con un email válido
* Realizar acciones como aprobación o rechazo de presupuestos
* El sistema enviará automáticamente correos según el flujo

---

### ⚠️ Notas

* Asegurarse de que Apache y MySQL estén en ejecución
* Verificar que la base de datos esté correctamente importada
* El proyecto ya está configurado para usar la base de datos `el_reparador_db`
* No exponer credenciales reales en el repositorio
* Utilizar variables de entorno para la configuración sensible

---

## 🔐 Autenticación

* Sistema de login implementado
* Acceso basado en roles (Gerente / Técnico / Atención al cliente)
* Secciones protegidas
* A los nuevos usuarios se les asigna una contraseña temporal al crear su cuenta.
* Para garantizar la seguridad de la cuenta, los usuarios deben actualizar su contraseña al iniciar sesión por primera vez.

---

## 📅 Sistema de Calendario

* Calendario interactivo para planificación de reparaciones
* Eventos generados dinámicamente según acciones del sistema
* Al aprobarse un presupuesto, se asigna automáticamente al calendario del técnico
* Se establecen fechas de inicio y finalización estimada
* Mejora la organización y planificación del trabajo
* Indicadores visuales:

  * 🔴 Menos de 72 horas restantes
  * 🟡 Entre 72 y 120 horas
  * 🟢 Más de 120 horas

---

## 🔔 Notificaciones y Emails

* Envío automático de correos según eventos del sistema
* Los presupuestos incluyen botones de acción (aprobar / rechazar)
* Cada acción utiliza un token de un solo uso para evitar duplicaciones
* Al responder, se envía automáticamente un correo de confirmación
* Se envían correos adicionales durante el proceso de reparación
* El sistema genera notificaciones internas con las decisiones del cliente
* Incluye referencia al ID del electrodoméstico para su gestión
* Comunicación basada en eventos a lo largo de todo el proceso

---

## 💡 Arquitectura y Decisiones de Diseño

* Estructura modular organizada por dominios de negocio
* Uso de clases PHP en la mayoría de los módulos para encapsular lógica
* Uso de PDO para interacción segura con la base de datos
* Separación parcial entre lógica y presentación

---

## 👩‍💻 Autor

**Stefanía Vergini**
Desarrolladora Full-Stack

---

## 📬 Contacto

[stefanialvergini@gmail.com](mailto:stefanialvergini@gmail.com)
Abierta a nuevas oportunidades y colaboraciones
