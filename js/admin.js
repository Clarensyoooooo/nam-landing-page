/* =============================================================================
   admin.js — NAM Builders Admin Panel
   ============================================================================= */


/* =============================================================================
   1. SIDEBAR TOGGLE (mobile)
   ============================================================================= */

(function initSidebar() {
    const sidebar   = document.getElementById('adminSidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const closeBtn  = document.getElementById('sidebarCloseBtn');

    if (!sidebar || !overlay || !toggleBtn) return;

    function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    toggleBtn.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('.admin-nav-link').forEach(link => {
        link.addEventListener('click', () => { if (window.innerWidth <= 768) closeSidebar(); });
    });

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
}());


/* =============================================================================
   2. TOAST NOTIFICATION SYSTEM
   ============================================================================= */

(function initToasts() {
    const DURATION = 5000;

    const icons = {
        success : 'fas fa-check-circle',
        danger  : 'fas fa-exclamation-circle',
        warning : 'fas fa-exclamation-triangle',
        info    : 'fas fa-info-circle',
    };

    const titles = {
        success : 'Success',
        danger  : 'Error',
        warning : 'Warning',
        info    : 'Info',
    };

    window.showToast = function (message, type = 'info') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `adm-toast toast-${type}`;
        toast.innerHTML = `
            <div class="adm-toast-icon"><i class="${icons[type] ?? icons.info}"></i></div>
            <div class="adm-toast-body">
                <div class="adm-toast-title">${titles[type] ?? 'Notice'}</div>
                <div class="adm-toast-msg">${message}</div>
            </div>
            <button class="adm-toast-close" aria-label="Dismiss">&times;</button>
            <div class="adm-toast-progress">
                <div class="adm-toast-progress-fill"></div>
            </div>
        `;
        container.appendChild(toast);

        toast.querySelector('.adm-toast-close').addEventListener('click', () => removeToast(toast));

        const fill = toast.querySelector('.adm-toast-progress-fill');
        setTimeout(() => {
            fill.style.transition = `width ${DURATION}ms linear`;
            fill.style.width = '0%';
        }, 30);

        let timer = setTimeout(() => removeToast(toast), DURATION);

        toast.addEventListener('mouseenter', () => {
            clearTimeout(timer);
            fill.style.transitionDuration = '0ms';
        });
        toast.addEventListener('mouseleave', () => {
            const remaining = (parseFloat(fill.style.width) / 100) * DURATION;
            fill.style.transition = `width ${remaining}ms linear`;
            fill.style.width = '0%';
            timer = setTimeout(() => removeToast(toast), remaining);
        });
    };

    function removeToast(toast) {
        toast.classList.add('removing');
        toast.addEventListener('animationend', () => toast.parentNode?.removeChild(toast));
    }

    // ── Show any pending toast stored in sessionStorage (after redirect) ──
    document.addEventListener('DOMContentLoaded', () => {
        try {
            const pending = sessionStorage.getItem('adminToast');
            if (pending) {
                sessionStorage.removeItem('adminToast');
                const { message, type } = JSON.parse(pending);
                if (message) showToast(message, type || 'success');
            }
        } catch (_) {}

        // Also handle legacy PHP setAlert() flash via data attribute
        const el = document.getElementById('phpAlertData');
        if (el) {
            const msg  = el.getAttribute('data-message');
            const type = el.getAttribute('data-type') || 'info';
            if (msg) showToast(msg, type);
            el.parentNode.removeChild(el);
        }
    });
}());


/* =============================================================================
   SHARED FETCH HELPER
   ============================================================================= */

/**
 * adminFetch — POST form data, parse JSON, call onSuccess or onError.
 *
 * On success the backend returns { success: true, message: "..." }.
 * Callers that need to redirect after success should call
 *   redirectWithToast(url, message, type)
 * inside their onSuccess callback.
 */
function adminFetch(url, formData, onSuccess, onError) {
    fetch(url, { method: 'POST', body: formData })
        .then(r => {
            if (!r.ok) throw new Error('Server returned status ' + r.status + '. You may need to log in again.');
            return r.text();
        })
        .then(text => {
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Non-JSON response:', text.substring(0, 300));
                throw new Error('Unexpected server response. Check PHP error logs.');
            }
            if (data.success) {
                onSuccess(data);
            } else {
                onError(data.message || 'An unknown error occurred.');
            }
        })
        .catch(err => onError(err.message || 'Network error. Please try again.'));
}

/**
 * Store a toast message in sessionStorage then redirect.
 * The toast fires once the new page loads (picked up in initToasts DOMContentLoaded).
 */
function redirectWithToast(url, message, type = 'success') {
    try {
        sessionStorage.setItem('adminToast', JSON.stringify({ message, type }));
    } catch (_) {}
    window.location.href = url;
}


