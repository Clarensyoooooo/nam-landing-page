<?php
// Supplies Management Page
// Fetch all categories (for filter tabs + form dropdown)
$cat_result = $conn->query("SELECT * FROM supply_categories ORDER BY sort_order ASC");
$categories = $cat_result ? $cat_result->fetch_all(MYSQLI_ASSOC) : [];

// Fetch all supplies with their category name
$supplies_result = $conn->query("
    SELECT s.*, sc.category_name, sc.color_hex, sc.icon_class
    FROM supplies s
    LEFT JOIN supply_categories sc ON s.category_id = sc.id
    ORDER BY sc.sort_order ASC, s.sort_order ASC
");
$supplies = $supplies_result ? $supplies_result->fetch_all(MYSQLI_ASSOC) : [];

displayAlert();
?>

<style>
/* ── Tab bar ── */
.sup-tabs {
    display: flex; gap: .5rem; flex-wrap: wrap;
    margin-bottom: 1.5rem;
}
.sup-tab {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .45rem 1rem; border-radius: 50px; font-size: .82rem;
    font-weight: 700; border: 1.5px solid var(--border-color);
    background: #fff; color: var(--text-light); cursor: pointer;
    transition: all .2s; font-family: inherit; letter-spacing: .02em;
}
.sup-tab.active, .sup-tab:hover {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: #fff;
}
.sup-tab .count-badge {
    background: rgba(255,255,255,.25); color: #fff;
    border-radius: 50px; padding: 0 .4rem; font-size: .7rem; font-weight: 800;
}
.sup-tab:not(.active) .count-badge { background: var(--light-bg); color: var(--text-light); }

/* ── Supplies grid ── */
.sup-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.2rem;
    margin-top: .5rem;
}
.sup-card {
    background: #fff; border: 1.5px solid var(--border-color);
    border-radius: 12px; overflow: hidden;
    transition: transform .25s, box-shadow .25s, border-color .25s;
    display: flex; flex-direction: column;
}
.sup-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,.1);
    border-color: var(--primary-color);
}
.sup-card-img {
    height: 130px; background: var(--light-bg);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; position: relative;
}
.sup-card-img img { width:100%; height:100%; object-fit:cover; }
.sup-card-img .no-img {
    font-size: 2.2rem; color: var(--border-color);
}
.sup-cat-chip {
    position: absolute; bottom: 8px; left: 8px;
    font-size: .65rem; font-weight: 800; letter-spacing: .06em;
    text-transform: uppercase; padding: .2rem .55rem;
    border-radius: 50px; color: #fff;
}
.sup-card-body { padding: .9rem 1rem; flex: 1; display: flex; flex-direction: column; }
.sup-card-name { font-size: .9rem; font-weight: 800; color: var(--text-dark); margin-bottom: .2rem; }
.sup-card-unit { font-size: .73rem; color: var(--text-light); font-weight: 600; }
.sup-card-desc { font-size: .78rem; color: var(--text-light); line-height: 1.55; margin: .45rem 0 0; flex: 1; }
.sup-card-footer {
    padding: .6rem 1rem; border-top: 1px solid var(--light-bg);
    display: flex; gap: .4rem; justify-content: flex-end; background: #FAFBFF;
}
.sup-inactive { opacity: .55; }

/* ── Category cards (in category tab) ── */
.cat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem; margin-top: .5rem;
}
.cat-card {
    background: #fff; border: 1.5px solid var(--border-color);
    border-radius: 12px; padding: 1.2rem 1rem;
    display: flex; flex-direction: column; gap: .5rem;
    transition: transform .25s, box-shadow .25s, border-color .25s;
}
.cat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.09);
    border-color: var(--primary-color);
}
.cat-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; color: #fff; margin-bottom: .3rem; flex-shrink: 0;
}
.cat-card h4 { font-size: .92rem; font-weight: 800; margin: 0; color: var(--text-dark); }
.cat-card p  { font-size: .76rem; color: var(--text-light); margin: 0; line-height: 1.5; }
.cat-card-footer {
    margin-top: auto; display: flex; gap: .4rem;
    justify-content: flex-end; padding-top: .6rem;
    border-top: 1px solid var(--light-bg);
}

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

