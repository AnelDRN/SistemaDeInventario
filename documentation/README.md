# Documentación del Proyecto: Sistema de Inventario o Rastro

**Versión:** 1.0
**Fecha:** 01 de Diciembre de 2025
**Autor:** Asistente Gemini (Arquitecto de Software)

---

## 1. Introducción

Este documento detalla la arquitectura, diseño y requisitos del "Sistema de Inventario o Rastro". El objetivo es construir una aplicación web modular, robusta y escalable utilizando PHP nativo, siguiendo el patrón MVC y preparándola para una futura migración a un entorno Docker.

---

## 2. Arquitectura del Sistema

-   **Patrón de Diseño:** Modelo-Vista-Controlador (MVC).
-   **Enfoque de Programación:** Orientada a Objetos (POO).
-   **Estructura de Archivos:** El sistema se organiza en carpetas dedicadas para `public` (front-controller, assets), `src` (lógica de negocio, controladores, modelos), `views` (plantillas), `config` (configuración) y `database` (scripts SQL).

---

## 3. Diagramas UML

Esta sección contiene los diagramas UML que modelan el sistema desde diferentes perspectivas.

### 3.1. Diagrama de Casos de Uso

El siguiente diagrama ilustra las interacciones de los usuarios (actores) con el sistema y las funcionalidades principales.

```mermaid
graph TD
    subgraph "Actores"
        Admin((Administrador))
        Visitante((Visitante))
    end

    subgraph "Casos de Uso del Sistema"
        UC1[Ver Catálogo de Partes]
        UC2[Buscar Partes]
        UC3[Ver Detalle de Parte]
        UC4[Escribir Comentario]
        UC5[Gestionar Usuarios]
        UC6[Gestionar Inventario de Partes]
        UC7[Moderar Comentarios]
        UC8[Ver Reporte de Ventas]
        UC9[Realizar Venta]
    end

    %% Conexiones de Visitante
    Visitante -- "Consulta" --> UC1
    Visitante -- "Consulta" --> UC2
    Visitante -- "Consulta" --> UC3
    Visitante -- "Participa" --> UC4

    %% Conexiones de Administrador
    Admin -- "Gestiona" --> UC5
    Admin -- "Gestiona" --> UC6
    Admin -- "Gestiona" --> UC7
    Admin -- "Supervisa" --> UC8
    Admin -- "Ejecuta" --> UC9

    %% Admin también puede hacer lo que hace el visitante
    Admin -- "Hereda capacidades de" --> Visitante
```

**Descripción de Actores:**
-   **Visitante:** Cualquier usuario público que navega por el sitio. No requiere autenticación para las funciones básicas de consulta.
-   **Administrador:** Un usuario autenticado con privilegios elevados para gestionar todos los aspectos del sistema.

---

*(Las siguientes secciones se completarán progresivamente)*

### 3.2. Diagrama de Clases

Este diagrama detalla la estructura estática del sistema. Define las clases, sus atributos, métodos y las relaciones entre ellas, sirviendo como un plano para la implementación del código.

```mermaid
classDiagram
    direction BT

    class IErrorHandler {
        <<Interface>>
        +logError(message: string, context: array) void
    }

    class FileLogger {
        +logError(message: string, context: array) void
    }
    IErrorHandler <|.. FileLogger : implements

    class Database {
        <<Singleton>>
        -static $instance: Database
        -pdo: PDO
        -Database()
        +static getInstance(): Database
        +getConnection(): PDO
    }

    class Sanitizer {
        +sanitizeString(input: string): string
        +validateEmail(email: string): bool
    }

    class ImageHelper {
        +createThumbnail(sourcePath: string, destPath: string, width: int): bool
        +uploadImage(file: array, destPath: string): string
    }

    class BaseController {
        #view(viewName: string, data: array) void
    }

    class UserController {
        -userModel: User
        +login(request: array) void
        +listUsers() void
        +updateUser(id: int, data: array) void
        +deactivateUser(id: int) void
    }
    BaseController <|-- UserController

    class PartController {
        -partModel: Part
        +listAll() void
        +show(id: int) void
        +create(data: array) void
        +update(id: int, data: array) void
    }
    BaseController <|-- PartController

    class SaleController {
        -saleModel: Sale
        -partModel: Part
        +createSale(partId: int, price: float) bool
    }
    BaseController <|-- SaleController


    class User {
        -id: int
        -nombre: string
        -email: string
        -password_hash: string
        -activo: bool
        -roleId: int
        +findById(id: int): User
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
        -descripcion: string
        -marca_auto: string
        -modelo_auto: string
        -año_auto: int
        -imagenUrl: string
        -thumbnailUrl: string
        -sectionId: int
        +findById(id: int): Part
        +findAll(): array
        +save(): bool
    }

    class Section {
        -id: int
        -nombre: string
        -descripcion: string
    }

    class Sale {
        -id: int
        -partId: int
        -usuarioVendedorId: int
        -precioVenta: float
        -fechaVenta: datetime
        +createRecord(data: array): bool
    }

    class Comment {
        -id: int
        -partId: int
        -usuarioId: int
        -texto: string
        -estado: string
        +findByPart(partId: int): array
        +save(): bool
        +approve(): bool
    }

    %% Relationships
    UserController ..> User : uses
    PartController ..> Part : uses
    SaleController ..> Sale : uses
    SaleController ..> Part : uses

    User "1" -- "1" Role : has a
    Part "1" -- "1" Section : is in
    Part "1" -- "0..*" Comment : has
    Sale "1" -- "1" User : sold by
    Sale "1" -- "1" Part : is for

    %% Helpers and Core
    UserController ..> Sanitizer : uses
    PartController ..> Sanitizer : uses
    PartController ..> ImageHelper : uses
    
    User ..> Database : uses
    Part ..> Database : uses
    Sale ..> Database : uses
    Comment ..> Database : uses
```

### 3.3. Diagrama de Secuencia: Realizar Venta

Este diagrama muestra la secuencia de interacciones entre los objetos del sistema durante el proceso de venta de una parte. Este es un flujo de trabajo crítico que demuestra el patrón MVC en acción.

```mermaid
sequenceDiagram
    actor Administrador
    participant SaleController
    participant PartModel as "Part (Model)"
    participant SaleModel as "Sale (Model)"
    participant Database

    Administrador->>SaleController: createSale(partId, price)
    activate SaleController

    SaleController->>PartModel: findById(partId)
    activate PartModel
    PartModel->>Database: SELECT * FROM partes WHERE id = ?
    Database-->>PartModel: partData
    PartModel-->>SaleController: partObject
    deactivate PartModel

    SaleController->>SaleModel: createRecord(partData, price, adminId)
    activate SaleModel
    SaleModel->>Database: INSERT INTO vendido_parte (...) VALUES (...)
    Database-->>SaleModel: success
    SaleModel-->>SaleController: true
    deactivate SaleModel

    SaleController->>PartModel: delete(partId)
    activate PartModel
    PartModel->>Database: DELETE FROM partes WHERE id = ?
    Database-->>PartModel: success
    PartModel-->>SaleController: true
    deactivate PartModel
    
    SaleController-->>Administrador: "Venta registrada con éxito"
    deactivate SaleController
```
