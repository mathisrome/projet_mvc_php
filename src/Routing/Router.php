<?php

namespace App\Routing;

use ReflectionMethod;
use Twig\Environment;

class Router
{
  public function __construct(
    private array $services
  ) {
  }

  private array $routes = [];

  public function addRoute(
    string $name,
    string $url,
    string $httpMethod,
    string $controllerClass,
    string $controllerMethod
  ) {
    $this->routes[] = [
      'name' => $name,
      'url' => $url,
      'http_method' => $httpMethod,
      'controller' => $controllerClass,
      'method' => $controllerMethod
    ];
  }

  public function getRoute(string $uri, string $httpMethod): ?array
  {
    foreach ($this->routes as $route) {
      if ($route['url'] === $uri && $route['http_method'] === $httpMethod) {
        return $route;
      }
    }

    return null;
  }

  /**
   * @param string $requestUri
   * @param string $httpMethod
   * @return void
   * @throws RouteNotFoundException
   */
  public function execute(string $requestUri, string $httpMethod)
  {
    $route = $this->getRoute($requestUri, $httpMethod);

    if ($route === null) {
      throw new RouteNotFoundException($requestUri, $httpMethod);
    }

    $controllerClass = $route['controller'];
    $method = $route['method'];

    $classInfos = new \ReflectionClass($controllerClass);
    $constructorInfos = $classInfos->getConstructor();
    $contructorParams = $constructorInfos->getParameters();

    $params = [];
    foreach ($contructorParams as $param) {
      $paramType = $param->getType();
      $typeName = $paramType->getName();

      if (array_key_exists($typeName, $this->services)) {
        $params[] = $this->services[$typeName];
      }
    }

    $controllerInstance = new $controllerClass(...$params);

    $methodInfos = new ReflectionMethod($controllerClass . '::' . $method);
    $methodParams = $methodInfos->getParameters();

    $params = [];
    foreach ($methodParams as $methodParam) {
      $paramType = $methodParam->getType();
      $typeName = $paramType->getName();

      if (array_key_exists($typeName, $this->services)) {
        $params[] = $this->services[$typeName];
      }
    }

    echo $controllerInstance->$method(...$params);
  }
}
