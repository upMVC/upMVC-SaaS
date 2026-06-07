<?php

/**
 * Post-create-project setup script.
 * Runs automatically after: composer create-project bitshost/upmvc-saas my-app
 */

$root = dirname(__DIR__);

$green  = "\033[32m";
$yellow = "\033[33m";
$cyan   = "\033[36m";
$bold   = "\033[1m";
$reset  = "\033[0m";

echo "\n";
echo "  {$bold}upMVC SaaS — Project Setup{$reset}\n";
echo "  " . str_repeat('─', 40) . "\n\n";

// ── 1. Copy .env ─────────────────────────────────────────────────────────────

$envExample = $root . '/src/Etc/.env.example';
$envFile    = $root . '/src/Etc/.env';

if (file_exists($envFile)) {
    echo "  {$yellow}[skip]{$reset} .env already exists — not overwritten\n";
} elseif (!file_exists($envExample)) {
    echo "  {$yellow}[warn]{$reset} src/Etc/.env.example not found — copy it manually\n";
} else {
    $content = file_get_contents($envExample);

    // Auto-generate secure random values so the app works on first boot
    $jwtSecret = bin2hex(random_bytes(32));
    $appKey    = bin2hex(random_bytes(24));

    $content = str_replace(
        'your-random-secret-minimum-32-characters-change-this',
        $jwtSecret,
        $content
    );
    $content = str_replace(
        'your_app_key_here',
        $appKey,
        $content
    );

    file_put_contents($envFile, $content);
    echo "  {$green}[ok]{$reset}   src/Etc/.env created\n";
    echo "         JWT_SECRET and APP_KEY auto-generated\n";
}

// ── 2. Create required runtime directories ───────────────────────────────────

$dirs = [
    'storage'       => $root . '/storage',
    'storage/cache' => $root . '/storage/cache',
    'src/logs'      => $root . '/src/logs',
];

foreach ($dirs as $label => $path) {
    if (is_dir($path)) {
        continue;
    }
    if (mkdir($path, 0755, true)) {
        echo "  {$green}[ok]{$reset}   Created {$label}/\n";
    } else {
        echo "  {$yellow}[warn]{$reset} Could not create {$label}/ — create it manually\n";
    }
}

// ── 3. Next steps ─────────────────────────────────────────────────────────────

echo "\n";
echo "  {$bold}Next steps:{$reset}\n\n";
echo "  {$cyan}1.{$reset} Edit {$bold}src/Etc/.env{$reset}\n";
echo "     Set DB_HOST, DB_NAME, DB_USER, DB_PASS and SITE_PATH\n\n";
echo "  {$cyan}2.{$reset} Import the schema and demo data:\n";
echo "     {$bold}mysql -u root -p your_db < database/demo.sql{$reset}\n\n";
echo "  {$cyan}3.{$reset} Point your web server document root to {$bold}/public{$reset}\n";
echo "     or run the built-in server:\n";
echo "     {$bold}php -S localhost:8000 -t public{$reset}\n\n";
echo "  {$cyan}4.{$reset} Open {$bold}/auth{$reset} in your browser and sign in\n\n";
echo "  " . str_repeat('─', 40) . "\n";
echo "  Docs: https://upmvc.com  |  Issues: GitHub\n";
echo "\n";
