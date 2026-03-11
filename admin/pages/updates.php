<?php
// Updates Management Page
$updates_result = $conn->query("SELECT * FROM updates ORDER BY sort_order ASC, created_at DESC");
$updates = $updates_result ? $updates_result->fetch_all(MYSQLI_ASSOC) : [];
displayAlert();
?>

<style>
/* ── Preview thumbnail in table ── */
.upd-thumb {
    width: 54px; height: 54px;
    border-radius: 10px; object-fit: cover;
    border: 1.5px solid var(--border-color);
    background: var(--light-bg); display: block; flex-shrink: 0;
}
.upd-thumb-placeholder {
    width: 54px; height: 54px; border-radius: 10px;
    background: var(--light-bg); border: 1.5px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: var(--border-color); font-size: 1.2rem; flex-shrink: 0;
}
.upd-title-cell { display: flex; align-items: center; gap: .75rem; }

/* ── Confirm Modal ── */
.confirm-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
    z-index: 9999; align-items: center; justify-content: center; padding: 1rem;
}
.confirm-modal-overlay.active { display: flex; animation: cfFadeIn .2s ease; }
@keyframes cfFadeIn { from{opacity:0} to{opacity:1} }
.confirm-modal-box {
    background:#fff; border-radius:16px; max-width:420px; width:100%;
    box-shadow:0 24px 70px rgba(0,0,0,.25); overflow:hidden;
    animation:cfSlideUp .28s cubic-bezier(.22,.68,0,1.1);
}
@keyframes cfSlideUp {
    from { transform:translateY(20px) scale(.97); opacity:0; }
    to   { transform:translateY(0) scale(1); opacity:1; }
}
.confirm-modal-header {
    background:linear-gradient(135deg,#DC3545,#C82333);
    padding:1.5rem 1.8rem 1.2rem; display:flex; align-items:center; gap:1rem;
}
.confirm-modal-icon {
    width:48px; height:48px; background:rgba(255,255,255,.18); border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:1.3rem; color:#fff; flex-shrink:0;
}
.confirm-modal-header h3 { color:#fff; margin:0; font-size:1.1rem; font-weight:800; }
.confirm-modal-header p  { color:rgba(255,255,255,.8); margin:3px 0 0; font-size:.83rem; }
.confirm-modal-body { padding:1.6rem 1.8rem; }
.confirm-modal-body > p { color:#374151; font-size:.97rem; line-height:1.7; margin:0; }
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

/* ── Image preview in modal ── */
#updImgPreview img {
    width: 90px; height: 90px; object-fit: cover;
    border-radius: 10px; border: 2px solid var(--primary-color); display: block;
    margin-top: .5rem;
}
.char-count { font-size: .78rem; color: var(--text-light); text-align: right; margin-top: .25rem; }
.char-count.warn { color: #E65100; }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:.75rem;">
    <div>
        <h2 style="margin:0;">Updates &amp; Posts</h2>
        <p style="margin:.2rem 0 0; font-size:.83rem; color:var(--text-light);">
            <?php echo count($updates); ?> post<?php echo count($updates) !== 1 ? 's' : ''; ?> published
        </p>
    </div>
    <button class="btn-add" onclick="openAddUpdateModal()">
        <i class="fas fa-plus"></i> New Post
    </button>
</div>

<div class="admin-card">
    <?php if (!empty($updates)): ?>
        <table class="admin-table" id="updatesTable">
            <thead>
                <tr>
                    <th>Post</th>
                    <th>Description</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($updates as $upd): ?>
                <tr>
                    <td>
                        <div class="upd-title-cell">
                            <?php if (!empty($upd['image_path'])): ?>
                                <img class="upd-thumb"
                                     src="<?php echo UPLOADS_URL . htmlspecialchars($upd['image_path']); ?>"
                                     alt="<?php echo htmlspecialchars($upd['title']); ?>"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="upd-thumb-placeholder" style="display:none;"><i class="fas fa-image"></i></div>
                            <?php else: ?>
                                <div class="upd-thumb-placeholder"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                            <span style="font-weight:700; font-size:.9rem;">
                                <?php echo htmlspecialchars($upd['title']); ?>
                            </span>
                        </div>
                    </td>
                    <td style="font-size:.83rem; color:var(--text-light); max-width:260px;">
                        <?php echo htmlspecialchars(substr($upd['description'], 0, 80)) . (strlen($upd['description']) > 80 ? '…' : ''); ?>
                    </td>
                    <td style="text-align:center; font-size:.85rem;"><?php echo $upd['sort_order']; ?></td>
                    <td>
                        <span class="badge" style="background-color:<?php echo $upd['is_active'] ? '#28A745' : '#6C757D'; ?>;">
                            <?php echo $upd['is_active'] ? 'Published' : 'Draft'; ?>
                        </span>
                    </td>
                    <td style="font-size:.82rem; white-space:nowrap;"><?php echo formatDate($upd['created_at']); ?></td>
                    <td>
                        <div class="admin-actions">
                            <button class="btn-edit" onclick="editUpdate(<?php echo $upd['id']; ?>)" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-delete" onclick="openUpdDeleteConfirm(<?php echo $upd['id']; ?>, '<?php echo addslashes(htmlspecialchars($upd['title'])); ?>')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align:center; color:var(--text-light); padding:3.5rem 1rem;">
            <i class="fas fa-newspaper" style="font-size:3rem; margin-bottom:1rem; display:block; opacity:.35;"></i>
            No posts yet. <a href="#" onclick="openAddUpdateModal(); return false;" style="color:var(--primary-color); font-weight:600;">Create your first post</a>
        </div>
    <?php endif; ?>
</div>

<!-- ══ Add / Edit Update Modal ══ -->
<div class="modal-overlay" id="updateModal">
    <div class="modal-content" style="max-width:560px; max-height:90vh; overflow-y:auto;">
        <div class="modal-header">
            <h2 id="updateModalTitle">New Post</h2>
            <button class="modal-close" onclick="closeUpdateModal()">&times;</button>
        </div>
        <form id="updateForm" enctype="multipart/form-data" onsubmit="submitUpdateForm(event)">
            <input type="hidden" id="updateId" name="update_id" value="">

            <div class="form-group">
                <label for="updTitle">Post Title <span style="color:#DC3545;">*</span></label>
                <input type="text" id="updTitle" name="title" class="form-control"
                       maxlength="120" required placeholder="e.g. Project Completion — SM Batangas">
                <div class="char-count" id="updTitleCount">0 / 120</div>
            </div>

            <div class="form-group">
                <label for="updDescription">Short Description <span style="color:#DC3545;">*</span></label>
                <textarea id="updDescription" name="description" class="form-control"
                          rows="4" maxlength="400" required
                          placeholder="A brief summary shown on the website…"></textarea>
                <div class="char-count" id="updDescCount">0 / 400</div>
            </div>

            <div class="form-group">
                <label for="updImage">Photos <span style="color:var(--text-light);font-weight:400;">(select multiple)</span></label>
                <input type="file" id="updImage" name="update_images[]" class="form-control"
                       accept="image/*" multiple onchange="previewUpdImgs(this)">
                <small style="color:var(--text-light);">JPG, PNG, WEBP — max 5MB each. First photo becomes the cover. You can add more photos when editing.</small>
                <!-- New image previews -->
                <div id="updImgPreview" style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.6rem;"></div>
                <!-- Existing images (shown in edit mode) -->
                <div id="updExistingImgs" style="margin-top:.8rem;"></div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label for="updOrder">Sort Order</label>
                    <input type="number" id="updOrder" name="sort_order" class="form-control" value="0" min="0">
                </div>
                <div class="form-group" style="display:flex; align-items:flex-end; padding-bottom:.5rem;">
                    <label style="display:flex; align-items:center; gap:.5rem; cursor:pointer; margin:0;">
                        <input type="checkbox" id="updActive" name="is_active" value="1" checked>
                        <span>Published (visible on website)</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary-main"
                        style="background:#6C757D; color:#fff; border:none; padding:.6rem 1.2rem; border-radius:6px; cursor:pointer;"
                        onclick="closeUpdateModal()">Cancel</button>
                <button type="submit" class="btn-add" id="updSubmitBtn">
                    <i class="fas fa-save"></i> Save Post
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══ Delete Confirm Modal ══ -->
<div class="confirm-modal-overlay" id="updDeleteConfirmModal">
    <div class="confirm-modal-box">
        <div class="confirm-modal-header">
            <div class="confirm-modal-icon"><i class="fas fa-trash-alt"></i></div>
            <div>
                <h3>Delete Post</h3>
                <p>This action cannot be undone</p>
            </div>
        </div>
        <div class="confirm-modal-body">
            <p>Are you sure you want to delete <strong id="deleteUpdTitle">this post</strong>?</p>
        </div>
        <div class="confirm-modal-footer">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeUpdDeleteConfirm()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="confirm-btn confirm-btn-delete" onclick="executeUpdDelete()">
                <i class="fas fa-trash-alt"></i> Yes, Delete
            </button>
        </div>
    </div>
</div>

<script>
var _updDeleteId = null;

/* ── Char counters ── */
function bindCharCount(inputId, countId, max) {
    var el  = document.getElementById(inputId);
    var cnt = document.getElementById(countId);
    if (!el || !cnt) return;
    function upd() {
        var n = el.value.length;
        cnt.textContent = n + ' / ' + max;
        cnt.className   = 'char-count' + (n > max * 0.85 ? ' warn' : '');
    }
    el.addEventListener('input', upd);
    upd();
}
bindCharCount('updTitle',       'updTitleCount', 120);
bindCharCount('updDescription', 'updDescCount',  400);

/* ── Multi-image preview ── */
function previewUpdImgs(input) {
    var preview = document.getElementById('updImgPreview');
    preview.innerHTML = '';
    if (!input.files || !input.files.length) return;
    Array.from(input.files).forEach(function (file) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;display:inline-block;';
            var img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid var(--primary-color);display:block;';
            wrap.appendChild(img);
            preview.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}

/* ── Open / close modal ── */
function openAddUpdateModal() {
    document.getElementById('updateModalTitle').innerText = 'New Post';
    document.getElementById('updateForm').reset();
    document.getElementById('updateId').value = '';
    document.getElementById('updImgPreview').innerHTML = '';
    document.getElementById('updExistingImgs').innerHTML = '';
    document.getElementById('updSubmitBtn').disabled = false;
    document.getElementById('updSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Save Post';
    /* reset char counters */
    ['updTitleCount', 'updDescCount'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) { el.textContent = el.textContent.replace(/^\d+/, '0'); el.className = 'char-count'; }
    });
    document.getElementById('updateModal').classList.add('active');
}
function closeUpdateModal() {
    document.getElementById('updateModal').classList.remove('active');
}

/* ── Edit: load existing data ── */
function editUpdate(id) {
    fetch('../backend/get_update.php?id=' + id)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.success) { alert('Could not load post.'); return; }
            var u = data.data;
            document.getElementById('updateModalTitle').innerText = 'Edit Post';
            document.getElementById('updateId').value            = u.id;
            document.getElementById('updTitle').value            = u.title;
            document.getElementById('updDescription').value      = u.description;
            document.getElementById('updOrder').value            = u.sort_order;
            document.getElementById('updActive').checked         = u.is_active == 1;
            document.getElementById('updSubmitBtn').disabled     = false;
            document.getElementById('updSubmitBtn').innerHTML    = '<i class="fas fa-save"></i> Save Post';
            document.getElementById('updImgPreview').innerHTML   = '';

            /* Show existing images */
            var existingWrap = document.getElementById('updExistingImgs');
            existingWrap.innerHTML = '';
            var imgs = u.all_images || (u.image_path ? ['<?php echo UPLOADS_URL; ?>' + u.image_path] : []);
            if (imgs.length) {
                var label = document.createElement('p');
                label.style.cssText = 'font-size:.8rem;color:var(--text-light);margin:0 0 .4rem;font-weight:600;';
                label.textContent = 'Current photos (' + imgs.length + '):';
                existingWrap.appendChild(label);
                var row = document.createElement('div');
                row.style.cssText = 'display:flex;flex-wrap:wrap;gap:.5rem;';
                imgs.forEach(function (src) {
                    var thumb = document.createElement('img');
                    thumb.src = src;
                    thumb.style.cssText = 'width:72px;height:72px;object-fit:cover;border-radius:8px;border:1.5px solid var(--border-color);';
                    row.appendChild(thumb);
                });
                existingWrap.appendChild(row);
                var hint = document.createElement('p');
                hint.style.cssText = 'font-size:.75rem;color:var(--text-light);margin:.4rem 0 0;';
                hint.textContent = 'New uploads will be added alongside existing photos.';
                existingWrap.appendChild(hint);
            }

            /* trigger char counters */
            ['updTitle', 'updDescription'].forEach(function (id) {
                document.getElementById(id).dispatchEvent(new Event('input'));
            });

            document.getElementById('updateModal').classList.add('active');
        });
}