/* ── Search bar ── */
.sup-search-wrap {
    display:flex; align-items:center; gap:.5rem;
    background:#fff; border:1.5px solid var(--border-color);
    border-radius:8px; padding:.4rem .9rem; max-width:240px;
    transition:border-color .2s, box-shadow .2s;
}
.sup-search-wrap:focus-within {
    border-color:var(--primary-color);
    box-shadow:0 0 0 3px rgba(21,101,192,.1);
}
.sup-search-wrap i  { color:#9CA3AF; font-size:.85rem; }
.sup-search-wrap input {
    border:none; outline:none; font-size:.84rem;
    color:#374151; width:100%; font-family:inherit; background:transparent;
}

/* top toolbar */
.sup-toolbar {
    display:flex; justify-content:space-between; align-items:center;
    flex-wrap:wrap; gap:.75rem; margin-bottom:1.4rem;
}
</style>

<!-- ══ Page toolbar ══ -->
<div class="sup-toolbar">
    <div>
        <h2 style="margin:0;">Supplies</h2>
        <p style="margin:0; font-size:.83rem; color:var(--text-light);">Manage products and supply categories</p>
    </div>
    <div style="display:flex; gap:.5rem; flex-wrap:wrap; align-items:center;">
        <div class="sup-search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="supSearchInput" placeholder="Search supplies…" oninput="filterSupplies(this.value)">
        </div>
        <button class="btn-add" style="background:#6A1B9A;" onclick="openAddCategoryModal()">
            <i class="fas fa-tag"></i> Add Category
        </button>
        <button class="btn-add" onclick="openAddSupplyModal()">
            <i class="fas fa-plus"></i> Add Supply
        </button>
    </div>
</div>

<!-- ══ Tab bar ══ -->
<div class="sup-tabs" id="supTabs">
    <button class="sup-tab active" data-cat="all" onclick="switchTab(this,'all')">
        <i class="fas fa-layer-group"></i> All
        <span class="count-badge"><?php echo count($supplies); ?></span>
    </button>
    <?php foreach ($categories as $cat):
        $cnt = array_reduce($supplies, function($c,$s) use ($cat){ return $c + ($s['category_id']==$cat['id']?1:0); }, 0);
    ?>
    <button class="sup-tab" data-cat="<?php echo $cat['id']; ?>" onclick="switchTab(this,'<?php echo $cat['id']; ?>')">
        <i class="<?php echo htmlspecialchars($cat['icon_class']); ?>"></i>
        <?php echo htmlspecialchars($cat['category_name']); ?>
        <span class="count-badge"><?php echo $cnt; ?></span>
    </button>
    <?php endforeach; ?>
    <button class="sup-tab" data-cat="categories" onclick="switchTab(this,'categories')" style="margin-left:auto;">
        <i class="fas fa-folder"></i> Manage Categories
    </button>
</div>

<!-- ══ Supplies grid ══ -->
<div id="suppliesView">
    <?php if (!empty($supplies)): ?>
        <div class="sup-grid" id="suppliesGrid">
            <?php foreach ($supplies as $sup): ?>
            <div class="sup-card <?php echo !$sup['is_active'] ? 'sup-inactive' : ''; ?>"
                 data-cat="<?php echo $sup['category_id']; ?>"
                 data-search="<?php echo strtolower(htmlspecialchars($sup['supply_name'].' '.$sup['description'].' '.$sup['category_name'])); ?>">
                <div class="sup-card-img">
                    <?php if (!empty($sup['image_path'])): ?>
                        <img src="<?php echo UPLOADS_URL . htmlspecialchars($sup['image_path']); ?>"
                             alt="<?php echo htmlspecialchars($sup['supply_name']); ?>">
                    <?php else: ?>
                        <div class="no-img"><i class="<?php echo htmlspecialchars($sup['icon_class'] ?? 'fas fa-boxes'); ?>"></i></div>
                    <?php endif; ?>
                    <span class="sup-cat-chip" style="background:<?php echo htmlspecialchars($sup['color_hex'] ?? '#1565C0'); ?>;">
                        <?php echo htmlspecialchars($sup['category_name'] ?? 'Uncategorized'); ?>
                    </span>
                    <?php if (!$sup['is_active']): ?>
                        <span style="position:absolute;top:8px;right:8px;background:#6C757D;color:#fff;font-size:.6rem;font-weight:800;padding:.15rem .45rem;border-radius:4px;letter-spacing:.05em;">INACTIVE</span>
                    <?php endif; ?>
                </div>
                <div class="sup-card-body">
                    <div class="sup-card-name"><?php echo htmlspecialchars($sup['supply_name']); ?></div>
                    <?php if (!empty($sup['unit'])): ?>
                        <div class="sup-card-unit"><i class="fas fa-ruler-combined" style="font-size:.65rem;margin-right:.2rem;"></i>Unit: <?php echo htmlspecialchars($sup['unit']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($sup['description'])): ?>
                        <div class="sup-card-desc"><?php echo htmlspecialchars(substr($sup['description'], 0, 90)) . (strlen($sup['description'])>90?'…':''); ?></div>
                    <?php endif; ?>
                </div>
                <div class="sup-card-footer">
                    <button class="btn-edit" onclick="editSupply(<?php echo $sup['id']; ?>)" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-delete" onclick="openSupplyDeleteConfirm(<?php echo $sup['id']; ?>,'<?php echo addslashes(htmlspecialchars($sup['supply_name'])); ?>')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="supNoResults" style="display:none; text-align:center; color:var(--text-light); padding:3rem;">
            <i class="fas fa-search" style="font-size:2rem; margin-bottom:.75rem; display:block;"></i>
            No supplies match your search.
        </div>
    <?php else: ?>
        <div style="text-align:center; color:var(--text-light); padding:3rem;">
            <i class="fas fa-boxes" style="font-size:3rem; margin-bottom:1rem; display:block; opacity:.4;"></i>
            No supplies yet. <a href="#" onclick="openAddSupplyModal();return false;">Add one now</a>
        </div>
    <?php endif; ?>
</div>

<!-- ══ Categories management view ══ -->
<div id="categoriesView" style="display:none;">
    <?php if (!empty($categories)): ?>
        <div class="cat-grid">
            <?php foreach ($categories as $cat):
                $cnt = array_reduce($supplies, function($c,$s) use ($cat){ return $c + ($s['category_id']==$cat['id']?1:0); }, 0);
            ?>
            <div class="cat-card">
                <div class="cat-icon" style="background:<?php echo htmlspecialchars($cat['color_hex']); ?>;">
                    <i class="<?php echo htmlspecialchars($cat['icon_class']); ?>"></i>
                </div>
                <h4><?php echo htmlspecialchars($cat['category_name']); ?></h4>
                <?php if (!empty($cat['description'])): ?>
                    <p><?php echo htmlspecialchars(substr($cat['description'],0,80)); ?></p>
                <?php endif; ?>
                <small style="color:var(--text-light); font-size:.73rem; font-weight:600;">
                    <i class="fas fa-boxes"></i> <?php echo $cnt; ?> supplies
                    &nbsp;|&nbsp;
                    <span style="color:<?php echo $cat['is_active'] ? '#28A745' : '#6C757D'; ?>;">
                        <?php echo $cat['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </small>
                <div class="cat-card-footer">
                    <button class="btn-edit" onclick="editCategory(<?php echo $cat['id']; ?>)" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn-delete" onclick="openCatDeleteConfirm(<?php echo $cat['id']; ?>,'<?php echo addslashes(htmlspecialchars($cat['category_name'])); ?>')" title="Delete"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align:center; color:var(--text-light); padding:3rem;">
            <i class="fas fa-folder-open" style="font-size:3rem; margin-bottom:1rem; display:block; opacity:.4;"></i>
            No categories yet. <a href="#" onclick="openAddCategoryModal();return false;">Add one now</a>
        </div>
    <?php endif; ?>
</div>

<!-- ══ ADD / EDIT SUPPLY MODAL ══ -->
<div class="modal-overlay" id="supplyModal">
    <div class="modal-content" style="max-width:520px; max-height:90vh; overflow-y:auto;">
        <div class="modal-header">
            <h2 id="supplyModalTitle">Add New Supply</h2>
            <button class="modal-close" onclick="closeSupplyModal()">&times;</button>
        </div>
        <form id="supplyForm" enctype="multipart/form-data" onsubmit="submitSupplyForm(event)">
            <input type="hidden" id="supplyId" name="supply_id" value="">

            <div class="form-group">
                <label for="supCategory">Category <span style="color:#DC3545;">*</span></label>
                <select id="supCategory" name="category_id" class="form-control" required>
                    <option value="">— Select category —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="supName">Supply Name <span style="color:#DC3545;">*</span></label>
                <input type="text" id="supName" name="supply_name" class="form-control" required placeholder="e.g. Portland Cement 40kg">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label for="supUnit">Unit</label>
                    <input type="text" id="supUnit" name="unit" class="form-control" placeholder="pcs, kg, m, box…">
                </div>
                <div class="form-group">
                    <label for="supOrder">Sort Order</label>
                    <input type="number" id="supOrder" name="sort_order" class="form-control" value="0" min="0">
                </div>
            </div>

            <div class="form-group">
                <label for="supDescription">Description</label>
                <textarea id="supDescription" name="description" class="form-control" rows="3" placeholder="Brief product description…"></textarea>
            </div>

            <div class="form-group">
                <label for="supImage">Image</label>
                <input type="file" id="supImage" name="supply_image" class="form-control" accept="image/*" onchange="previewSupplyImg(this)">
                <small style="color:var(--text-light);">JPG, PNG, WEBP — max 5MB</small>
                <div id="supImgPreview" style="margin-top:.6rem;"></div>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="supActive" name="is_active" value="1" checked> Active (visible on website)
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary-main" style="background:#6C757D;color:#fff;border:none;" onclick="closeSupplyModal()">Cancel</button>
                <button type="submit" class="btn-add">Save Supply</button>
            </div>
        </form>
    </div>
</div>

<!-- ══ ADD / EDIT CATEGORY MODAL ══ -->
<div class="modal-overlay" id="categoryModal">
    <div class="modal-content" style="max-width:480px;">
        <div class="modal-header">
            <h2 id="categoryModalTitle">Add Category</h2>
            <button class="modal-close" onclick="closeCategoryModal()">&times;</button>
        </div>
        <form id="categoryForm" onsubmit="submitCategoryForm(event)">
            <input type="hidden" id="categoryId" name="category_id" value="">

            <div class="form-group">
                <label for="catName">Category Name <span style="color:#DC3545;">*</span></label>
                <input type="text" id="catName" name="category_name" class="form-control" required placeholder="e.g. Electrical Supplies">
            </div>

            <div class="form-group">
                <label for="catDesc">Description</label>
                <textarea id="catDesc" name="description" class="form-control" rows="2" placeholder="Brief category description…"></textarea>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label for="catIcon">Font Awesome Icon Class</label>
                    <input type="text" id="catIcon" name="icon_class" class="form-control" value="fas fa-boxes" placeholder="fas fa-boxes">
                    <small style="color:var(--text-light);">Browse at fontawesome.com</small>
                </div>
                <div class="form-group">
                    <label for="catColor">Accent Color</label>
                    <div style="display:flex; gap:.5rem; align-items:center;">
                        <input type="color" id="catColor" name="color_hex" value="#1565C0" style="width:44px;height:38px;padding:2px;border:1.5px solid var(--border-color);border-radius:6px;cursor:pointer;">
                        <input type="text" id="catColorText" class="form-control" value="#1565C0" maxlength="7" placeholder="#1565C0"
                               oninput="document.getElementById('catColor').value=this.value"
                               style="flex:1;">
                    </div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label for="catOrder">Sort Order</label>
                    <input type="number" id="catOrder" name="sort_order" class="form-control" value="0" min="0">
                </div>
                <div class="form-group" style="display:flex; align-items:flex-end; padding-bottom:.5rem;">
                    <label>
                        <input type="checkbox" id="catActive" name="is_active" value="1" checked> Active
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary-main" style="background:#6C757D;color:#fff;border:none;" onclick="closeCategoryModal()">Cancel</button>
                <button type="submit" class="btn-add">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- ══ DELETE CONFIRM — Supply ══ -->
<div class="confirm-modal-overlay" id="supplyDeleteConfirm">
    <div class="confirm-modal-box">
        <div class="confirm-modal-header">
            <div class="confirm-modal-icon"><i class="fas fa-trash-alt"></i></div>
            <div><h3>Delete Supply</h3><p>This action cannot be undone</p></div>
        </div>
        <div class="confirm-modal-body">
            <p>Are you sure you want to delete <strong id="delSupName">this supply</strong>?</p>
        </div>
        <div class="confirm-modal-footer">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeSupplyDeleteConfirm()"><i class="fas fa-times"></i> Cancel</button>
            <button class="confirm-btn confirm-btn-delete" onclick="executeSupplyDelete()"><i class="fas fa-trash-alt"></i> Yes, Delete</button>
        </div>
    </div>
</div>

<!-- ══ DELETE CONFIRM — Category ══ -->
<div class="confirm-modal-overlay" id="catDeleteConfirm">
    <div class="confirm-modal-box">
        <div class="confirm-modal-header">
            <div class="confirm-modal-icon"><i class="fas fa-trash-alt"></i></div>
            <div><h3>Delete Category</h3><p>All supplies in this category will also be deleted</p></div>
        </div>
        <div class="confirm-modal-body">
            <p>Delete category <strong id="delCatName">this category</strong>?</p>
            <div style="margin-top:.8rem; background:#FEF3C7; border:1px solid #FDE68A; border-radius:8px; padding:.65rem 1rem; font-size:.84rem; color:#92400E; font-weight:600; display:flex; gap:.5rem; align-items:center;">
                <i class="fas fa-exclamation-triangle" style="color:#D97706;"></i>
                All supplies under this category will be permanently removed.
            </div>
        </div>
        <div class="confirm-modal-footer">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeCatDeleteConfirm()"><i class="fas fa-times"></i> Cancel</button>
            <button class="confirm-btn confirm-btn-delete" onclick="executeCatDelete()"><i class="fas fa-trash-alt"></i> Yes, Delete</button>
        </div>
    </div>
</div>

<script>
/* ════════════════════════
   TABS
════════════════════════ */
function switchTab(btn, cat) {
    document.querySelectorAll('.sup-tab').forEach(function(t){ t.classList.remove('active'); });
    btn.classList.add('active');

    var supView = document.getElementById('suppliesView');
    var catView = document.getElementById('categoriesView');

    if (cat === 'categories') {
        supView.style.display = 'none';
        catView.style.display = '';
        return;
    }
    catView.style.display = 'none';
    supView.style.display = '';

    var cards = document.querySelectorAll('#suppliesGrid .sup-card');
    var visible = 0;
    cards.forEach(function(c) {
        var show = (cat === 'all') || (c.getAttribute('data-cat') === String(cat));
        c.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('supNoResults').style.display = visible === 0 ? '' : 'none';
}

/* ════════════════════════
   SEARCH
════════════════════════ */
function filterSupplies(q) {
    q = q.toLowerCase().trim();
    var cards = document.querySelectorAll('#suppliesGrid .sup-card');
    var visible = 0;
    cards.forEach(function(c) {
        var matches = q === '' || (c.getAttribute('data-search') || '').includes(q);
        c.style.display = matches ? '' : 'none';
        if (matches) visible++;
    });
    document.getElementById('supNoResults').style.display = visible === 0 ? '' : 'none';
}

/* ════════════════════════
   SUPPLY MODAL
════════════════════════ */
function openAddSupplyModal() {
    document.getElementById('supplyModalTitle').innerText = 'Add New Supply';
    document.getElementById('supplyForm').reset();
    document.getElementById('supplyId').value = '';
    document.getElementById('supImgPreview').innerHTML = '';
    document.getElementById('supplyModal').classList.add('active');
}
function closeSupplyModal() { document.getElementById('supplyModal').classList.remove('active'); }

function previewSupplyImg(input) {
    var preview = document.getElementById('supImgPreview');
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid var(--primary-color);';
            preview.appendChild(img);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function editSupply(id) {
    fetch('../backend/get_supply.php?id=' + id)
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (!data.success) { alert('Could not load supply.'); return; }
            var s = data.data;
            document.getElementById('supplyModalTitle').innerText = 'Edit Supply';
            document.getElementById('supplyId').value          = s.id;
            document.getElementById('supCategory').value        = s.category_id;
            document.getElementById('supName').value            = s.supply_name;
            document.getElementById('supUnit').value            = s.unit || '';
            document.getElementById('supDescription').value     = s.description || '';
            document.getElementById('supOrder').value           = s.sort_order;
            document.getElementById('supActive').checked        = s.is_active == 1;
            document.getElementById('supImgPreview').innerHTML  = '';
            if (s.image_path) {
                var img = document.createElement('img');
                img.src = '<?php echo UPLOADS_URL; ?>' + s.image_path;
                img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid var(--primary-color);';
                document.getElementById('supImgPreview').appendChild(img);
            }
            document.getElementById('supplyModal').classList.add('active');
        });
}

function submitSupplyForm(e) {
    e.preventDefault();
    var fd = new FormData(document.getElementById('supplyForm'));
    fetch('../backend/save_supply.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (data.success) { window.location.href = 'dashboard.php?page=supplies'; }
            else { alert('Error: ' + data.message); }
        });
}

/* ════════════════════════
   CATEGORY MODAL
════════════════════════ */
function openAddCategoryModal() {
    document.getElementById('categoryModalTitle').innerText = 'Add Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('catColor').value     = '#1565C0';
    document.getElementById('catColorText').value = '#1565C0';
    document.getElementById('categoryModal').classList.add('active');
}
function closeCategoryModal() { document.getElementById('categoryModal').classList.remove('active'); }

// Sync color picker ↔ text input
document.getElementById('catColor').addEventListener('input', function() {
    document.getElementById('catColorText').value = this.value;
});

function editCategory(id) {
    fetch('../backend/get_category.php?id=' + id)
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (!data.success) { alert('Could not load category.'); return; }
            var c = data.data;
            document.getElementById('categoryModalTitle').innerText = 'Edit Category';
            document.getElementById('categoryId').value  = c.id;
            document.getElementById('catName').value     = c.category_name;
            document.getElementById('catDesc').value     = c.description || '';
            document.getElementById('catIcon').value     = c.icon_class  || 'fas fa-boxes';
            document.getElementById('catColor').value    = c.color_hex   || '#1565C0';
            document.getElementById('catColorText').value= c.color_hex   || '#1565C0';
            document.getElementById('catOrder').value    = c.sort_order;
            document.getElementById('catActive').checked = c.is_active == 1;
            document.getElementById('categoryModal').classList.add('active');
        });
}