/* =============================================================================
   3. CLIENTS PAGE
   ============================================================================= */

let _clientDeleteId = null;

function openClientDeleteConfirm(id, name) {
    _clientDeleteId = id;
    document.getElementById('deleteClientName').textContent = `"${name}"`;
    document.getElementById('clientDeleteConfirmModal').classList.add('active');
}

function closeClientDeleteConfirm() {
    document.getElementById('clientDeleteConfirmModal').classList.remove('active');
    _clientDeleteId = null;
}

function executeClientDelete() {
    if (_clientDeleteId) window.location.href = `../backend/delete_client.php?id=${_clientDeleteId}`;
}

function openAddClientModal() {
    document.getElementById('modalTitle').innerText = 'Add New Client';
    document.getElementById('clientForm').reset();
    document.getElementById('clientId').value = '';
    document.getElementById('clientModal').classList.add('active');
}

function closeClientModal() {
    document.getElementById('clientModal').classList.remove('active');
}

function editClient(id) {
    fetch(`../backend/get_client.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { showToast('Could not load client.', 'danger'); return; }
            const c = data.data;
            document.getElementById('modalTitle').innerText    = 'Edit Client';
            document.getElementById('clientId').value          = c.id;
            document.getElementById('clientName').value        = c.client_name;
            document.getElementById('clientDescription').value = c.description;
            document.getElementById('clientOrder').value       = c.sort_order;
            document.getElementById('clientActive').checked    = c.is_active == 1;
            document.getElementById('clientModal').classList.add('active');
        })
        .catch(() => showToast('Network error loading client.', 'danger'));
}

function submitClientForm(event) {
    if (event && event.preventDefault) event.preventDefault();
    const form = document.getElementById('clientForm');
    const clientName = form.querySelector('[name="client_name"]')?.value ?? '';
    if (!clientName.trim()) { showToast('Client name is required.', 'danger'); form.querySelector('[name="client_name"]')?.focus(); return; }
    const btn = document.getElementById('clientSaveBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…'; }
    adminFetch('../backend/save_client.php', new FormData(form),
        data => redirectWithToast('dashboard.php?page=clients', data.message, 'success'),
        errMsg => { showToast(errMsg, 'danger'); if (btn) { btn.disabled = false; btn.innerHTML = 'Save Client'; } }
    );
}

function initClientModalListeners() {
    document.getElementById('clientModal')?.addEventListener('click', e => {
        if (e.target === e.currentTarget) closeClientModal();
    });
    document.getElementById('clientDeleteConfirmModal')?.addEventListener('click', e => {
        if (e.target === e.currentTarget) closeClientDeleteConfirm();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeClientModal(); closeClientDeleteConfirm(); }
    });
}
document.addEventListener('DOMContentLoaded', initClientModalListeners);


/* =============================================================================
   4. SERVICES PAGE
   ============================================================================= */

let _svcDeleteId = null;

function openSvcDeleteConfirm(id, name) {
    _svcDeleteId = id;
    document.getElementById('deleteSvcName').textContent = `"${name}"`;
    document.getElementById('svcDeleteConfirmModal').classList.add('active');
}

function closeSvcDeleteConfirm() {
    document.getElementById('svcDeleteConfirmModal').classList.remove('active');
    _svcDeleteId = null;
}

function executeSvcDelete() {
    if (_svcDeleteId) window.location.href = `../backend/delete_service.php?id=${_svcDeleteId}`;
}

function openAddServiceModal() {
    document.getElementById('modalTitle').innerText = 'Add New Service';
    document.getElementById('serviceForm').reset();
    document.getElementById('serviceId').value = '';
    document.getElementById('newImagesPreview').innerHTML = '';
    document.getElementById('existingImagesSection').style.display = 'none';
    document.getElementById('existingImagesGrid').innerHTML = '';
    document.getElementById('serviceModal').classList.add('active');
}

function closeServiceModal() {
    document.getElementById('serviceModal').classList.remove('active');
}

function previewNewImages(input) {
    const preview = document.getElementById('newImagesPreview');
    preview.innerHTML = '';
    Array.from(input.files ?? []).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;display:inline-block;';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid var(--primary-color);display:block;';
            const lbl = document.createElement('span');
            lbl.textContent = 'NEW';
            lbl.style.cssText = 'position:absolute;bottom:4px;left:50%;transform:translateX(-50%);background:var(--primary-color);color:#fff;font-size:9px;font-weight:800;padding:1px 6px;border-radius:4px;';
            wrap.appendChild(img);
            wrap.appendChild(lbl);
            preview.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}

function editService(id) {
    fetch(`../backend/get_service.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { showToast('Could not load service.', 'danger'); return; }
            const s = data.data;
            document.getElementById('modalTitle').innerText       = 'Edit Service';
            document.getElementById('serviceId').value            = s.id;
            document.getElementById('serviceName').value          = s.service_name;
            document.getElementById('serviceDescription').value   = s.description;
            document.getElementById('serviceOrder').value         = s.sort_order;
            document.getElementById('serviceActive').checked      = s.is_active == 1;
            document.getElementById('newImagesPreview').innerHTML = '';

            const section = document.getElementById('existingImagesSection');
            const grid    = document.getElementById('existingImagesGrid');
            grid.innerHTML = '';
            section.style.display = 'block';

            const images = s.images ?? [];
            if (images.length > 0) {
                images.forEach(img => grid.appendChild(buildExistingImgItem(img)));
            } else {
                grid.innerHTML = '<div class="existing-imgs-empty"><i class="fas fa-image"></i> No images uploaded yet.</div>';
            }

            document.getElementById('serviceModal').classList.add('active');
        })
        .catch(() => showToast('Network error loading service.', 'danger'));
}

