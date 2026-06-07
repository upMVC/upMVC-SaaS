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
                <a href="<?php echo $base; ?>/logout"
                   style="padding:6px 14px;border:1px solid #cbd5e1;border-radius:4px;text-decoration:none;color:#374151;font-size:.85rem;">
                    Logout
                </a>
            </div>

            <div id="flash-msg" style="display:none;padding:10px 16px;border-radius:6px;margin-bottom:16px;"></div>

            <div id="tenants-table">
                <p style="color:#999;">Loading tenants…</p>
            </div>
        </div>

        <!-- Edit modal -->
        <div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;">
            <div style="background:#fff;border-radius:10px;padding:28px 32px;width:420px;max-width:94vw;box-shadow:0 8px 32px rgba(0,0,0,.2);">
                <h2 style="margin:0 0 20px;font-size:1.1rem;">Edit Tenant</h2>

                <label style="display:block;margin-bottom:4px;font-size:.85rem;font-weight:600;color:#374151;">Name</label>
                <input id="edit-name" type="text"
                       style="width:100%;box-sizing:border-box;padding:7px 10px;border:1px solid #cbd5e1;border-radius:5px;font-size:.9rem;margin-bottom:14px;">

                <label style="display:block;margin-bottom:4px;font-size:.85rem;font-weight:600;color:#374151;">Status</label>
                <select id="edit-status"
                        style="width:100%;padding:7px 10px;border:1px solid #cbd5e1;border-radius:5px;font-size:.9rem;margin-bottom:14px;">
                    <option value="active">active</option>
                    <option value="trial">trial</option>
                    <option value="suspended">suspended</option>
                </select>

                <label style="display:block;margin-bottom:4px;font-size:.85rem;font-weight:600;color:#374151;">Plan</label>
                <select id="edit-plan"
                        style="width:100%;padding:7px 10px;border:1px solid #cbd5e1;border-radius:5px;font-size:.9rem;margin-bottom:22px;">
                </select>

                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button onclick="closeEdit()"
                            style="padding:7px 18px;border:1px solid #cbd5e1;border-radius:5px;background:#fff;cursor:pointer;font-size:.85rem;">
                        Cancel
                    </button>
                    <button onclick="saveEdit()"
                            style="padding:7px 18px;background:#2563eb;color:#fff;border:none;border-radius:5px;cursor:pointer;font-size:.85rem;font-weight:600;">
                        Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden form used to POST impersonate token to assume endpoint -->
        <form id="assume-form" action="<?php echo $base; ?>/platform-admin/assume" method="POST" style="display:none;">
            <input type="hidden" name="token" id="assume-token">
        </form>

        <script>
        const BASE  = <?php echo json_encode($base); ?>;
        const token = <?php echo json_encode($_SESSION['jwt_token'] ?? ''); ?>;

        let plans     = [];
        let editingId = null;

        // ── helpers ─────────────────────────────────────────────────────────

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

        // ── data loading ─────────────────────────────────────────────────────

        async function loadPlans() {
            const data = await api('/api/plans');
            if (data && Array.isArray(data)) {
                plans = data;
            } else if (data && data.success && Array.isArray(data.data)) {
                plans = data.data;
            }
            const sel = document.getElementById('edit-plan');
            sel.innerHTML = plans.map(p =>
                `<option value="${p.id}">${p.name} — $${p.price}/mo</option>`
            ).join('');
        }

        async function loadTenants() {
            const data = await api('/api/admin/tenants');
            if (!data.success) { flash('Failed to load tenants', 'error'); return; }

            document.getElementById('tenant-count').textContent = data.data.total + ' total';

            const statusColor = { active:'#d1fae5', trial:'#fef9c3', suspended:'#fee2e2' };

            const rows = data.data.tenants.map(t => `
                <tr style="border-bottom:1px solid #e2e8f0;">
                    <td style="padding:10px 12px;">${t.id}</td>
                    <td style="padding:10px 12px;font-family:monospace;">${t.slug}</td>
                    <td style="padding:10px 12px;">${t.name}</td>
                    <td style="padding:10px 12px;">
                        <span style="padding:2px 8px;border-radius:12px;font-size:.8rem;
                            background:${statusColor[t.status] ?? '#f1f5f9'}">
                            ${t.status}
                        </span>
                    </td>
                    <td style="padding:10px 12px;">${t.plan_name ?? '—'}</td>
                    <td style="padding:10px 12px;color:#64748b;font-size:.8rem;">${t.created_at?.slice(0,10)}</td>
                    <td style="padding:10px 12px;display:flex;gap:6px;flex-wrap:wrap;">
                        <button onclick="openEdit(${t.id},'${t.name.replace(/'/g,"\\'")}','${t.status}',${t.plan_id ?? 'null'})"
                            style="padding:3px 10px;background:#2563eb;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:.8rem;">
                            Edit
                        </button>
                        <button onclick="impersonate(${t.id})"
                            style="padding:3px 10px;background:#8b5cf6;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:.8rem;">
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

        // ── edit modal ───────────────────────────────────────────────────────

        function openEdit(id, name, status, planId) {
            editingId = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-status').value = status;
            const planSel = document.getElementById('edit-plan');
            if (planId) planSel.value = planId;
            const modal = document.getElementById('edit-modal');
            modal.style.display = 'flex';
        }

        function closeEdit() {
            document.getElementById('edit-modal').style.display = 'none';
            editingId = null;
        }

        async function saveEdit() {
            if (!editingId) return;
            const body = {
                name:    document.getElementById('edit-name').value.trim(),
                status:  document.getElementById('edit-status').value,
                plan_id: parseInt(document.getElementById('edit-plan').value, 10),
            };
            const data = await api(`/api/admin/tenants/${editingId}`, {
                method: 'PATCH',
                body: JSON.stringify(body),
            });
            if (data.success) {
                flash('Tenant updated', 'success');
                closeEdit();
                loadTenants();
            } else {
                flash(data.error ?? 'Update failed', 'error');
            }
        }

        // close modal on backdrop click
        document.getElementById('edit-modal').addEventListener('click', function(e) {
            if (e.target === this) closeEdit();
        });

        // ── impersonate ──────────────────────────────────────────────────────

        async function impersonate(tenantId) {
            const data = await api('/api/admin/impersonate', {
                method: 'POST',
                body: JSON.stringify({ tenant_id: tenantId }),
            });
            if (!data.success) { flash(data.error ?? 'Impersonate failed', 'error'); return; }

            // POST the token to the PHP assume endpoint — it swaps the session and redirects to /app
            document.getElementById('assume-token').value = data.data.access_token;
            document.getElementById('assume-form').submit();
        }

        // ── init ─────────────────────────────────────────────────────────────

        loadPlans();
        loadTenants();
        </script>

        <?php
        $bv->endBody();
    }
}
