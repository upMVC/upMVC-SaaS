<?php

namespace App\Modules\TenantShop;

class View
{
    public function storefront(array $tenant): void
    {
        $baseUrl = BASE_URL;
        $name    = htmlspecialchars($tenant['name'], ENT_QUOTES, 'UTF-8');
        $slug    = htmlspecialchars($tenant['slug'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $name ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; color: #1e293b; }
        header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; }
        header h1 { font-size: 1.5rem; font-weight: 700; }
        header a { color: #6366f1; text-decoration: none; font-weight: 500; font-size: .9rem; }
        .main { max-width: 900px; margin: 48px auto; padding: 0 24px; }
        .placeholder { background: #fff; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 60px 24px; text-align: center; color: #94a3b8; }
        .placeholder h2 { font-size: 1.25rem; margin-bottom: 8px; color: #64748b; }
        .placeholder p { font-size: .9rem; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: .85rem; }
    </style>
</head>
<body>
    <header>
        <h1><?= $name ?></h1>
        <a href="<?= $baseUrl ?>/app/<?= $slug ?>">Tenant Login &rarr;</a>
    </header>
    <div class="main">
        <div class="placeholder">
            <h2>Storefront Skeleton</h2>
            <p>This is the public-facing shop for <strong><?= $name ?></strong>.</p>
            <p style="margin-top:16px">Add your product listings, categories, and checkout logic in<br>
               <code>TenantShop\Model</code> and <code>TenantShop\View</code>.</p>
        </div>
    </div>
</body>
</html>
<?php
    }

    public function notFound(string $slug): void
    {
        $slug = htmlspecialchars($slug, ENT_QUOTES, 'UTF-8');
        http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Shop Not Found</title></head>
<body style="font-family:sans-serif;text-align:center;padding:80px">
    <h1>404 — Shop not found</h1>
    <p>No active storefront exists for <strong><?= $slug ?></strong>.</p>
    <p><a href="<?= BASE_URL ?>">Go Home</a></p>
</body>
</html>
<?php
    }
}
