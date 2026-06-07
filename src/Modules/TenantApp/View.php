<?php

namespace App\Modules\TenantApp;

class View
{
    // ── Admin shell (authenticated) ─────────────────────────────────────────

    public function renderAdmin(array $s): void
    {
        $base     = BASE_URL;
        $slug     = htmlspecialchars($s['slug']);
        $name     = htmlspecialchars($s['name']);
        $username = htmlspecialchars($s['username']);
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $name; ?> — Admin</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; display: flex; min-height: 100vh; }
        .sidebar { width: 240px; min-height: 100vh; background: #0f172a; color: #e2e8f0; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-brand { padding: 24px 20px 16px; border-bottom: 1px solid #1e293b; }
        .sidebar-brand h2 { font-size: 1.1rem; font-weight: 700; color: #f8fafc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .plan-badge { display: inline-block; margin-top: 6px; padding: 2px 8px; background: #1e3a5f; color: #93c5fd; border-radius: 10px; font-size: .73rem; font-weight: 600; }
        .sidebar-nav { padding: 12px 0; flex: 1; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 20px; color: #94a3b8; text-decoration: none; font-size: .9rem; border-left: 3px solid transparent; transition: background .15s, color .15s; }
        .sidebar-nav a:hover { background: #1e293b; color: #e2e8f0; }
        .sidebar-nav a.active { background: #1e293b; color: #38bdf8; border-left-color: #38bdf8; font-weight: 600; }
        .nav-divider { border-top: 1px solid #1e293b; margin: 8px 0; }
        .sidebar-footer { padding: 16px 20px; border-top: 1px solid #1e293b; font-size: .8rem; color: #475569; }
        .sidebar-footer a { color: #64748b; text-decoration: none; }
        .sidebar-footer a:hover { color: #e2e8f0; }
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 28px; height: 56px; display: flex; align-items: center; justify-content: space-between; }
        .topbar-title { font-size: 1rem; font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .status-badge { padding: 3px 10px; border-radius: 12px; font-size: .78rem; font-weight: 600; }
        .content { padding: 28px; flex: 1; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; }
        .card-label { font-size: .75rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; }
        .card-value { font-size: 1.6rem; font-weight: 700; color: #0f172a; margin-top: 6px; }
        .card-sub { font-size: .82rem; color: #64748b; margin-top: 4px; }
        .card-link { font-size: .82rem; color: #3b82f6; text-decoration: none; }
        .section { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 24px; }
        .section-header { padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-weight: 600; font-size: .95rem; }
        .tbl { width: 100%; border-collapse: collapse; font-size: .88rem; }
        .tbl th { padding: 10px 14px; text-align: left; background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-weight: 600; color: #374151; font-size: .8rem; text-transform: uppercase; }
        .tbl td { padding: 11px 14px; border-bottom: 1px solid #f1f5f9; }
        .tbl tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: .78rem; font-weight: 600; }
        .badge-owner { background: #dbeafe; color: #1e40af; }
        .badge-user { background: #f1f5f9; color: #374151; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .features { display: flex; gap: 16px; flex-wrap: wrap; padding: 20px; }
        .feat-item { display: flex; align-items: center; gap: 6px; font-size: .88rem; }
        .feat-on { color: #10b981; } .feat-off { color: #cbd5e1; }
        .alert-suspended { padding: 14px 18px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 8px; color: #991b1b; margin-bottom: 20px; }
        #content-area p.loading { color: #94a3b8; padding: 40px 0; text-align: center; }
        @media (max-width: 640px) { .sidebar { width: 200px; } .content { padding: 16px; } }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>🏢 <?php echo $name; ?></h2>
        <span class="plan-badge" id="plan-badge">…</span>
    </div>
    <nav class="sidebar-nav">
        <a href="<?php echo $base; ?>/app/<?php echo $slug; ?>/admin" id="nav-dashboard">📊 Dashboard</a>
        <a href="<?php echo $base; ?>/app/<?php echo $slug; ?>/admin/users" id="nav-users">👥 Users</a>
        <div class="nav-divider"></div>
        <a href="<?php echo $base; ?>/app/<?php echo $slug; ?>" style="font-size:.78rem;color:#475569;padding:6px 20px;border-left:none;">
            🌐 View Public Site ↗
        </a>
    </nav>
    <div class="sidebar-footer">
        Logged in as <strong><?php echo $username; ?></strong><br>
        <a href="<?php echo $base; ?>/logout">Sign out</a>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <span class="topbar-title" id="page-title">Loading…</span>
        <div class="topbar-right">
            <span style="font-size:.85rem;color:#64748b;"><?php echo $username; ?></span>
            <span class="status-badge" id="status-badge"></span>
        </div>
    </div>
    <div class="content" id="content-area">
        <p class="loading">Loading…</p>
    </div>
</div>

<script>
const BASE      = <?php echo json_encode($base); ?>;
const TOKEN     = <?php echo json_encode($s['token']); ?>;
const TENANT_ID = <?php echo (int) $s['tenant_id']; ?>;
const SLUG      = <?php echo json_encode($slug); ?>;
const PATH      = window.location.pathname;

const statusColors = {
    active:    { bg: '#dcfce7', fg: '#166534' },
    trial:     { bg: '#fef9c3', fg: '#92400e' },
    suspended: { bg: '#fee2e2', fg: '#991b1b' },
};

async function api(path) {
    const res = await fetch(BASE + path, {
        headers: { 'Authorization': 'Bearer ' + TOKEN, 'Content-Type': 'application/json' }
    });
    return res.json();
}

function isUsersPage() {
    return PATH.includes('/admin/users');
}

// ── nav active state ─────────────────────────────────────────────────────────
document.getElementById(isUsersPage() ? 'nav-users' : 'nav-dashboard').classList.add('active');

// ── render helpers ────────────────────────────────────────────────────────────

function renderDashboard(tenant, users) {
    document.getElementById('page-title').textContent = 'Dashboard';

    const features = tenant.plan_features ?? {};
    const limits   = tenant.plan_limits   ?? {};

    const featHtml = Object.keys(features).length ? `
        <div class="section">
            <div class="section-header">Plan Features</div>
            <div class="features">
                ${Object.entries(features).map(([k, v]) => `
                    <div class="feat-item">
                        <span class="${v ? 'feat-on' : 'feat-off'}">${v ? '✔' : '✖'}</span>
                        ${k.replace(/_/g,' ')}
                    </div>`).join('')}
            </div>
            ${Object.keys(limits).length ? `
            <div style="padding:0 20px 16px;font-size:.82rem;color:#64748b;display:flex;gap:20px;flex-wrap:wrap;">
                ${Object.entries(limits).map(([k,v]) => `<span>${k.replace(/_/g,' ')}: <strong style="color:#0f172a">${v == 0 ? '∞' : v}</strong></span>`).join('')}
            </div>` : ''}
        </div>` : '';

    const previewRows = users.slice(0, 5).map(u => `
        <tr>
            <td>${u.name}</td>
            <td style="font-family:monospace;font-size:.83rem">${u.username}</td>
            <td><span class="badge ${u.role === 'tenant_owner' ? 'badge-owner' : 'badge-user'}">${u.role === 'tenant_owner' ? 'Owner' : 'User'}</span></td>
            <td><span class="badge ${u.state ? 'badge-active' : 'badge-inactive'}">${u.state ? 'Active' : 'Inactive'}</span></td>
        </tr>`).join('');

    document.getElementById('content-area').innerHTML = `
        ${tenant.status === 'suspended' ? '<div class="alert-suspended">⚠️ Your account is <strong>suspended</strong>. Please contact support.</div>' : ''}
        <div class="cards">
            <div class="card">
                <div class="card-label">Organisation</div>
                <div class="card-value" style="font-size:1.15rem;margin-top:8px;">${tenant.name}</div>
                <div class="card-sub">slug: ${tenant.slug}</div>
            </div>
            <div class="card">
                <div class="card-label">Plan</div>
                <div class="card-value">${tenant.plan_name ?? '—'}</div>
                <div class="card-sub">$${parseFloat(tenant.plan_price ?? 0).toFixed(2)}/mo</div>
            </div>
            <div class="card">
                <div class="card-label">Users</div>
                <div class="card-value">${users.length}</div>
                <a class="card-link" href="${BASE}/app/${SLUG}/admin/users">Manage →</a>
            </div>
            <div class="card">
                <div class="card-label">Member since</div>
                <div class="card-value" style="font-size:1rem;margin-top:8px;">${(tenant.created_at ?? '').slice(0,10)}</div>
            </div>
        </div>
        ${featHtml}
        ${previewRows ? `
        <div class="section">
            <div class="section-header">Team Members <a href="${BASE}/app/${SLUG}/admin/users" style="float:right;font-size:.82rem;font-weight:400;color:#3b82f6;text-decoration:none;">View all →</a></div>
            <table class="tbl"><thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Status</th></tr></thead>
            <tbody>${previewRows}</tbody></table>
        </div>` : ''}`;
}

function renderUsers(users) {
    document.getElementById('page-title').textContent = 'Users';

    const rows = users.map(u => `
        <tr>
            <td>${u.name}</td>
            <td style="font-family:monospace;font-size:.83rem">${u.username}</td>
            <td style="color:#64748b">${u.email}</td>
            <td><span class="badge ${u.role === 'tenant_owner' ? 'badge-owner' : 'badge-user'}">${u.role === 'tenant_owner' ? 'Owner' : 'User'}</span></td>
            <td><span class="badge ${u.state ? 'badge-active' : 'badge-inactive'}">${u.state ? 'Active' : 'Inactive'}</span></td>
            <td style="color:#94a3b8;font-size:.8rem">${(u.stamp ?? '').slice(0,10)}</td>
        </tr>`).join('');

    document.getElementById('content-area').innerHTML = `
        <div class="section">
            <div class="section-header">Team Members — ${users.length} total</div>
            ${rows ? `<table class="tbl">
                <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Since</th></tr></thead>
                <tbody>${rows}</tbody>
            </table>` : '<div style="padding:20px;color:#64748b">No users yet.</div>'}
        </div>`;
}

// ── init ──────────────────────────────────────────────────────────────────────

async function init() {
    const [tenantRes, usersRes] = await Promise.all([
        api(`/api/tenants/${TENANT_ID}`),
        api(`/api/tenants/${TENANT_ID}/users`),
    ]);

    if (!tenantRes.success) { document.getElementById('content-area').innerHTML = '<p style="color:red">Failed to load tenant data.</p>'; return; }

    const tenant = tenantRes.data;
    const users  = usersRes.success ? usersRes.data : [];

    // Update sidebar plan badge and topbar status
    document.getElementById('plan-badge').textContent = tenant.plan_name ?? 'Free';
    const sc = statusColors[tenant.status] ?? { bg: '#f1f5f9', fg: '#374151' };
    const sb = document.getElementById('status-badge');
    sb.textContent = tenant.status;
    sb.style.background = sc.bg;
    sb.style.color = sc.fg;

    isUsersPage() ? renderUsers(users) : renderDashboard(tenant, users);
}

init();
</script>
</body>
</html>
<?php
    }

    // ── Public frontend (no auth) ────────────────────────────────────────────

    public function renderPublic(string $slug): void
    {
        $base = BASE_URL;
        $safeSlug = htmlspecialchars($slug);
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title">Loading…</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #fff; color: #1e293b; }
        .pub-nav { position: sticky; top: 0; z-index: 10; background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 60px; display: flex; align-items: center; justify-content: space-between; }
        .pub-nav-brand { font-size: 1.1rem; font-weight: 700; }
        .pub-nav-links a { font-size: .9rem; color: #64748b; text-decoration: none; padding: 6px 12px; border-radius: 6px; }
        .pub-nav-links a:hover { background: #f1f5f9; }
        .btn-login { background: #0f172a; color: #fff !important; padding: 7px 18px; border-radius: 7px; font-weight: 600; }
        .hero { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); color: #fff; padding: 80px 32px 64px; text-align: center; }
        .hero-avatar { width: 72px; height: 72px; background: rgba(255,255,255,.15); border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin: 0 auto 24px; }
        .hero h1 { font-size: 2.2rem; font-weight: 800; margin-bottom: 12px; }
        .hero p { font-size: 1rem; color: #94a3b8; margin-bottom: 28px; }
        .status-pill { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: .85rem; font-weight: 600; margin-bottom: 28px; }
        .hero-cta { display: inline-block; background: #3b82f6; color: #fff; padding: 13px 32px; border-radius: 8px; font-size: 1rem; font-weight: 600; text-decoration: none; }
        .suspended-banner { background: #fee2e2; border: 1px solid #fca5a5; padding: 18px 32px; text-align: center; color: #991b1b; font-weight: 500; }
        .features-section { padding: 64px 32px; background: #f8fafc; }
        .features-section h2 { text-align: center; font-size: 1.5rem; font-weight: 700; margin-bottom: 40px; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; max-width: 900px; margin: 0 auto; }
        .feat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 28px 24px; }
        .feat-card-icon { font-size: 2rem; margin-bottom: 14px; }
        .feat-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: 8px; }
        .feat-card p { font-size: .88rem; color: #64748b; line-height: 1.6; }
        .pub-footer { padding: 28px 32px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: .82rem; color: #94a3b8; }
    </style>
</head>
<body>

<div id="suspended-banner" style="display:none" class="suspended-banner">
    ⚠️ This account is currently <strong>suspended</strong>. Please contact support.
</div>

<header class="pub-nav">
    <span class="pub-nav-brand" id="nav-brand">…</span>
    <div class="pub-nav-links">
        <a href="<?php echo $base; ?>/auth" class="btn-login">Sign In</a>
    </div>
</header>

<section class="hero">
    <div class="hero-avatar" id="hero-avatar">…</div>
    <h1 id="hero-name">…</h1>
    <p>The platform built for your business. Sign in to access your account.</p>
    <div class="status-pill" id="status-pill"></div><br>
    <a href="<?php echo $base; ?>/auth" class="hero-cta" id="hero-cta">Sign In to Your Account →</a>
</section>

<section class="features-section">
    <h2>Everything you need</h2>
    <div class="features-grid">
        <div class="feat-card"><div class="feat-card-icon">📊</div><h3>Analytics Dashboard</h3><p>Get a clear overview of your activity, users, and key metrics in one place.</p></div>
        <div class="feat-card"><div class="feat-card-icon">👥</div><h3>Team Management</h3><p>Invite team members, assign roles, and manage access across your organisation.</p></div>
        <div class="feat-card"><div class="feat-card-icon">🔒</div><h3>Secure &amp; Reliable</h3><p>Role-based access control, secure sessions, and isolated multi-tenant data.</p></div>
    </div>
</section>

<footer class="pub-footer">
    <span id="footer-name">…</span>
    <span>Powered by upMVC SaaS</span>
</footer>

<script>
const BASE = <?php echo json_encode($base); ?>;
const SLUG = <?php echo json_encode($safeSlug); ?>;
const statusColors = {
    active:    { bg: '#dcfce7', fg: '#166534' },
    trial:     { bg: '#fef9c3', fg: '#92400e' },
    suspended: { bg: '#fee2e2', fg: '#991b1b' },
};

async function init() {
    const res  = await fetch(BASE + '/api/public/tenants/' + SLUG);
    const data = await res.json();

    if (!data.success || !data.data) {
        document.getElementById('hero-name').textContent = 'Tenant not found';
        document.getElementById('hero-cta').style.display = 'none';
        return;
    }

    const t       = data.data;
    const initial = t.name.charAt(0).toUpperCase();
    const sc      = statusColors[t.status] ?? { bg: '#f1f5f9', fg: '#374151' };

    document.title = t.name;
    document.getElementById('nav-brand').textContent    = '🏢 ' + t.name;
    document.getElementById('hero-avatar').textContent  = initial;
    document.getElementById('hero-name').textContent    = t.name;
    document.getElementById('footer-name').textContent  = t.name;

    const pill = document.getElementById('status-pill');
    pill.textContent = t.status;
    pill.style.background = sc.bg;
    pill.style.color = sc.fg;

    if (t.status === 'suspended') {
        document.getElementById('suspended-banner').style.display = 'block';
        document.getElementById('hero-cta').style.display = 'none';
    }
}

init();
</script>
</body>
</html>
<?php
    }
}
