<?php
// Services Management Page
$services = getAllServices($conn, false);
displayAlert();

// For each service, get their images
foreach ($services as &$service) {
    $sid = $service['id'];
    $img_result = $conn->query("SELECT * FROM service_images WHERE service_id = $sid ORDER BY sort_order ASC");
    $service['images'] = $img_result ? $img_result->fetch_all(MYSQLI_ASSOC) : [];
}
unset($service);

// ── Fix UPLOADS_URL for admin pages ──────────────────────────────────────────
// BASE_URL is built from PHP_SELF so when loaded inside admin/dashboard.php it
// becomes http://localhost/nam_builders/admin/ — we must strip /admin to get
// the true site root before appending uploads/.
$admin_uploads_url = rtrim(str_replace('/admin', '', rtrim(BASE_URL, '/')), '/') . '/uploads/';
?>

<script>
// Absolute uploads URL — always resolves to /nam_builders/uploads/
var UPLOADS_URL = '<?php echo $admin_uploads_url; ?>';
</script>

<style>
/* ══ Shared Confirm Modal ══ */
.confirm-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
    z-index: 9999; align-items: center; justify-content: center; padding: 1rem;
}
.confirm-modal-overlay.active { display: flex; animation: cfFadeIn .2s ease; }
@keyframes cfFadeIn { from{opacity:0} to{opacity:1} }