function buildExistingImgItem(img) {
    const wrapper = document.createElement('div');
    wrapper.className = 'existing-img-item';
    wrapper.id = `img-wrapper-${img.id}`;

    const imgEl = document.createElement('img');
    imgEl.src = window.UPLOADS_URL + img.image_path;
    imgEl.alt = 'Service image';
    imgEl.onerror = function () {
        this.style.display = 'none';
        if (!wrapper.querySelector('.img-placeholder-box')) {
            const ph = document.createElement('div');
            ph.className = 'img-placeholder-box';
            ph.innerHTML = '<i class="fas fa-image"></i>';
            wrapper.insertBefore(ph, wrapper.firstChild);
        }
    };

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'img-del-btn';
    btn.title = 'Remove image';
    btn.innerHTML = '&times;';
    btn.onclick = () => deleteServiceImage(img.id, wrapper);

    wrapper.appendChild(imgEl);
    wrapper.appendChild(btn);
    return wrapper;
}

function deleteServiceImage(imgId, wrapperEl) {
    fetch(`../backend/delete_service_img.php?id=${imgId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                wrapperEl?.remove();
                const grid = document.getElementById('existingImagesGrid');
                if (grid && grid.querySelectorAll('.existing-img-item').length === 0) {
                    grid.innerHTML = '<div class="existing-imgs-empty"><i class="fas fa-image"></i> No images uploaded yet.</div>';
                }
            } else {
                showToast(`Failed to delete image: ${data.message}`, 'danger');
            }
        })
        .catch(() => showToast('Network error. Please try again.', 'danger'));
}

function submitServiceForm(event) {
    if (event && event.preventDefault) event.preventDefault();
    const form        = document.getElementById('serviceForm');
    const serviceName = form.querySelector('[name="service_name"]')?.value ?? '';
    const serviceDesc = form.querySelector('[name="description"]')?.value ?? '';
    if (!serviceName.trim()) { showToast('Service name is required.', 'danger'); form.querySelector('[name="service_name"]')?.focus(); return; }
    if (!serviceDesc.trim()) { showToast('Service description is required.', 'danger'); form.querySelector('[name="description"]')?.focus(); return; }
    const btn = document.getElementById('svcSaveBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…'; }
    adminFetch('../backend/save_service.php', new FormData(form),
        data => redirectWithToast('dashboard.php?page=services', data.message, 'success'),
        errMsg => { showToast(errMsg, 'danger'); if (btn) { btn.disabled = false; btn.innerHTML = 'Save Service'; } }
    );
}

function initServiceModalListeners() {
    document.getElementById('serviceModal')?.addEventListener('click', e => {
        if (e.target === e.currentTarget) closeServiceModal();
    });
    document.getElementById('svcDeleteConfirmModal')?.addEventListener('click', e => {
        if (e.target === e.currentTarget) closeSvcDeleteConfirm();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeServiceModal(); closeSvcDeleteConfirm(); }
    });
}
document.addEventListener('DOMContentLoaded', initServiceModalListeners);


/* =============================================================================
   5. MESSAGES PAGE
   ============================================================================= */

let currentMessageId = null;
let _msgDeleteId     = null;
let _currentMsgFilter = 'all';

function setMsgBadgeText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

function initMessageBadges() {
    const rows = document.querySelectorAll('#messagesTable tbody tr:not(#msgNoResults)');
    const c = { all: 0, unread: 0, read: 0, replied: 0 };
    rows.forEach(row => {
        const s = row.getAttribute('data-status');
        c.all++;
        if (c[s] !== undefined) c[s]++;
    });
    setMsgBadgeText('countAll',     `(${c.all})`);
    setMsgBadgeText('countUnread',  `(${c.unread})`);
    setMsgBadgeText('countRead',    `(${c.read})`);
    setMsgBadgeText('countReplied', `(${c.replied})`);
}

function filterByStatus(btn, filter) {
    _currentMsgFilter = filter;
    document.querySelectorAll('.msg-filter-btn').forEach(b => b.style.boxShadow = '');
    btn.style.boxShadow = '0 0 0 2px var(--primary-color)';
    applyMessageFilters();
}

function applyMessageFilters(query) {
    query = (query ?? document.getElementById('msgSearchInput')?.value ?? '').toLowerCase().trim();
    const rows = document.querySelectorAll('#messagesTable tbody tr:not(#msgNoResults)');
    let visible = 0;
    rows.forEach(row => {
        const searchMatch = query === '' || (row.getAttribute('data-search') ?? '').includes(query);
        const statusMatch = _currentMsgFilter === 'all' || row.getAttribute('data-status') === _currentMsgFilter;
        const show = searchMatch && statusMatch;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const noResults = document.getElementById('msgNoResults');
    if (noResults) noResults.style.display = visible === 0 ? '' : 'none';
}

function openMsgDeleteConfirm(id, name) {
    _msgDeleteId = id;
    document.getElementById('deleteMsgName').textContent = `"${name}"`;
    document.getElementById('msgDeleteConfirmModal').classList.add('active');
}

function closeMsgDeleteConfirm() {
    document.getElementById('msgDeleteConfirmModal').classList.remove('active');
    _msgDeleteId = null;
}

function executeMsgDelete() {
    if (_msgDeleteId) window.location.href = `../backend/delete_message.php?id=${_msgDeleteId}`;
}

function viewMessage(id) {
    currentMessageId = id;
    document.getElementById('mmReplyTextarea').value = '';

    fetch(`../backend/get_message.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { showToast('Could not load message.', 'danger'); return; }
            const msg  = data.data;
            document.getElementById('mmAvatar').textContent    = (msg.full_name || '?').trim().charAt(0).toUpperCase();
            document.getElementById('mmName').textContent      = msg.full_name || '—';
            document.getElementById('mmEmailSub').textContent  = msg.email    || '—';
            document.getElementById('mmReplyToTag').textContent = msg.email   || '—';

            const date = new Date(msg.created_at).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' });
            const isReplied = msg.is_replied == 1;
            const isRead    = msg.is_read    == 1;
            let sc, si, sl;
            if (isReplied)   { sc = 'replied'; si = 'fa-reply';  sl = 'Replied'; }
            else if (isRead) { sc = 'read';    si = 'fa-check';  sl = 'Read';    }
            else             { sc = 'unread';  si = 'fa-circle'; sl = 'Unread';  }

            let chips = `
                <span class="mm-meta-chip mm-status-chip ${sc}"><i class="fas ${si}" style="font-size:.65rem;"></i> ${sl}</span>
                <span class="mm-meta-chip"><i class="fas fa-clock"></i> ${escHtml(date)}</span>
                <span class="mm-meta-chip"><i class="fas fa-envelope"></i> ${escHtml(msg.email)}</span>
            `;
            if (msg.phone)          chips += `<span class="mm-meta-chip"><i class="fas fa-phone"></i> ${escHtml(msg.phone)}</span>`;
            if (msg.service_needed) chips += `<span class="mm-meta-chip svc-chip"><i class="fas fa-cog"></i> ${escHtml(msg.service_needed)}</span>`;

            document.getElementById('mmMetaRow').innerHTML      = chips;
            document.getElementById('mmMessageBox').textContent = msg.message || '(no message body)';
            document.getElementById('messageModal').classList.add('active');
            document.body.style.overflow = 'hidden';

            // Optimistically mark row as read in the table
            document.querySelectorAll('#messagesTable tbody tr').forEach(row => {
                if (row.querySelector(`[onclick="viewMessage(${id})"]`)) {
                    const badge = row.querySelector('.status-badge');
                    if (badge && badge.classList.contains('unread')) {
                        badge.className = 'status-badge read';
                        badge.innerHTML = '<i class="fas fa-check"></i> Read';
                        row.setAttribute('data-status', 'read');
                    }
                }
            });
        })
        .catch(() => showToast('Network error loading message.', 'danger'));
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.remove('active');
    document.body.style.overflow = '';
    currentMessageId = null;
}

