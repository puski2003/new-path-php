<?php

/**
 * Response — simple output helpers
 */
class Response
{

    /**
     * Redirect to a URL and stop execution.
     */
    public static function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Set an HTTP status code.
     */
    public static function status(int $code): void
    {
        http_response_code($code);
    }

    /**
     * Abort with an HTTP status and a plain message.
     */
    public static function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        echo htmlspecialchars($message);
        exit;
    }
}
