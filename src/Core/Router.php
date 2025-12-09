<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    protected array $routes = [];
    protected array $params = [];

    /**
     * Añade una ruta a la tabla de enrutamiento.
     *
     * @param string $route La URL de la ruta.
     * @param array $params Parámetros (controlador, acción, etc.).
     */
    public function add(string $route, array $params = []): void
    {
        // Convierte la ruta a una expresión regular: escapa las barras diagonales
        $route = preg_replace('/\//', '\\/', $route);
        // Convierte variables como {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        // Convierte variables con expresiones regulares personalizadas como {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        // Añade delimitadores de inicio y fin, y hace la distinción de mayúsculas/minúsculas
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * Busca una coincidencia de la URL en las rutas y establece los parámetros si la encuentra.
     *
     * @param string $url La URL a buscar.
     * @return bool True si se encuentra una coincidencia, false en caso contrario.
     */
    public function match(string $url): bool
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                // Obtiene los nombres de los grupos de captura y los valores coincidentes
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * Despacha la ruta, creando el controlador y ejecutando la acción.
     *
     * @param string $url La URL de la solicitud.
     */
    public function dispatch(string $url): void
    {
        // Limpiar la query string de la URL para que no interfiera con las coincidencias
        $url = trim(parse_url($url, PHP_URL_PATH), '/');

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToPascalCase($controller);
            $controller = "App\\Controllers\\{$controller}";

            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);
                
                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                if (is_callable([$controller_object, $action])) {
                    $controller_object->$action();
                } else {
                    throw new \Exception("Método $action (en el controlador $controller) no encontrado");
                }
            } else {
                throw new \Exception("Controlador clase $controller no encontrado");
            }
        } else {
            throw new \Exception('No se encontró la ruta.', 404);
        }
    }

    /**
     * Convierte una cadena con guiones a PascalCase.
     * e.g. 'mi-controlador' -> 'MiControlador'
     */
    protected function convertToPascalCase(string $string): string
    {
        return str_replace('-', '', ucwords(strtolower($string), '-'));
    }

    /**
     * Convierte una cadena con guiones a camelCase.
     * e.g. 'mi-accion' -> 'miAccion'
     */
    protected function convertToCamelCase(string $string): string
    {
        return lcfirst($this->convertToPascalCase($string));
    }
}
