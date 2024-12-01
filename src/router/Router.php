<?php
namespace Router;

class Router {
    private $routes = [];
    private $basePath;

    public function __construct($basePath = '') {
        $this->basePath = $basePath;
    }

    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->basePath . $path,
            'handler' => $handler
        ];
    }

    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchRoute($route['path'], $path)) {
                $params = $this->getRouteParams($route['path'], $path);
                return call_user_func_array($route['handler'], $params);
            }
        }

        header('HTTP/1.0 404 Not Found');
        echo json_encode(['success' => false, 'message' => 'Ruta no encontrada']);
        exit;
    }

    private function matchRoute($routePath, $requestPath) {
        $routeRegex = $this->createRouteRegex($routePath);
        return preg_match($routeRegex, $requestPath);
    }

    private function getRouteParams($routePath, $requestPath) {
        $params = [];
        $routeRegex = $this->createRouteRegex($routePath);
        
        if (preg_match($routeRegex, $requestPath, $matches)) {
            array_shift($matches); 
            $params = $matches;
        }
        
        return $params;
    }

    private function createRouteRegex($path) {
        return '#^' . preg_replace('/\{(\w+)\}/', '([^/]+)', $path) . '$#';
    }
}