/* ── Submit ── */
function submitUpdateForm(e) {
    e.preventDefault();
    var btn = document.getElementById('updSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    fetch('../backend/save_update.php', {
        method: 'POST',
        body: new FormData(document.getElementById('updateForm'))
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            window.location.href = 'dashboard.php?page=updates';
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Save Post';
        }
    })
    .catch(function () {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save Post';
    });
}

/* ── Delete confirm ── */
function openUpdDeleteConfirm(id, title) {
    _updDeleteId = id;
    document.getElementById('deleteUpdTitle').textContent = '"' + title + '"';
    document.getElementById('updDeleteConfirmModal').classList.add('active');
}
function closeUpdDeleteConfirm() {
    document.getElementById('updDeleteConfirmModal').classList.remove('active');
    _updDeleteId = null;
}
function executeUpdDelete() {
    if (_updDeleteId) window.location.href = '../backend/delete_update.php?id=' + _updDeleteId;
}

/* ── Close on outside click / ESC ── */
document.getElementById('updateModal').addEventListener('click', function (e) {
    if (e.target === this) closeUpdateModal();
});
document.getElementById('updDeleteConfirmModal').addEventListener('click', function (e) {
    if (e.target === this) closeUpdDeleteConfirm();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeUpdateModal(); closeUpdDeleteConfirm(); }
});
</script>