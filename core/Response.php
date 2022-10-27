<?php

namespace App\core;

class Response
{
    /**
     * @param int $code
     * @return void
     */
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    /**
     * @param string $uri
     * @return self
     */
    public function redirect(string $uri): Response
    {
        header("Location: $uri");

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function with(string $key, string $value): void
    {
        Application::$app->session->setFlashMessage($key, $value);
    }

    /**
     * @param array $value
     * @return void
     */
    public function withValidationErrors(array $value): void
    {
        Application::$app->session->setValidationErrors($value);
    }
}