<?php

namespace App\Modules\PlatformAdmin;

class Controller
{
    public function display(): void
    {
        if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
            header('Location: ' . BASE_URL . '/auth');
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'platform_admin') {
            http_response_code(403);
            echo '<h1>403 — Platform admin access required.</h1>';
            exit;
        }

        (new View())->render();
    }
}