function submitCategoryForm(e) {
    e.preventDefault();
    var fd = new FormData(document.getElementById('categoryForm'));
    fetch('../backend/save_category.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (data.success) { window.location.href = 'dashboard.php?page=supplies'; }
            else { alert('Error: ' + data.message); }
        });
}

/* ════════════════════════
   DELETE CONFIRMS
════════════════════════ */
var _delSupId = null, _delCatId = null;

function openSupplyDeleteConfirm(id, name) {
    _delSupId = id;
    document.getElementById('delSupName').textContent = '"' + name + '"';
    document.getElementById('supplyDeleteConfirm').classList.add('active');
}
function closeSupplyDeleteConfirm() {
    document.getElementById('supplyDeleteConfirm').classList.remove('active');
    _delSupId = null;
}
function executeSupplyDelete() {
    if (_delSupId) window.location.href = '../backend/delete_supply.php?id=' + _delSupId;
}

function openCatDeleteConfirm(id, name) {
    _delCatId = id;
    document.getElementById('delCatName').textContent = '"' + name + '"';
    document.getElementById('catDeleteConfirm').classList.add('active');
}
function closeCatDeleteConfirm() {
    document.getElementById('catDeleteConfirm').classList.remove('active');
    _delCatId = null;
}
function executeCatDelete() {
    if (_delCatId) window.location.href = '../backend/delete_category.php?id=' + _delCatId;
}

// Close modals on overlay click / Escape
['supplyModal','categoryModal','supplyDeleteConfirm','catDeleteConfirm'].forEach(function(id) {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['supplyModal','categoryModal','supplyDeleteConfirm','catDeleteConfirm'].forEach(function(id) {
            document.getElementById(id).classList.remove('active');
        });
    }
});
</script>