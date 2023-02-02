<?php

namespace App;

class Request
{
    public readonly string $method;
    public readonly string $uri;
    private array $parameters = [];
    private array $data = [];
    private array $headers = [];
    private $user;

    public function __construct()
    {
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->uri = isset($_SERVER['PATH_INFO'] )? $_SERVER['PATH_INFO'] : '/';
        $this->headers = apache_request_headers();
        $this->loadData();
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function param(string $name)
    {
        return $this->parameters[$name] ?? null;
    }

    public function getData()
    {
        return $this->data;
    }

    private function loadData()
    {
        $jsonData = json_decode(file_get_contents("php://input"), 1) ?? [];
        $this->data = array_merge($_REQUEST, $jsonData);
    }

    public function getBearerToken()
    {
        return isset($this->headers['Authorization']) ? str_replace('Bearer ', '', $this->headers['Authorization']) : null;
    }

    public function getUser()
    {
        return $this->user;
    }
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
}
