<?php

namespace App\Modules\PlatformAdmin;

use App\Common\Bmvc\BaseView;

class View
{
    public function render(): void
    {
        $base  = BASE_URL;
        $bv    = new BaseView();
        $bv->startHead('Platform Admin');
        $bv->endHead();
        $bv->startBody('Platform Admin');
        ?>

        <div id="platform-admin-app" style="max-width:1100px;margin:30px auto;padding:0 16px;font-family:sans-serif;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h1 style="margin:0;font-size:1.5rem;">Platform Admin
                    <small id="tenant-count" style="font-size:.75rem;color:#666;margin-left:8px;"></small>
                </h1>
                <a href="<?php echo $base; ?>/auth/logout"
                   style="padding:6px 14px;border:1px solid #cbd5e1;border-radius:4px;text-decoration:none;color:#374151;font-size:.85rem;">
                    Logout
                </a>
            </div>

            <div id="flash-msg" style="display:none;padding:10px 16px;border-radius:6px;margin-bottom:16px;"></div>

            <div id="tenants-table">
                <p style="color:#999;">Loading tenants…</p>
            </div>
        </div>

        <script>
        const BASE = <?php echo json_encode($base); ?>;
        const token = sessionStorage.getItem('access_token') || localStorage.getItem('access_token') || '';

        function flash(msg, type) {
            const el = document.getElementById('flash-msg');
            el.textContent = msg;
            el.style.display = 'block';
            el.style.background = type === 'success' ? '#d1fae5' : '#fee2e2';
            el.style.color      = type === 'success' ? '#065f46' : '#991b1b';
            el.style.border     = '1px solid ' + (type === 'success' ? '#6ee7b7' : '#fca5a5');
            setTimeout(() => el.style.display = 'none', 4000);
        }

        async function api(path, opts = {}) {
            const res = await fetch(BASE + path, {
                headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', ...opts.headers },
                ...opts,
            });
            return res.json();
        }

        async function loadTenants() {
            const data = await api('/api/admin/tenants');
            if (!data.success) { flash('Failed to load tenants', 'error'); return; }

            document.getElementById('tenant-count').textContent = data.data.total + ' total';

            const rows = data.data.tenants.map(t => `
                <tr style="border-bottom:1px solid #e2e8f0;">
                    <td style="padding:10px 12px;">${t.id}</td>
                    <td style="padding:10px 12px;font-family:monospace;">${t.slug}</td>
                    <td style="padding:10px 12px;">${t.name}</td>
                    <td style="padding:10px 12px;">
                        <span style="padding:2px 8px;border-radius:12px;font-size:.8rem;
                            background:${{ active:'#d1fae5', trial:'#fef9c3', suspended:'#fee2e2' }[t.status] ?? '#f1f5f9'}">
                            ${t.status}
                        </span>
                    </td>
                    <td style="padding:10px 12px;">${t.plan_name ?? '—'}</td>
                    <td style="padding:10px 12px;color:#64748b;font-size:.8rem;">${t.created_at?.slice(0,10)}</td>
                    <td style="padding:10px 12px;">
                        <select onchange="setStatus(${t.id}, this.value)" style="padding:3px 6px;border:1px solid #cbd5e1;border-radius:4px;font-size:.8rem;margin-right:4px;">
                            ${['active','trial','suspended'].map(s => `<option ${t.status===s?'selected':''}>${s}</option>`).join('')}
                        </select>
                        <button onclick="impersonate(${t.id})"
                            style="padding:3px 8px;background:#8b5cf6;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:.8rem;">
                            Login as
                        </button>
                    </td>
                </tr>`).join('');

            document.getElementById('tenants-table').innerHTML = `
                <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
                    <thead>
                        <tr style="background:#f1f5f9;text-align:left;">
                            <th style="padding:10px 12px;border-bottom:2px solid #e2e8f0;">ID</th>
                            <th style="padding:10px 12px;border-bottom:2px solid #e2e8f0;">Slug</th>
                            <th style="padding:10px 12px;border-bottom:2px solid #e2e8f0;">Name</th>
                            <th style="padding:10px 12px;border-bottom:2px solid #e2e8f0;">Status</th>
                            <th style="padding:10px 12px;border-bottom:2px solid #e2e8f0;">Plan</th>
                            <th style="padding:10px 12px;border-bottom:2px solid #e2e8f0;">Created</th>
                            <th style="padding:10px 12px;border-bottom:2px solid #e2e8f0;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>`;
        }

        async function setStatus(id, status) {
            const data = await api(`/api/admin/tenants/${id}/status`, {
                method: 'PATCH',
                body: JSON.stringify({ status }),
            });
            data.success ? flash('Status updated', 'success') : flash(data.error, 'error');
            loadTenants();
        }

        async function impersonate(tenantId) {
            const data = await api('/api/admin/impersonate', {
                method: 'POST',
                body: JSON.stringify({ tenant_id: tenantId }),
            });
            if (!data.success) { flash(data.error, 'error'); return; }
            sessionStorage.setItem('access_token', data.data.access_token);
            flash('Impersonating ' + data.data.tenant.name + ' — token stored in sessionStorage', 'success');
        }

        loadTenants();
        </script>

        <?php
        $bv->endBody();
    }
}
