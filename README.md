# Veterinaria Patitas

Aplicación web para una clínica veterinaria: permite a los **clientes** gestionar mascotas y citas, y al **personal** (veterinarios y administración) operar la agenda, inventario, facturación y cobros. Está pensada como proyecto académico tipo cliente/servidor con PHP y MySQL.

## Qué hace la aplicación

- **Página pública** (`home`): presentación de la clínica y acceso a login y registro.
- **Clientes** (rol cliente): registro e inicio de sesión, panel con resumen, **mascotas**, **agenda de citas** (solicitar y editar citas pendientes), **servicios** públicos, **historial clínico** de sus animales, **facturas** y **pagos** asociados a sus citas.
- **Veterinarios**: panel de administración con citas del día, listado general, **confirmación de citas pendientes** asignadas a ellos, servicios, inventario, reportes, evaluaciones y **registro de pagos** por cita atendida (además del listado de todos los pagos).
- **Administrador**: lo mismo que el veterinario en operación diaria, más **gestión de usuarios** (altas de clientes y veterinarios) y visibilidad global de citas pendientes.

Las reglas de negocio relevantes (por ejemplo, quién puede ver qué factura o pago, o registrar un cobro solo sobre citas válidas) están aplicadas en los **modelos** y en la **API JSON**.

## Tecnología y arquitectura

| Capa | Descripción |
|------|-------------|
| **Vistas** | PHP en `app/views/pages/` y partials (`partials/`). Entrada única de páginas: `index.php?r=<ruta>` enrutada por `PageController`. |
| **API REST interna** | `api.php?route=<nombre>` despachada por `ApiRouter`; respuestas JSON; sesión PHP para autenticación. |
| **Lógica** | Controladores en `app/controllers/`, modelos en `app/models/`. |
| **Configuración** | `config/` (conexión, sesión, helpers de API). |
| **Frontend** | Bootstrap 5, estilos en `public/css/estilos.css`, scripts en `public/js/`. |
| **Base de datos** | Esquema y datos de ejemplo en `sql/` (por ejemplo `schema.sql`). |

## Estructura del repositorio (resumen)

```
├── api.php              # Front controller JSON
├── index.php            # Front controller de vistas
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
├── config/
├── public/
│   ├── css/
│   └── js/
└── sql/
```

## Cómo empezar

1. Servir el proyecto con un servidor con **PHP** y crear la base según `sql/schema.sql` (y seeds si los usas).
2. Configurar credenciales de MySQL en `config/database.php` (o el mecanismo que use tu entorno; por ejemplo contenedor Docker con la app montada en esta carpeta).
3. Abrir en el navegador la URL base del proyecto y usar `index.php?r=home` o la ruta por defecto que exponga tu servidor.

Para detalle de tablas, relaciones y convenciones de la API, revisa el código de los controladores enlazados en `ApiRouter` y el esquema en `sql/schema.sql`.
