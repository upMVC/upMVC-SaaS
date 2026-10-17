<?php

/**
 * Post-create-project setup script.
 * Runs automatically after creating the upMVC-SaaS starter app.
 */

$root = dirname(__DIR__);

$green  = "\033[32m";
$yellow = "\033[33m";
$cyan   = "\033[36m";
$bold   = "\033[1m";
$reset  = "\033[0m";

echo "\n";
echo "  {$bold}upMVC SaaS Starter — Project Setup{$reset}\n";
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
    'storage/logs'  => $root . '/storage/logs',
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
echo "  {$cyan}2.{$reset} Install packages (Packagist):\n";
echo "     {$bold}composer install{$reset}\n\n";
echo "  {$cyan}3.{$reset} Migrate (kernel → saas-pack 001/002):\n";
echo "     {$bold}php vendor/bitshost/upmvc/src/Tools/migrate.php{$reset}\n\n";
echo "  {$cyan}4.{$reset} Optional pack seed:\n";
echo "     {$bold}mysql -u root -p your_db < vendor/bitshost/upmvc-saas-pack/database/seed.sql{$reset}\n\n";
echo "  {$cyan}5.{$reset} Doctor:\n";
echo "     {$bold}php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php doctor{$reset}\n\n";
echo "  {$cyan}6.{$reset} Point the document root to {$bold}/public{$reset}\n";
echo "     or: {$bold}php -S localhost:8000 -t public{$reset}\n\n";
echo "  {$cyan}7.{$reset} Open {$bold}/auth{$reset} and sign in\n\n";
echo "  Guide: https://github.com/upMVC/upMVC/blob/main/docs/BUILD_SAAS_APP.md\n";
echo "  " . str_repeat('─', 40) . "\n";
echo "\n";