function sendReply() {
    const body = document.getElementById('mmReplyTextarea').value.trim();
    if (!body) { showToast('Please type a reply before sending.', 'warning'); document.getElementById('mmReplyTextarea').focus(); return; }

    const btn = document.getElementById('mmSendReplyBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…';

    const fd = new FormData();
    fd.append('message_id', currentMessageId);
    fd.append('reply_body', body);

    fetch('../backend/reply_message.php', { method: 'POST', body: fd })
        .then(r => r.text())
        .then(text => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reply';
            let data;
            try { data = JSON.parse(text); } catch (e) { showToast('Server error. Check console.', 'danger'); return; }
            if (data.success) {
                const id = currentMessageId;
                closeMessageModal();
                showToast(data.message, 'success');
                document.querySelectorAll('#messagesTable tbody tr').forEach(row => {
                    if (row.querySelector(`[onclick="viewMessage(${id})"]`)) {
                        const badge = row.querySelector('.status-badge');
                        if (badge) { badge.className = 'status-badge replied'; badge.innerHTML = '<i class="fas fa-reply"></i> Replied'; row.setAttribute('data-status', 'replied'); }
                    }
                });
            } else {
                showToast(data.message || 'Failed to send reply.', 'danger');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reply';
            showToast('Could not reach the server. Please try again.', 'danger');
        });
}

function initMessagesPageListeners() {
    document.getElementById('messageModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeMessageModal(); });
    document.getElementById('msgDeleteConfirmModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeMsgDeleteConfirm(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeMessageModal(); closeMsgDeleteConfirm(); } });
    initMessageBadges();
}
document.addEventListener('DOMContentLoaded', initMessagesPageListeners);

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
}


/* =============================================================================
   6. SUPPLIES PAGE
   ============================================================================= */

const SUP_PER_PAGE = 10;
let _supCurrentCat   = 'all';
let _supCurrentPage  = 1;
let _supMatchingRows = [];

function supGetAllRows() {
    return Array.from(document.querySelectorAll('#suppliesTable tbody tr'));
}

function supApplyFilters() {
    const query   = (document.getElementById('supSearchInput')?.value ?? '').toLowerCase().trim();
    const allRows = supGetAllRows();
    _supMatchingRows = allRows.filter(row => {
        const catMatch    = _supCurrentCat === 'all' || row.getAttribute('data-cat') === String(_supCurrentCat);
        const searchMatch = query === '' || (row.getAttribute('data-search') ?? '').includes(query);
        return catMatch && searchMatch;
    });
    supRenderPage();
}

function supRenderPage() {
    const total      = _supMatchingRows.length;
    const totalPages = Math.max(1, Math.ceil(total / SUP_PER_PAGE));
    if (_supCurrentPage > totalPages) _supCurrentPage = totalPages;
    if (_supCurrentPage < 1)          _supCurrentPage = 1;

    const start = (_supCurrentPage - 1) * SUP_PER_PAGE;
    const end   = start + SUP_PER_PAGE;

    supGetAllRows().forEach(r => r.style.display = 'none');
    _supMatchingRows.forEach((r, i) => { r.style.display = (i >= start && i < end) ? '' : 'none'; });

    const noRes = document.getElementById('supNoResults');
    if (noRes) noRes.style.display = total === 0 ? '' : 'none';

    const bar    = document.getElementById('supPaginationBar');
    const infoEl = document.getElementById('supPagInfo');
    const btnsEl = document.getElementById('supPagBtns');
    if (!bar) return;
    if (total === 0) { bar.style.display = 'none'; return; }

    bar.style.display = 'flex';
    infoEl.innerHTML  = `Showing <strong>${start + 1}–${Math.min(end, total)}</strong> of <strong>${total}</strong> supplies`;

    btnsEl.innerHTML = '';
    const prevBtn = supMakePagBtn('<i class="fas fa-chevron-left"></i>', _supCurrentPage <= 1, () => supGoToPage(_supCurrentPage - 1));
    btnsEl.appendChild(prevBtn);

    const range  = 2;
    const pStart = Math.max(1, _supCurrentPage - range);
    const pEnd   = Math.min(totalPages, _supCurrentPage + range);
    if (pStart > 1) { btnsEl.appendChild(supMakePagBtn('1', false, () => supGoToPage(1))); if (pStart > 2) btnsEl.appendChild(supMakeEllipsis()); }
    for (let p = pStart; p <= pEnd; p++) {
        const btn = supMakePagBtn(String(p), false, () => supGoToPage(p));
        if (p === _supCurrentPage) btn.classList.add('active');
        btnsEl.appendChild(btn);
    }
    if (pEnd < totalPages - 1) btnsEl.appendChild(supMakeEllipsis());
    if (pEnd < totalPages) btnsEl.appendChild(supMakePagBtn(String(totalPages), false, () => supGoToPage(totalPages)));

    const nextBtn = supMakePagBtn('<i class="fas fa-chevron-right"></i>', _supCurrentPage >= totalPages, () => supGoToPage(_supCurrentPage + 1));
    btnsEl.appendChild(nextBtn);
}

function supMakePagBtn(html, disabled, onClick) {
    const btn = document.createElement('button');
    btn.className = 'adm-pag-btn';
    btn.innerHTML = html;
    btn.disabled  = disabled;
    btn.addEventListener('click', onClick);
    return btn;
}

function supMakeEllipsis() {
    const span = document.createElement('span');
    span.style.cssText = 'padding:0 .25rem;color:var(--text-light);';
    span.textContent   = '…';
    return span;
}

function supGoToPage(p) { _supCurrentPage = p; supRenderPage(); }

function switchSupTab(btn, cat) {
    document.querySelectorAll('.sup-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const supView = document.getElementById('suppliesView');
    const catView = document.getElementById('categoriesView');
    if (cat === 'categories') { supView.style.display = 'none'; catView.style.display = ''; return; }
    catView.style.display = 'none';
    supView.style.display = '';
    _supCurrentCat  = cat;
    _supCurrentPage = 1;
    supApplyFilters();
}

function openAddSupplyModal() {
    document.getElementById('supplyModalTitle').innerText = 'Add New Supply';
    document.getElementById('supplyForm').reset();
    document.getElementById('supplyId').value = '';
    document.getElementById('supImgPreview').innerHTML = '';
    document.getElementById('supplySubmitBtn').disabled = false;
    document.getElementById('supplySubmitBtn').innerHTML = 'Save Supply';
    document.getElementById('supplyModal').classList.add('active');
}

function closeSupplyModal() { document.getElementById('supplyModal').classList.remove('active'); }

function previewSupplyImg(input) {
    const preview = document.getElementById('supImgPreview');
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { const img = document.createElement('img'); img.src = e.target.result; preview.appendChild(img); };
        reader.readAsDataURL(input.files[0]);
    }
}

function editSupply(id) {
    fetch(`../backend/get_supply.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { showToast('Could not load supply.', 'danger'); return; }
            const s = data.data;
            document.getElementById('supplyModalTitle').innerText = 'Edit Supply';
            document.getElementById('supplyId').value             = s.id;
            document.getElementById('supCategory').value          = s.category_id;
            document.getElementById('supName').value              = s.supply_name;
            document.getElementById('supDescription').value       = s.description || '';
            document.getElementById('supOrder').value             = s.sort_order;
            document.getElementById('supActive').checked          = s.is_active == 1;
            document.getElementById('supplySubmitBtn').disabled   = false;
            document.getElementById('supplySubmitBtn').innerHTML  = 'Save Supply';
            const preview = document.getElementById('supImgPreview');
            preview.innerHTML = '';
            if (s.image_path) {
                const img = document.createElement('img');
                img.src = window.UPLOADS_URL + s.image_path;
                img.alt = s.supply_name;
                img.onerror = () => preview.innerHTML = '';
                preview.appendChild(img);
            }
            document.getElementById('supplyModal').classList.add('active');
        })
        .catch(() => showToast('Network error loading supply.', 'danger'));
}

function submitSupplyForm(e) {
    if (e && e.preventDefault) e.preventDefault();
    const form    = document.getElementById('supplyForm');
    const catId   = form.querySelector('[name="category_id"]')?.value ?? '';
    const supName = form.querySelector('[name="supply_name"]')?.value ?? '';
    if (!catId)        { showToast('Please select a category.', 'danger'); form.querySelector('[name="category_id"]')?.focus(); return; }
    if (!supName.trim()) { showToast('Supply name is required.', 'danger'); form.querySelector('[name="supply_name"]')?.focus(); return; }
    const btn = document.getElementById('supplySubmitBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…'; }
    adminFetch('../backend/save_supply.php', new FormData(form),
        data => redirectWithToast('dashboard.php?page=supplies', data.message, 'success'),
        errMsg => { showToast(errMsg, 'danger'); if (btn) { btn.disabled = false; btn.innerHTML = 'Save Supply'; } }
    );
}

function openAddCategoryModal() {
    document.getElementById('categoryModalTitle').innerText = 'Add Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryModal').classList.add('active');
}

function closeCategoryModal() { document.getElementById('categoryModal').classList.remove('active'); }

function editCategory(id) {
    fetch(`../backend/get_category.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { showToast('Could not load category.', 'danger'); return; }
            const c = data.data;
            document.getElementById('categoryModalTitle').innerText = 'Edit Category';
            document.getElementById('categoryId').value             = c.id;
            document.getElementById('catName').value                = c.category_name;
            document.getElementById('catDesc').value                = c.description || '';
            document.getElementById('catOrder').value               = c.sort_order;
            document.getElementById('catActive').checked            = c.is_active == 1;
            document.getElementById('categoryModal').classList.add('active');
        })
        .catch(() => showToast('Network error loading category.', 'danger'));
}

function submitCategoryForm(e) {
    if (e && e.preventDefault) e.preventDefault();
    const form    = document.getElementById('categoryForm');
    const catName = form.querySelector('[name="category_name"]')?.value ?? '';
    if (!catName.trim()) { showToast('Category name is required.', 'danger'); form.querySelector('[name="category_name"]')?.focus(); return; }
    const btn = document.getElementById('catSaveBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…'; }
    adminFetch('../backend/save_category.php', new FormData(form),
        data => redirectWithToast('dashboard.php?page=supplies', data.message, 'success'),
        errMsg => { showToast(errMsg, 'danger'); if (btn) { btn.disabled = false; btn.innerHTML = 'Save Category'; } }
    );
}

let _delSupId = null;
let _delCatId = null;

function openSupplyDeleteConfirm(id, name) {
    _delSupId = id;
    document.getElementById('delSupName').textContent = `"${name}"`;
    document.getElementById('supplyDeleteConfirm').classList.add('active');
}

function closeSupplyDeleteConfirm() { document.getElementById('supplyDeleteConfirm').classList.remove('active'); _delSupId = null; }
function executeSupplyDelete()       { if (_delSupId) window.location.href = `../backend/delete_supply.php?id=${_delSupId}`; }

function openCatDeleteConfirm(id, name) {
    _delCatId = id;
    document.getElementById('delCatName').textContent = `"${name}"`;
    document.getElementById('catDeleteConfirm').classList.add('active');
}

function closeCatDeleteConfirm() { document.getElementById('catDeleteConfirm').classList.remove('active'); _delCatId = null; }
function executeCatDelete()       { if (_delCatId) window.location.href = `../backend/delete_category.php?id=${_delCatId}`; }

function initSuppliesPageListeners() {
    ['supplyModal', 'categoryModal', 'supplyDeleteConfirm', 'catDeleteConfirm'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function (e) { if (e.target === this) this.classList.remove('active'); });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            ['supplyModal', 'categoryModal', 'supplyDeleteConfirm', 'catDeleteConfirm'].forEach(id => document.getElementById(id)?.classList.remove('active'));
        }
    });
    _supCurrentPage = 1;
    _supCurrentCat  = 'all';
    supApplyFilters();
}
document.addEventListener('DOMContentLoaded', initSuppliesPageListeners);


