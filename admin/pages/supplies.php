<?php
$cat_result = $conn->query("SELECT * FROM supply_categories ORDER BY sort_order ASC");
$categories = $cat_result ? $cat_result->fetch_all(MYSQLI_ASSOC) : [];

$supplies_result = $conn->query("
    SELECT s.*, sc.category_name
    FROM supplies s
    LEFT JOIN supply_categories sc ON s.category_id = sc.id
    ORDER BY sc.sort_order ASC, s.sort_order ASC
");
$supplies    = $supplies_result ? $supplies_result->fetch_all(MYSQLI_ASSOC) : [];
$total_items = count($supplies);
displayAlert();
?>

<script>window.UPLOADS_URL = '<?php echo UPLOADS_URL; ?>';</script>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:.75rem;">
    <div>
        <h2 style="margin:0;">
            Manage Supplies
            <span style="font-size:.8rem;font-weight:600;color:var(--text-light);margin-left:.5rem;">(<?php echo $total_items; ?> total)</span>
        </h2>
        <p style="margin:.2rem 0 0; font-size:.83rem; color:var(--text-light);">
            <?php echo $total_items; ?> total supplies across <?php echo count($categories); ?> categories
        </p>
    </div>
    <div style="display:flex; gap:.5rem; flex-wrap:wrap; align-items:center;">
        <div class="sup-search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="supSearchInput" placeholder="Search supplies…" oninput="supApplyFilters()">
        </div>
        <button class="btn-add" style="background:#6A1B9A;" onclick="openAddCategoryModal()">
            <i class="fas fa-tag"></i> Add Category
        </button>
        <button class="btn-add" onclick="openAddSupplyModal()">
            <i class="fas fa-plus"></i> Add Supply
        </button>
    </div>
</div>

<!-- Category tab bar -->
<div class="sup-tabs" id="supTabs">
    <button class="sup-tab active" data-cat="all" onclick="switchSupTab(this, 'all')">
        <i class="fas fa-layer-group"></i> All <span class="cbadge"><?php echo $total_items; ?></span>
    </button>
    <?php foreach ($categories as $cat):
        $cnt = array_reduce($supplies, fn($c,$s) => $c + ($s['category_id'] == $cat['id'] ? 1 : 0), 0);
    ?>
    <button class="sup-tab" data-cat="<?php echo $cat['id']; ?>" onclick="switchSupTab(this, '<?php echo $cat['id']; ?>')">
        <i class="fas fa-tag"></i> <?php echo htmlspecialchars($cat['category_name']); ?>
        <span class="cbadge"><?php echo $cnt; ?></span>
    </button>
    <?php endforeach; ?>
    <button class="sup-tab" data-cat="categories" onclick="switchSupTab(this, 'categories')" style="margin-left:auto;">
        <i class="fas fa-folder"></i> Categories
    </button>
</div>

<!-- Supplies Table -->
<div id="suppliesView">
    <div class="admin-card">
        <?php if (!empty($supplies)): ?>
            <table class="admin-table" id="suppliesTable">
                <thead>
                    <tr><th>Order</th><th>Supply</th><th>Category</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($supplies as $sup): ?>
                    <tr data-cat="<?php echo $sup['category_id']; ?>"
                        data-search="<?php echo strtolower(htmlspecialchars($sup['supply_name'].' '.$sup['description'].' '.$sup['category_name'])); ?>">
                        <td style="font-size:.85rem; text-align:center;"><?php echo $sup['sort_order']; ?></td>
                        <td><span style="font-weight:700; font-size:.9rem;"><?php echo htmlspecialchars($sup['supply_name']); ?></span></td>
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:.35rem;background:#1565C020;color:#1565C0;border:1px solid #1565C040;border-radius:50px;padding:.2rem .65rem;font-size:.76rem;font-weight:800;">
                                <i class="fas fa-tag" style="font-size:.65rem;"></i>
                                <?php echo htmlspecialchars($sup['category_name'] ?? 'Uncategorized'); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background-color:<?php echo $sup['is_active'] ? '#28A745' : '#6C757D'; ?>;">
                                <?php echo $sup['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td><?php echo formatDate($sup['created_at']); ?></td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-edit" onclick="editSupply(<?php echo $sup['id']; ?>)"><i class="fas fa-edit"></i></button>
                                <button class="btn-delete" onclick="openSupplyDeleteConfirm(<?php echo $sup['id']; ?>, '<?php echo addslashes(htmlspecialchars($sup['supply_name'])); ?>')"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="supNoResults" style="display:none;">
                        <td colspan="6" style="text-align:center; color:var(--text-light); padding:2.5rem;">
                            <i class="fas fa-search" style="margin-right:.4rem;"></i> No supplies match your search.
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="adm-pagination" id="supPaginationBar" style="display:none;">
                <div class="adm-pag-info" id="supPagInfo"></div>
                <div class="adm-pag-btns" id="supPagBtns"></div>
            </div>
        <?php else: ?>
            <div style="text-align:center; color:var(--text-light); padding:3rem;">
                <i class="fas fa-boxes" style="font-size:3rem;margin-bottom:1rem;display:block;opacity:.4;"></i>
                No supplies yet. <a href="#" onclick="openAddSupplyModal(); return false;">Add one now</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Categories Table -->
<div id="categoriesView" style="display:none;">
    <div class="admin-card">
        <?php if (!empty($categories)): ?>
            <table class="admin-table">
                <thead>
                    <tr><th>Category</th><th>Description</th><th>Supplies</th><th>Order</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat):
                        $cnt = array_reduce($supplies, fn($c,$s) => $c + ($s['category_id'] == $cat['id'] ? 1 : 0), 0);
                    ?>
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:.65rem;">
                                <div style="width:36px;height:36px;border-radius:8px;background:#1565C0;display:flex;align-items:center;justify-content:center;color:#fff;">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <span style="font-weight:700;"><?php echo htmlspecialchars($cat['category_name']); ?></span>
                            </div>
                        </td>
                        <td style="font-size:.83rem; color:var(--text-light); max-width:240px;">
                            <?php echo !empty($cat['description']) ? htmlspecialchars(substr($cat['description'], 0, 80)).'…' : '<span style="opacity:.4;">—</span>'; ?>
                        </td>
                        <td style="text-align:center;"><span class="badge" style="background-color:var(--primary-color);"><?php echo $cnt; ?></span></td>
                        <td style="font-size:.85rem; text-align:center;"><?php echo $cat['sort_order']; ?></td>
                        <td>
                            <span class="badge" style="background-color:<?php echo $cat['is_active'] ? '#28A745' : '#6C757D'; ?>;">
                                <?php echo $cat['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-edit" onclick="editCategory(<?php echo $cat['id']; ?>)"><i class="fas fa-edit"></i></button>
                                <button class="btn-delete" onclick="openCatDeleteConfirm(<?php echo $cat['id']; ?>, '<?php echo addslashes(htmlspecialchars($cat['category_name'])); ?>')"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align:center; color:var(--text-light); padding:3rem;">
                No categories yet. <a href="#" onclick="openAddCategoryModal(); return false;">Add one now</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Supply Modal -->
<div class="modal-overlay" id="supplyModal">
    <div class="modal-content" style="max-width:520px;max-height:90vh;overflow-y:auto;">
        <div class="modal-header">
            <h2 id="supplyModalTitle">Add New Supply</h2>
            <button class="modal-close" onclick="closeSupplyModal()">&times;</button>
        </div>
        <form id="supplyForm" enctype="multipart/form-data">
            <input type="hidden" id="supplyId" name="supply_id" value="">
            <div class="form-group">
                <label for="supCategory">Category</label>
                <select id="supCategory" name="category_id" class="form-control">
                    <option value="">— Select category —</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="supName">Supply Name</label>
                <input type="text" id="supName" name="supply_name" class="form-control" placeholder="Enter supply name">
            </div>
            <div class="form-group">
                <label for="supOrder">Sort Order</label>
                <input type="number" id="supOrder" name="sort_order" class="form-control" value="0" min="0">
            </div>
            <div class="form-group">
                <label for="supDescription">Description</label>
                <textarea id="supDescription" name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="supImage">Image</label>
                <input type="file" id="supImage" name="supply_image" class="form-control" accept="image/*" onchange="previewSupplyImg(this)">
                <small style="color:var(--text-light);">JPG, PNG, WEBP — max 5 MB</small>
                <div id="supImgPreview" style="margin-top:.6rem;"></div>
            </div>
            <div class="form-group">
                <label><input type="checkbox" id="supActive" name="is_active" value="1" checked> Active (visible on website)</label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary-main" style="background:#6C757D;color:#fff;border:none;" onclick="closeSupplyModal()">Cancel</button>
                <button type="button" class="btn-add" id="supplySubmitBtn" onclick="submitSupplyForm(event)">Save Supply</button>
            </div>
        </form>
    </div>
</div>

<!-- Category Modal -->
<div class="modal-overlay" id="categoryModal">
    <div class="modal-content" style="max-width:480px;">
        <div class="modal-header">
            <h2 id="categoryModalTitle">Add Category</h2>
            <button class="modal-close" onclick="closeCategoryModal()">&times;</button>
        </div>
        <form id="categoryForm">
            <input type="hidden" id="categoryId" name="category_id" value="">
            <div class="form-group">
                <label for="catName">Category Name</label>
                <input type="text" id="catName" name="category_name" class="form-control" placeholder="Enter category name">
            </div>
            <div class="form-group">
                <label for="catDesc">Description</label>
                <textarea id="catDesc" name="description" class="form-control" rows="2"></textarea>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label for="catOrder">Sort Order</label>
                    <input type="number" id="catOrder" name="sort_order" class="form-control" value="0" min="0">
                </div>
                <div class="form-group" style="display:flex; align-items:flex-end; padding-bottom:.5rem;">
                    <label><input type="checkbox" id="catActive" name="is_active" value="1" checked> Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary-main" style="background:#6C757D;color:#fff;border:none;" onclick="closeCategoryModal()">Cancel</button>
                <button type="button" id="catSaveBtn" class="btn-add" onclick="submitCategoryForm(event)">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Supply Delete Confirm -->
<div class="confirm-modal-overlay" id="supplyDeleteConfirm">
    <div class="confirm-modal-box">
        <div class="confirm-modal-header">
            <div class="confirm-modal-icon"><i class="fas fa-trash-alt"></i></div>
            <div><h3>Delete Supply</h3><p>This action cannot be undone</p></div>
        </div>
        <div class="confirm-modal-body"><p>Are you sure you want to delete <strong id="delSupName">this supply</strong>?</p></div>
        <div class="confirm-modal-footer">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeSupplyDeleteConfirm()"><i class="fas fa-times"></i> Cancel</button>
            <button class="confirm-btn confirm-btn-delete" onclick="executeSupplyDelete()"><i class="fas fa-trash-alt"></i> Yes, Delete</button>
        </div>
    </div>
</div>

<!-- Category Delete Confirm -->
<div class="confirm-modal-overlay" id="catDeleteConfirm">
    <div class="confirm-modal-box">
        <div class="confirm-modal-header">
            <div class="confirm-modal-icon"><i class="fas fa-trash-alt"></i></div>
            <div><h3>Delete Category</h3><p>All supplies will also be deleted</p></div>
        </div>
        <div class="confirm-modal-body">
            <p>Delete category <strong id="delCatName">this category</strong>?</p>
            <div class="confirm-warning"><i class="fas fa-exclamation-triangle"></i> All supplies under this category will be permanently removed.</div>
        </div>
        <div class="confirm-modal-footer">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeCatDeleteConfirm()"><i class="fas fa-times"></i> Cancel</button>
            <button class="confirm-btn confirm-btn-delete" onclick="executeCatDelete()"><i class="fas fa-trash-alt"></i> Yes, Delete</button>
        </div>
    </div>
</div>