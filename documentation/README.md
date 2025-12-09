# Documentación del Proyecto: Sistema de Inventario o Rastro

**Versión:** 2.0
**Fecha:** 09 de Diciembre de 2025
**Autor:** Asistente Gemini (Arquitecto de Software)

---

## 1. Introducción

Este documento detalla la arquitectura, diseño y requisitos del "Sistema de Inventario o Rastro". El objetivo es construir una aplicación web modular y robusta utilizando PHP nativo y el patrón MVC. La versión actual incluye un sistema de inventario funcional, gestión de usuarios con diferentes roles, un portal público de catálogo, un sistema de comentarios anidados y generación de reportes de ventas en PDF.

---

## 2. Arquitectura del Sistema

-   **Patrón de Diseño:** Modelo-Vista-Controlador (MVC).
-   **Enfoque de Programación:** Orientada a Objetos (POO) con tipado estricto de PHP 8.
-   **Estructura de Archivos:** El sistema se organiza en carpetas dedicadas para `public` (front-controller), `src` (lógica de negocio), `views` (plantillas), `config` (conexión a BD), `database` (scripts SQL) y `libs` (dependencias de terceros como FPDF).

---

## 3. Diagramas UML

### 3.1. Diagrama de Casos de Uso

El siguiente diagrama ilustra las interacciones de los actores con las funcionalidades principales del sistema.

```mermaid
graph TD
    subgraph "Actores"
        Admin((Administrador))
        Cliente((Cliente Registrado))
        Visitante((Visitante))
    end

    subgraph "Casos de Uso del Sistema"
        UC1[Ver Catálogo de Partes]
        UC2[Buscar Partes]
        UC3[Ver Detalle de Parte]
        UC4[Registrar Cuenta]
        UC5[Publicar Comentario]
        UC6[Responder a Comentario]
        UC7[Gestionar Inventario]
        UC8[Gestionar Usuarios y Roles]
        UC9[Registrar Venta y Generar Factura]
        UC10[Generar Reporte de Ventas]
        UC11[Eliminar Comentario]
    end

    %% Relaciones
    Visitante -- "Consulta" --> UC1
    Visitante -- "Consulta" --> UC2
    Visitante -- "Consulta" --> UC3
    Visitante -- "Se registra para ser" --> UC4

    Cliente -- "Hereda de" --> Visitante
    Cliente -- "Participa con" --> UC5
    Cliente -- "Interactúa con" --> UC6

    Admin -- "Hereda de" --> Cliente
    Admin -- "Gestiona" --> UC7
    Admin -- "Gestiona" --> UC8
    Admin -- "Ejecuta" --> UC9
    Admin -- "Supervisa con" --> UC10
    Admin -- "Modera con" --> UC11
```

**Descripción de Actores:**
-   **Visitante:** Usuario anónimo. Puede navegar el catálogo y registrarse.
-   **Cliente Registrado:** Usuario con cuenta. Puede comentar y responder.
-   **Administrador:** Usuario con privilegios totales, incluyendo gestión de inventario, usuarios y ventas.

### 3.2. Diagrama de Clases

Este diagrama detalla la estructura estática del sistema en su estado actual.

```mermaid
classDiagram
    direction BT

    class FPDF {
        <<Library>>
        +AddPage()
        +SetFont()
        +Cell()
        +Output()
    }

    class InvoiceGenerator {
        +generate(saleData: array) void
    }
    FPDF <|-- InvoiceGenerator

    class Database {
        <<Singleton>>
        +static getInstance(): Database
        +getConnection(): PDO
    }

    class BaseController {
        #view(viewName: string, data: array) void
        #redirect(url: string) void
        #authorizeAdmin() void
    }

    class HomeController {
        +index() void
        +show() void
        +addComment() void
    }
    BaseController <|-- HomeController

    class UserController {
        +showLoginForm() void
        +login() void
        +logout() void
        +showRegistrationForm() void
        +register() void
    }
    BaseController <|-- UserController

    class SaleController {
        +showForm() void
        +process() void
    }
    BaseController <|-- SaleController

    class ReportController {
        +index() void
        +monthly() void
    }
    BaseController <|-- ReportController


    class User {
        -id: int
        -nombre_usuario: string
        -rol_id: int
        +findById(id: int): User
        +findByUsername(username: string): User
        +save(): bool
        +softDelete(): bool
    }

    class Role {
        -id: int
        -nombre: string
    }

    class Part {
        -id: int
        -nombre: string
        -cantidad_disponible: int
        +findById(id: int): Part
        +decrementStock(amount: int): bool
        +save(): bool
    }

    class Sale {
        -id: int
        -precio_venta: float
        +save(): bool
        +findSalesByMonth(year: int, month: int): array
    }

    class Comment {
        -id: int
        -parent_id: int
        -texto_comentario: string
        +save(): bool
        +delete(): bool
        +findAndThreadByPartId(partId: int): array
    }

    %% Relationships
    HomeController ..> Part : uses
    HomeController ..> Comment : uses
    SaleController ..> Part : uses
    SaleController ..> Sale : uses
    SaleController ..> InvoiceGenerator : uses
    ReportController ..> Sale : uses
    UserController ..> User : uses
    
    Comment "0..*" -- "1" Comment : is reply to
    User "1" -- "1" Role : has a
    Part "1" -- "0..*" Comment : has
    User "1" -- "0..*" Comment : writes
    Sale "1" -- "1" User : sold by
```

### 3.3. Diagrama de Secuencia: Registrar Venta

Este diagrama muestra el flujo actualizado para registrar una venta, que ahora incluye la generación de una factura en PDF.

```mermaid
sequenceDiagram
    actor Admin
    participant SaleController
    participant PartModel as "Part (Model)"
    participant SaleModel as "Sale (Model)"
    participant InvoiceGenerator
    participant Database

    Admin->>SaleController: process(part_id, precio_venta)
    activate SaleController

    SaleController->>PartModel: findById(part_id)
    activate PartModel
    PartModel->>Database: SELECT * FROM partes WHERE id = ?
    Database-->>PartModel: partData
    PartModel-->>SaleController: partObject
    deactivate PartModel

    SaleController->>SaleModel: new Sale()
    SaleController->>SaleModel: save()
    activate SaleModel
    SaleModel->>Database: INSERT INTO vendido_parte (...)
    Database-->>SaleModel: success, lastInsertId
    SaleModel-->>SaleController: true
    deactivate SaleModel

    SaleController->>PartModel: decrementStock(1)
    activate PartModel
    PartModel->>Database: UPDATE partes SET cantidad_disponible = ?
    Database-->>PartModel: success
    PartModel-->>SaleController: true
    deactivate PartModel

    SaleController->>InvoiceGenerator: new InvoiceGenerator()
    SaleController->>InvoiceGenerator: generate(saleData)
    activate InvoiceGenerator
    InvoiceGenerator-->>Admin: Descarga "Factura_123.pdf"
    deactivate InvoiceGenerator

    deactivate SaleController
```
