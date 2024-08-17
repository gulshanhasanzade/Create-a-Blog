<?php

namespace PHPMailer\PHPMailer;

class Exception extends \Exception
{
    public function errorMessage(): string
    {
        return '<strong>' . htmlspecialchars($this->getMessage(), ENT_COMPAT | ENT_HTML401) . "</strong><br />\n";
    }
}