.confirm-modal-box {
    background: #fff; border-radius: 16px; max-width: 420px; width: 100%;
    box-shadow: 0 24px 70px rgba(0,0,0,.25); overflow: hidden;
    animation: cfSlideUp .28s cubic-bezier(.22,.68,0,1.1);
}
@keyframes cfSlideUp {
    from { transform: translateY(20px) scale(.97); opacity: 0; }
    to   { transform: translateY(0) scale(1); opacity: 1; }
}
.confirm-modal-header {
    background: linear-gradient(135deg, #DC3545, #C82333);
    padding: 1.5rem 1.8rem 1.2rem;
    display: flex; align-items: center; gap: 1rem;
}
.confirm-modal-icon {
    width: 48px; height: 48px; background: rgba(255,255,255,.18); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: #fff; flex-shrink: 0;
}
.confirm-modal-header h3 { color:#fff; margin:0; font-size:1.1rem; font-weight:800; }
.confirm-modal-header p  { color:rgba(255,255,255,.8); margin:3px 0 0; font-size:.83rem; }
.confirm-modal-body { padding: 1.6rem 1.8rem; }
.confirm-modal-body > p { color:#374151; font-size:.97rem; line-height:1.7; margin:0 0 .5rem; }
.confirm-warning {
    display:flex; align-items:center; gap:.5rem;
    background:#FEF3C7; border:1px solid #FDE68A;
    border-radius:8px; padding:.65rem 1rem;
    font-size:.84rem; color:#92400E; font-weight:600; margin-top:.8rem;
}
.confirm-warning i { color:#D97706; flex-shrink:0; }
.confirm-modal-footer {
    padding:.9rem 1.8rem 1.3rem; display:flex; gap:.65rem; justify-content:flex-end;
    border-top:1px solid #e2e8f0; background:#FAFBFF;
}
.confirm-btn {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.58rem 1.3rem; border-radius:8px;
    font-size:.88rem; font-weight:700; cursor:pointer;
    border:none; transition:all .2s; font-family:inherit;
}
.confirm-btn-cancel { background:#F1F5F9; color:#4A5568; }
.confirm-btn-cancel:hover { background:#E2E8F0; }
.confirm-btn-delete { background:linear-gradient(135deg,#DC3545,#C82333); color:#fff; }
.confirm-btn-delete:hover {
    background:linear-gradient(135deg,#C82333,#A71D2A);
    transform:translateY(-1px); box-shadow:0 6px 18px rgba(220,53,69,.35);
}

/* ══ Existing images inside edit modal ══ */
.existing-imgs-wrap {
    display:flex; gap:10px; flex-wrap:wrap;
    padding:.75rem; background:#F8FAFC;
    border:1.5px dashed #CBD5E1; border-radius:10px;
    min-height:64px; align-items:flex-start;
}
.existing-imgs-empty {
    color:#9CA3AF; font-size:.82rem; font-style:italic;
    display:flex; align-items:center; gap:.4rem;
}
.existing-img-item { position:relative; display:inline-block; }
.existing-img-item img {
    width:80px; height:80px; object-fit:cover;
    border-radius:8px; border:1.5px solid #e2e8f0;
    display:block; background:#f1f5f9;
}
.img-placeholder-box {
    width:80px; height:80px; background:#f1f5f9;
    border-radius:8px; border:1.5px dashed #CBD5E1;
    display:flex; align-items:center; justify-content:center;
    color:#9CA3AF; font-size:1.6rem;
}
.img-del-btn {
    position:absolute; top:-7px; right:-7px;
    background:#DC3545; color:#fff; border:2px solid #fff;
    border-radius:50%; width:22px; height:22px;
    font-size:13px; font-weight:800; cursor:pointer; padding:0;
    transition:background .2s, transform .15s;
    display:flex; align-items:center; justify-content:center; line-height:1;
}
.img-del-btn:hover { background:#A71D2A; transform:scale(1.15); }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>List of Services</h2>
    <button class="btn-add" onclick="openAddServiceModal()">
        <i class="fas fa-plus"></i> Add New Service
    </button>
</div>

<div class="admin-card">
    <?php if (!empty($services)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Service Name</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo $service['sort_order']; ?></td>
                        <td><?php echo sanitize($service['service_name']); ?></td>
                        <td>
                            <span class="badge" style="background-color: <?php echo $service['is_active'] ? '#28A745' : '#6C757D'; ?>;">
                                <?php echo $service['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td><?php echo formatDate($service['created_at']); ?></td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-edit" onclick="editService(<?php echo $service['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-delete" onclick="openSvcDeleteConfirm(<?php echo $service['id']; ?>, '<?php echo addslashes(sanitize($service['service_name'])); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
            <i class="fas fa-inbox"></i> No services yet. <a href="#" onclick="openAddServiceModal(); return false;">Add one now</a>
        </p>
    <?php endif; ?>
</div>

<!-- Add/Edit Service Modal -->
<div class="modal-overlay" id="serviceModal">
    <div class="modal-content" style="max-width: 580px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Service</h2>
            <button class="modal-close" onclick="closeServiceModal()">&times;</button>
        </div>
        <form id="serviceForm" enctype="multipart/form-data" onsubmit="submitServiceForm(event)">
            <input type="hidden" id="serviceId" name="service_id" value="">

            <div class="form-group">
                <label for="serviceName">Service Name</label>
                <input type="text" id="serviceName" name="service_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="serviceDescription">Description</label>
                <textarea id="serviceDescription" name="description" class="form-control" rows="4" required></textarea>
            </div>

            <!-- Existing images (edit mode only) -->
            <div id="existingImagesSection" style="display:none; margin-bottom:1.2rem;">
                <label style="font-weight:600; color:var(--text-dark); display:block; margin-bottom:.5rem;">
                    Current Images
                    <small style="font-weight:400; color:var(--text-light);">(click × to remove)</small>
                </label>
                <div class="existing-imgs-wrap" id="existingImagesGrid"></div>
            </div>

            <div class="form-group">
                <label for="serviceImages">
                    Service Images <small style="color:var(--text-light); font-weight:normal;">(select multiple)</small>
                </label>
                <input type="file" id="serviceImages" name="service_images[]" class="form-control"
                       accept="image/*" multiple onchange="previewNewImages(this)">
                <small style="color:var(--text-light);">Formats: JPG, PNG, GIF, WEBP. Max 5MB each. New uploads are added to existing images.</small>
            </div>

            <div id="newImagesPreview" style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:1rem;"></div>

            <div class="form-group">
                <label for="serviceOrder">Display Order</label>
                <input type="number" id="serviceOrder" name="sort_order" class="form-control" value="0" min="0">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="serviceActive" name="is_active" value="1" checked> Active
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary-main" style="background-color:#6C757D;color:white;border:none;" onclick="closeServiceModal()">Cancel</button>
                <button type="submit" class="btn-add">Save Service</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="confirm-modal-overlay" id="svcDeleteConfirmModal">
    <div class="confirm-modal-box">
        <div class="confirm-modal-header">
            <div class="confirm-modal-icon"><i class="fas fa-trash-alt"></i></div>
            <div>
                <h3>Delete Service</h3>
                <p>This action cannot be undone</p>
            </div>
        </div>
        <div class="confirm-modal-body">
            <p>Are you sure you want to delete <strong id="deleteSvcName">this service</strong> and all its images?</p>
            <div class="confirm-warning">
                <i class="fas fa-exclamation-triangle"></i>
                All associated images will be permanently removed from the server.
            </div>
        </div>
        <div class="confirm-modal-footer">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeSvcDeleteConfirm()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="confirm-btn confirm-btn-delete" onclick="executeSvcDelete()">
                <i class="fas fa-trash-alt"></i> Yes, Delete
            </button>
        </div>
    </div>
</div>

<script>
var _svcDeleteId = null;

function openSvcDeleteConfirm(id, name) {
    _svcDeleteId = id;
    document.getElementById('deleteSvcName').textContent = '"' + name + '"';
    document.getElementById('svcDeleteConfirmModal').classList.add('active');
}
function closeSvcDeleteConfirm() {
    document.getElementById('svcDeleteConfirmModal').classList.remove('active');
    _svcDeleteId = null;
}
function executeSvcDelete() {
    if (_svcDeleteId) window.location.href = '../backend/delete_service.php?id=' + _svcDeleteId;
}
document.getElementById('svcDeleteConfirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeSvcDeleteConfirm();
});

/* ── Add / Edit modal ── */
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
    var preview = document.getElementById('newImagesPreview');
    preview.innerHTML = '';
    Array.from(input.files || []).forEach(function(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;display:inline-block;';
            var img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid var(--primary-color);display:block;';
            var lbl = document.createElement('span');
            lbl.textContent = 'NEW';
            lbl.style.cssText = 'position:absolute;bottom:4px;left:50%;transform:translateX(-50%);background:var(--primary-color);color:#fff;font-size:9px;font-weight:800;padding:1px 6px;border-radius:4px;letter-spacing:.05em;white-space:nowrap;';
            wrap.appendChild(img);
            wrap.appendChild(lbl);
            preview.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}

function editService(id) {
    fetch('../backend/get_service.php?id=' + id)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.success) { showToast('Could not load service.', 'danger'); return; }
            var s = data.data;

            document.getElementById('modalTitle').innerText        = 'Edit Service';
            document.getElementById('serviceId').value             = s.id;
            document.getElementById('serviceName').value           = s.service_name;
            document.getElementById('serviceDescription').value    = s.description;
            document.getElementById('serviceOrder').value          = s.sort_order;
            document.getElementById('serviceActive').checked       = s.is_active == 1;
            document.getElementById('newImagesPreview').innerHTML  = '';

            var section = document.getElementById('existingImagesSection');
            var grid    = document.getElementById('existingImagesGrid');
            grid.innerHTML   = '';
            section.style.display = 'block';

            var images = s.images || [];
            if (images.length > 0) {
                images.forEach(function(img) { grid.appendChild(buildExistingImgItem(img)); });
            } else {
                grid.innerHTML = '<div class="existing-imgs-empty"><i class="fas fa-image"></i> No images uploaded yet.</div>';
            }

            document.getElementById('serviceModal').classList.add('active');
        });
}

function buildExistingImgItem(img) {
    var wrapper = document.createElement('div');
    wrapper.className = 'existing-img-item';
    wrapper.id = 'img-wrapper-' + img.id;

    // UPLOADS_URL is corrected at the top of this file to always point
    // to the site root /uploads/ regardless of which admin page is active.
    var imgEl = document.createElement('img');
    imgEl.src = UPLOADS_URL + img.image_path;
    imgEl.alt = 'Service image';
    imgEl.onerror = function() {
        this.style.display = 'none';
        if (!wrapper.querySelector('.img-placeholder-box')) {
            var ph = document.createElement('div');
            ph.className = 'img-placeholder-box';
            ph.innerHTML = '<i class="fas fa-image"></i>';
            wrapper.insertBefore(ph, wrapper.firstChild);
        }
    };

    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'img-del-btn';
    btn.title = 'Remove image';
    btn.innerHTML = '&times;';
    (function(capturedId, capturedWrapper) {
        btn.onclick = function() { deleteServiceImage(capturedId, capturedWrapper); };
    }(img.id, wrapper));

    wrapper.appendChild(imgEl);
    wrapper.appendChild(btn);
    return wrapper;
}

function deleteServiceImage(imgId, wrapperEl) {
    fetch('../backend/delete_service_img.php?id=' + imgId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                if (wrapperEl && wrapperEl.parentNode) {
                    wrapperEl.remove();
                    var grid = document.getElementById('existingImagesGrid');
                    if (grid && grid.querySelectorAll('.existing-img-item').length === 0) {
                        grid.innerHTML = '<div class="existing-imgs-empty"><i class="fas fa-image"></i> No images uploaded yet.</div>';
                    }
                }
            } else {
                showToast('Failed to delete image: ' + data.message, 'danger');
            }
        })
        .catch(function() { showToast('Network error. Please try again.', 'danger'); });
}

function submitServiceForm(event) {
    event.preventDefault();
    var fd = new FormData(document.getElementById('serviceForm'));
    fetch('../backend/save_service.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) { window.location.href = 'dashboard.php?page=services'; }
            else { showToast(data.message, 'danger'); }
        });
}

document.getElementById('serviceModal').addEventListener('click', function(e) {
    if (e.target === this) closeServiceModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeServiceModal(); closeSvcDeleteConfirm(); }
});
</script>