/* =============================================================================
   7. UPDATES PAGE
   ============================================================================= */

let _updDeleteId = null;

function openUpdDeleteConfirm(id, title) {
    _updDeleteId = id;
    document.getElementById('deleteUpdTitle').textContent = `"${title}"`;
    document.getElementById('updDeleteConfirmModal').classList.add('active');
}

function closeUpdDeleteConfirm() { document.getElementById('updDeleteConfirmModal').classList.remove('active'); _updDeleteId = null; }
function executeUpdDelete()       { if (_updDeleteId) window.location.href = `../backend/delete_update.php?id=${_updDeleteId}`; }

function openAddUpdateModal() {
    document.getElementById('updateModalTitle').innerText = 'New Post';
    document.getElementById('updateForm').reset();
    document.getElementById('updateId').value = '';
    document.getElementById('updImgPreview').innerHTML = '';
    document.getElementById('updExistingImgs').innerHTML = '';
    document.getElementById('updSubmitBtn').disabled = false;
    document.getElementById('updSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Save Post';
    ['updTitleCount', 'updDescCount'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.textContent = el.textContent.replace(/^\d+/, '0'); el.className = 'char-count'; }
    });
    document.getElementById('updateModal').classList.add('active');
}

function closeUpdateModal() { document.getElementById('updateModal').classList.remove('active'); }

