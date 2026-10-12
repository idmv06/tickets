# 🎫 Sistema de Tickets

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.3-7952B3?logo=bootstrap&logoColor=white)
![Database](https://img.shields.io/badge/Database-MySQL%2FMariaDB-blue)


## 🌟 Características

- Contraseñas protegidas mediante hashing con bcrypt

- Protección contra SQL Injection con consultas preparadas 

- Protección contra XSS mediante escape de salida

- Validación estricta de archivos por tipo MIME y tamaño.

- Un sistema propio, manteniendo estética Glassmorphism

- Roles diferenciados (profesores, administrativos y soporte).

- Creación de tickets con adjuntos.

- Sistema de comentarios públicos e internos (notas privadas para el equipo).

- Asignación y reasignación de agentes de soporte.

- Un panel de administración con búsqueda por texto, filtro por estado y filtro por agente.

- Vista de impresión optimizada para exportar a PDF.



## ℹ️ Descripción

Sistema web de gestión de tickets desarrollado como proyecto de práctica profesional. Su objetivo es facilitar la creación, organización y seguimiento de solicitudes de soporte técnico mediante una plataforma centralizada.

El proyecto integra herramientas de desarrollo web, gestión de bases de datos, control de acceso y medidas de seguridad para ofrecer una experiencia de uso organizada.


## ✍️ Autor

Isaac Martínez

Este es mi primer proyecto de desarrollo de una plataforma web, realizado como parte de mi práctica profesional. Representa una oportunidad para aplicar mis conocimientos de programación, bases de datos y desarrollo web en un proyecto práctico.


## 🚀 Uso

La plataforma permite gestionar tickets, es decir, solicitudes de asistencia técnica relacionadas con problemas específicos.

Según los permisos de cada rol, las personas usuarias pueden crear solicitudes, adjuntar archivos, consultar el estado de los tickets y participar en el seguimiento de su resolución.

El equipo de soporte puede gestionar las solicitudes, asignar responsables y utilizar comentarios internos para coordinar la atención.


## ⬇️ Instalación

#Requisitos previos

- PHP 8.x, con las extensiones necesarias para la aplicación.

- MySQL o MariaDB.

- Un servidor web compatible con PHP, como Apache.

- El archivo SQL incluido en el repositorio.

#Pasos de instalación

- Clonar el repositorio o descargar sus archivos.

- Configurar la base de datos: crear una base de datos en MySQL o MariaDB e importar el archivo .sql incluido en el repositorio.

- Configurar la conexión: ajustar el archivo de conexión a la base de datos con las credenciales correspondientes. Si el proyecto utiliza variables de entorno, configurarlas según el entorno de ejecución.

- Iniciar el servidor: ejecutar la aplicación mediante un servidor compatible con PHP.

- Acceder al sistema: abrir la dirección local o la URL del despliegue y navegar hasta la página de inicio de la aplicación.

Nota: Los pasos exactos de configuración pueden variar según se ejecute el proyecto en un entorno local o en un servicio de alojamiento en la nube.

---

## 📝 Créditos y transparencia

- Desarrollo: proyecto realizado por Isaac Martínez como parte de mi práctica profesional.
- Inteligencia artificial: se utilizaron herramientas de IA como apoyo para resolver dudas, comprender conceptos, depurar código y mejorar la documentación. La IA fue una herramienta de asistencia durante el proceso de desarrollo, no un sustituto del aprendizaje ni de mi participación en el proyecto. **Hecho con apoyo de IA, pero no mediante *vibe coding* sin comprensión del código.**
- Plantilla del README: documentación estructurada tomando como referencia la guía [*How to write a good README*, de Bane Sullivan](https://github.com/banesullivan/README), adaptada a las necesidades de este proyecto.
- Mejora continua: este proyecto forma parte de mi aprendizaje en desarrollo web y puede recibir mejoras, correcciones y nuevas funcionalidades.
