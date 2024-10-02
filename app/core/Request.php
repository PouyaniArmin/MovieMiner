<?php

namespace App\Core;

use App\Interfaces\RequestInterface;
use Dotenv\Parser\Value;

use function PHPUnit\Framework\throwException;

class Request implements RequestInterface
{
    private $input;
    /**
     * Get the requested path from the URI.
     *
     * @return string The path of the request.
     */
    public function path(): string
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = filter_var($url,FILTER_SANITIZE_SPECIAL_CHARS);
        $posstion = strpos($url, "?");
        if ($posstion !== false) {
            $url = substr($url, 0, $posstion);
        }
        return !empty($url) ? $url : '/';
    }
    /**
     * Get the request method (GET, POST, etc.).
     *
     * @return string The request method in lowercase.
     */
    public function httpMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    /**
     * Determine if the request method is (GET,POST,....).
     *
     * @return bool True if the method is (GET,POST,....), false otherwise.
     */
    public function isGet(): bool
    {
        return $this->isMethod('get');
    }
    public function isPost(): bool
    {
        return $this->isMethod('post');
    }
    public function isPut(): bool
    {
        return $this->isMethod('put');
    }
    public function isPatch(): bool
    {
        return $this->isMethod('patch');
    }
    public function isDelete(): bool
    {
        return $this->isMethod('delete');
    }
    public function isHead(): bool
    {
        return $this->isMethod('head');
    }
    public function isOptions(): bool
    {
        return $this->isMethod('options');
    }
    /**
     * Check if the request method matches the given method.
     *
     * @param string $method The HTTP method to check against.
     * @return bool True if the request method matches, false otherwise.
     */
    public function isMethod(string $method): bool
    {
        return $this->httpMethod() === strtolower($method);
    }
    /**
     * Set the input for the request.
     *
     * @param mixed $input The input data to set.
     * @return void
     */
    public function setInput($input): void
    {
        $this->input = $input;
    }
    /**
     * Retrieve the body of the request as an associative array.
     *
     * @return array The request body data.
     */
    public function body(): array
    {
        $data = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }
        // PUT or PATCH Method
        if ($this->isPut() || $this->isPatch()) {
            parse_str($this->input, $data);
            array_walk($data, function (&$value) {
                $value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            });
        }
        if ($this->isHead() || $this->isOptions()) {
            return $data;
        }

        return $data;
    }
}