function previewUpdImgs(input) {
    const preview = document.getElementById('updImgPreview');
    preview.innerHTML = '';
    if (!input.files || !input.files.length) return;
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;display:inline-block;';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid var(--primary-color);display:block;';
            wrap.appendChild(img);
            preview.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}

function editUpdate(id) {
    fetch(`../backend/get_update.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { showToast('Could not load post.', 'danger'); return; }
            const u = data.data;
            document.getElementById('updateModalTitle').innerText = 'Edit Post';
            document.getElementById('updateId').value             = u.id;
            document.getElementById('updTitle').value             = u.title;
            document.getElementById('updDescription').value       = u.description;
            document.getElementById('updOrder').value             = u.sort_order;
            document.getElementById('updActive').checked          = u.is_active == 1;
            document.getElementById('updSubmitBtn').disabled      = false;
            document.getElementById('updSubmitBtn').innerHTML     = '<i class="fas fa-save"></i> Save Post';
            document.getElementById('updImgPreview').innerHTML    = '';

            const existingWrap = document.getElementById('updExistingImgs');
            existingWrap.innerHTML = '';
            const imgs = u.all_images ?? (u.image_path ? [`${window.UPLOADS_URL}${u.image_path}`] : []);
            if (imgs.length) {
                const label = document.createElement('p');
                label.style.cssText = 'font-size:.8rem;color:var(--text-light);margin:0 0 .4rem;font-weight:600;';
                label.textContent = `Current photos (${imgs.length}):`;
                existingWrap.appendChild(label);
                const row = document.createElement('div');
                row.style.cssText = 'display:flex;flex-wrap:wrap;gap:.5rem;';
                imgs.forEach(src => {
                    const thumb = document.createElement('img');
                    thumb.src = src;
                    thumb.style.cssText = 'width:72px;height:72px;object-fit:cover;border-radius:8px;border:1.5px solid var(--border-color);';
                    row.appendChild(thumb);
                });
                existingWrap.appendChild(row);
            }

            ['updTitle', 'updDescription'].forEach(elId => document.getElementById(elId)?.dispatchEvent(new Event('input')));
            document.getElementById('updateModal').classList.add('active');
        })
        .catch(() => showToast('Network error loading post.', 'danger'));
}

function submitUpdateForm(e) {
    if (e && e.preventDefault) e.preventDefault();
    const form  = document.getElementById('updateForm');
    const title = form.querySelector('[name="title"]')?.value ?? '';
    const desc  = form.querySelector('[name="description"]')?.value ?? '';
    if (!title.trim()) { showToast('Post title is required.', 'danger'); form.querySelector('[name="title"]')?.focus(); return; }
    if (!desc.trim())  { showToast('Description is required.', 'danger'); form.querySelector('[name="description"]')?.focus(); return; }
    const btn = document.getElementById('updSubmitBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…'; }
    adminFetch('../backend/save_update.php', new FormData(form),
        data => redirectWithToast('dashboard.php?page=updates', data.message, 'success'),
        errMsg => { showToast(errMsg, 'danger'); if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Save Post'; } }
    );
}

function bindCharCount(inputId, countId, max) {
    const el  = document.getElementById(inputId);
    const cnt = document.getElementById(countId);
    if (!el || !cnt) return;
    function update() {
        const n = el.value.length;
        cnt.textContent = `${n} / ${max}`;
        cnt.className   = `char-count${n > max * .85 ? ' warn' : ''}`;
    }
    el.addEventListener('input', update);
    update();
}

function initUpdatesPage() {
    bindCharCount('updTitle',       'updTitleCount', 120);
    bindCharCount('updDescription', 'updDescCount',  400);
    document.getElementById('updateModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeUpdateModal(); });
    document.getElementById('updDeleteConfirmModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeUpdDeleteConfirm(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeUpdateModal(); closeUpdDeleteConfirm(); } });
}
document.addEventListener('DOMContentLoaded', initUpdatesPage);


/* =============================================================================
   8. STATS PAGE
   ============================================================================= */

function statsLivePreview(id, card) {
    const val    = card.querySelector('input[name="values[]"]').value   || '0';
    const suffix = card.querySelector('input[name="suffixes[]"]').value || '';
    const label  = card.querySelector('input[name="labels[]"]').value   || '';
    const prev   = document.getElementById(`preview_${id}`);
    if (!prev) return;
    prev.querySelector('.stats-preview-number').textContent = val + suffix;
    prev.querySelector('.stats-preview-label').textContent  = label;
}

function statsTogglePreview(id, active) {
    const prev = document.getElementById(`preview_${id}`);
    const dot  = document.getElementById(`dot_${id}`);
    if (prev) prev.style.display = active ? '' : 'none';
    if (dot)  dot.className = `stat-active-dot${active ? '' : ' stat-inactive-dot'}`;
}

function saveStats(e) {
    e.preventDefault();
    const btn = document.getElementById('statsSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    adminFetch(
        '../backend/save_stats.php',
        new FormData(document.getElementById('statsForm')),
        data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Save All Stats';
            showToast(data.message || 'Stats updated successfully.', 'success');
        },
        errMsg => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Save All Stats';
            showToast(`Failed to save: ${errMsg}`, 'danger');
        }
    );
}


/* =============================================================================
   9. SHARED UTILITIES
   ============================================================================= */

function previewImage(inputId, previewId) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => { if (preview) { preview.src = e.target.result; preview.style.display = 'block'; } };
    reader.readAsDataURL(input.files[0]);
}

function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;
    const filter = input.value.toUpperCase();
    table.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = row.textContent.toUpperCase().includes(filter) ? '' : 'none';
    });
}

function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    const csv = [];
    table.querySelectorAll('tr').forEach(row => {
        const cols   = row.querySelectorAll('td, th');
        const csvRow = Array.from(cols).map(col => `"${col.innerText.replace(/"/g, '""')}"`);
        csv.push(csvRow.join(','));
    });
    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const url  = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url; link.download = filename; link.click();
    window.URL.revokeObjectURL(url);
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    }
});