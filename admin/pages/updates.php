<?php
$updates_result = $conn->query("SELECT * FROM updates ORDER BY sort_order ASC, created_at DESC");
$updates        = $updates_result ? $updates_result->fetch_all(MYSQLI_ASSOC) : [];
$total_items    = count($updates);
displayAlert();

$per_page    = 10;
$total_pages = max(1, ceil($total_items / $per_page));
$cur_page    = max(1, min($total_pages, intval($_GET['updates_page'] ?? 1)));
$offset      = ($cur_page - 1) * $per_page;
$page_items  = array_slice($updates, $offset, $per_page);
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:.75rem;">
    <div>
        <h2 style="margin:0;">
            Updates &amp; Posts
            <span style="font-size:.8rem;font-weight:600;color:var(--text-light);margin-left:.5rem;">
                (<?php echo $total_items; ?> total)
            </span>
        </h2>
    </div>
    <button class="btn-add" onclick="openAddUpdateModal()">
        <i class="fas fa-plus"></i> New Post
    </button>
</div>

<div class="admin-card">
    <?php if (!empty($updates)): ?>
        <table class="admin-table" id="updatesTable">
            <thead>
                <tr><th>Order</th><th>Post</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($page_items as $upd): ?>
                <tr>
                    <td style="text-align:center; font-size:.85rem;"><?php echo $upd['sort_order']; ?></td>
                    <td><span style="font-weight:700; font-size:.9rem;"><?php echo htmlspecialchars($upd['title']); ?></span></td>
                    <td>
                        <span class="badge" style="background-color:<?php echo $upd['is_active'] ? '#28A745' : '#6C757D'; ?>;">
                            <?php echo $upd['is_active'] ? 'Published' : 'Draft'; ?>
                        </span>
                    </td>
                    <td><?php echo formatDate($upd['created_at']); ?></td>
                    <td>
                        <div class="admin-actions">
                            <button class="btn-edit" onclick="editUpdate(<?php echo $upd['id']; ?>)"><i class="fas fa-edit"></i></button>
                            <button class="btn-delete" onclick="openUpdDeleteConfirm(<?php echo $upd['id']; ?>, '<?php echo addslashes(htmlspecialchars($upd['title'])); ?>')"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
        <div class="adm-pagination">
            <div class="adm-pag-info">
                Showing <strong><?php echo $offset + 1; ?>–<?php echo min($offset + $per_page, $total_items); ?></strong>
                of <strong><?php echo $total_items; ?></strong> posts
            </div>
            <div class="adm-pag-btns">
                <a href="?page=updates&updates_page=<?php echo max(1, $cur_page - 1); ?>" class="adm-pag-btn <?php echo $cur_page <= 1 ? 'disabled' : ''; ?>"><i class="fas fa-chevron-left"></i></a>
                <?php
                $range = 2; $start = max(1, $cur_page - $range); $end = min($total_pages, $cur_page + $range);
                if ($start > 1) echo '<a href="?page=updates&updates_page=1" class="adm-pag-btn">1</a>';
                if ($start > 2) echo '<span style="padding:0 .25rem;color:var(--text-light);">…</span>';
                for ($p = $start; $p <= $end; $p++) echo '<a href="?page=updates&updates_page='.$p.'" class="adm-pag-btn '.($p === $cur_page ? 'active' : '').'">'.$p.'</a>';
                if ($end < $total_pages - 1) echo '<span style="padding:0 .25rem;color:var(--text-light);">…</span>';
                if ($end < $total_pages) echo '<a href="?page=updates&updates_page='.$total_pages.'" class="adm-pag-btn">'.$total_pages.'</a>';
                ?>
                <a href="?page=updates&updates_page=<?php echo min($total_pages, $cur_page + 1); ?>" class="adm-pag-btn <?php echo $cur_page >= $total_pages ? 'disabled' : ''; ?>"><i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <div style="text-align:center; color:var(--text-light); padding:3.5rem 1rem;">
            <i class="fas fa-newspaper" style="font-size:3rem;margin-bottom:1rem;display:block;opacity:.35;"></i>
            No posts yet. <a href="#" onclick="openAddUpdateModal(); return false;" style="color:var(--primary-color);font-weight:600;">Create your first post</a>
        </div>
    <?php endif; ?>
</div>

<!-- Add / Edit Post Modal -->
<div class="modal-overlay" id="updateModal">
    <div class="modal-content" style="max-width:560px;max-height:90vh;overflow-y:auto;">
        <div class="modal-header">
            <h2 id="updateModalTitle">New Post</h2>
            <button class="modal-close" onclick="closeUpdateModal()">&times;</button>
        </div>
        <form id="updateForm" enctype="multipart/form-data">
            <input type="hidden" id="updateId" name="update_id" value="">

            <div class="form-group">
                <label for="updTitle">Post Title</label>
                <input type="text" id="updTitle" name="title" class="form-control" maxlength="120" placeholder="Enter post title">
                <div class="char-count" id="updTitleCount">0 / 120</div>
            </div>
            <div class="form-group">
                <label for="updDescription">Short Description</label>
                <textarea id="updDescription" name="description" class="form-control" rows="4" maxlength="400" placeholder="Enter description"></textarea>
                <div class="char-count" id="updDescCount">0 / 400</div>
            </div>
            <div class="form-group">
                <label for="updImage">Photos <span style="color:var(--text-light);font-weight:400;">(select multiple)</span></label>
                <input type="file" id="updImage" name="update_images[]" class="form-control" accept="image/*" multiple onchange="previewUpdImgs(this)">
                <small style="color:var(--text-light);">JPG, PNG, WEBP — max 5 MB each.</small>
                <div id="updImgPreview" style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.6rem;"></div>
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
                        <span>Published</span>
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary-main" style="background:#6C757D;color:#fff;border:none;padding:.6rem 1.2rem;border-radius:6px;cursor:pointer;" onclick="closeUpdateModal()">Cancel</button>
                <button type="button" id="updSubmitBtn" class="btn-add" onclick="submitUpdateForm(event)">
                    <i class="fas fa-save"></i> Save Post
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="confirm-modal-overlay" id="updDeleteConfirmModal">
    <div class="confirm-modal-box">
        <div class="confirm-modal-header">
            <div class="confirm-modal-icon"><i class="fas fa-trash-alt"></i></div>
            <div><h3>Delete Post</h3><p>This action cannot be undone</p></div>
        </div>
        <div class="confirm-modal-body">
            <p>Are you sure you want to delete <strong id="deleteUpdTitle">this post</strong>?</p>
        </div>
        <div class="confirm-modal-footer">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeUpdDeleteConfirm()"><i class="fas fa-times"></i> Cancel</button>
            <button class="confirm-btn confirm-btn-delete" onclick="executeUpdDelete()"><i class="fas fa-trash-alt"></i> Yes, Delete</button>
        </div>
    </div>
</div>