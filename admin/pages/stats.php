<?php
// Stats Management Page
$stats_result = $conn->query("SELECT * FROM site_stats ORDER BY sort_order ASC");
$all_stats = $stats_result ? $stats_result->fetch_all(MYSQLI_ASSOC) : [];
displayAlert();
?>

<style>
.stats-preview-bar {
    background: var(--primary-color);
    border-radius: 14px;
    padding: 2rem 2.5rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 30px rgba(21,101,192,.3);
}
.stats-preview-item { text-align: center; color: #fff; }
.stats-preview-number {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 2.4rem; font-weight: 800; line-height: 1;
    display: block; color: #fff;
}
.stats-preview-label {
    font-size: .82rem; font-weight: 600;
    letter-spacing: .06em; text-transform: uppercase;
    opacity: .85; margin-top: .3rem; display: block;
}
.stat-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.2rem;
    margin-bottom: 1.5rem;
}
.stat-edit-card {
    background: #fff;
    border: 1.5px solid var(--border-color);
    border-radius: 14px;
    padding: 1.4rem 1.4rem 1.2rem;
    position: relative;
    transition: border-color .22s, box-shadow .22s;
}
.stat-edit-card:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(21,101,192,.1);
}
.stat-edit-card .card-num-badge {
    position: absolute; top: -10px; left: 14px;
    background: var(--primary-color); color: #fff;
    border-radius: 50px; padding: .18rem .65rem;
    font-size: .68rem; font-weight: 800; letter-spacing: .06em;
    text-transform: uppercase;
}
.stat-edit-card .form-label {
    font-size: .78rem; font-weight: 700;
    color: var(--text-light); letter-spacing: .04em;
    text-transform: uppercase; margin-bottom: .3rem; display: block;
}
.stat-edit-card .form-control {
    width: 100%; padding: .55rem .8rem;
    border: 1.5px solid var(--border-color); border-radius: 8px;
    font-size: .93rem; font-family: inherit;
    transition: border-color .22s, box-shadow .22s; background: #fff;
    color: var(--text-dark);
}
.stat-edit-card .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(21,101,192,.12); outline: none;
}
.stat-input-row { display: grid; grid-template-columns: 1fr 80px; gap: .6rem; margin-bottom: .75rem; }
.stat-toggle { display: flex; align-items: center; gap: .5rem; margin-top: .5rem; }
.stat-toggle input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: var(--primary-color); }
.stat-toggle label { font-size: .82rem; font-weight: 600; color: var(--text-light); cursor: pointer; margin: 0; }
.stat-active-dot { width: 8px; height: 8px; border-radius: 50%; background: #28A745; display: inline-block; flex-shrink: 0; }
.stat-inactive-dot { background: #6C757D; }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:.75rem;">
    <div>
        <h2 style="margin:0;">Stats Section</h2>
        <p style="margin:.2rem 0 0; font-size:.83rem; color:var(--text-light);">
            Edit the numbers displayed on the homepage stats bar. Changes are reflected instantly after saving.
        </p>
    </div>
</div>

<div class="stats-preview-bar" id="statsPreviewBar">
    <?php foreach ($all_stats as $st): if (!$st['is_active']) continue; ?>
    <div class="stats-preview-item" id="preview_<?php echo $st['id']; ?>">
        <span class="stats-preview-number"><?php echo $st['value'] . htmlspecialchars($st['suffix']); ?></span>
        <span class="stats-preview-label"><?php echo htmlspecialchars($st['label']); ?></span>
    </div>
    <?php endforeach; ?>
</div>

<form id="statsForm" onsubmit="saveStats(event)">
<div class="stat-cards-grid" id="statCardsGrid">
    <?php foreach ($all_stats as $i => $st): ?>
    <div class="stat-edit-card" id="statCard_<?php echo $st['id']; ?>">
        <span class="card-num-badge"><?php echo str_pad($st['sort_order'], 2, '0', STR_PAD_LEFT); ?></span>
        <input type="hidden" name="ids[]" value="<?php echo $st['id']; ?>">
        <label class="form-label" style="margin-top:.4rem;">Label</label>
        <input type="text" class="form-control" name="labels[]"
               value="<?php echo htmlspecialchars($st['label']); ?>"
               maxlength="80" required
               oninput="livePreview(<?php echo $st['id']; ?>, this.closest('.stat-edit-card'))">
        <div class="stat-input-row" style="margin-top:.7rem;">
            <div>
                <label class="form-label">Value</label>
                <input type="number" class="form-control" name="values[]"
                       value="<?php echo $st['value']; ?>"
                       min="0" max="99999" required
                       oninput="livePreview(<?php echo $st['id']; ?>, this.closest('.stat-edit-card'))">
            </div>
            <div>
                <label class="form-label">Suffix</label>
                <input type="text" class="form-control" name="suffixes[]"
                       value="<?php echo htmlspecialchars($st['suffix']); ?>"
                       maxlength="5" placeholder="e.g. +"
                       oninput="livePreview(<?php echo $st['id']; ?>, this.closest('.stat-edit-card'))">
            </div>
        </div>
        <div class="stat-input-row">
            <div>
                <label class="form-label">Sort Order</label>
                <input type="number" class="form-control" name="orders[]"
                       value="<?php echo $st['sort_order']; ?>" min="0" max="99">
            </div>
        </div>
        <div class="stat-toggle">
            <span class="stat-active-dot <?php echo $st['is_active'] ? '' : 'stat-inactive-dot'; ?>"
                  id="dot_<?php echo $st['id']; ?>"></span>
            <input type="checkbox" id="active_<?php echo $st['id']; ?>"
                   name="actives[<?php echo $st['id']; ?>]" value="1"
                   <?php echo $st['is_active'] ? 'checked' : ''; ?>
                   onchange="togglePreview(<?php echo $st['id']; ?>, this.checked)">
            <label for="active_<?php echo $st['id']; ?>">Show on website</label>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div style="display:flex; gap:.75rem; align-items:center; flex-wrap:wrap;">
    <button type="submit" class="btn-add" id="statsSaveBtn">
        <i class="fas fa-save"></i> Save All Stats
    </button>
</div>
</form>

<script>
function livePreview(id, card) {
    var val    = card.querySelector('input[name="values[]"]').value   || '0';
    var suffix = card.querySelector('input[name="suffixes[]"]').value || '';
    var label  = card.querySelector('input[name="labels[]"]').value   || '';
    var prev   = document.getElementById('preview_' + id);
    if (!prev) return;
    prev.querySelector('.stats-preview-number').textContent = val + suffix;
    prev.querySelector('.stats-preview-label').textContent  = label;
}

function togglePreview(id, active) {
    var prev = document.getElementById('preview_' + id);
    var dot  = document.getElementById('dot_' + id);
    if (prev) prev.style.display = active ? '' : 'none';
    if (dot)  dot.className = 'stat-active-dot' + (active ? '' : ' stat-inactive-dot');
}

function saveStats(e) {
    e.preventDefault();
    var btn = document.getElementById('statsSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    fetch('../backend/save_stats.php', {
        method: 'POST',
        body: new FormData(document.getElementById('statsForm'))
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save All Stats';
        if (data.success) {
            showToast('Stats updated successfully. Changes are now live on the website.', 'success');
        } else {
            showToast('Failed to save: ' + data.message, 'danger');
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save All Stats';
        showToast('Network error. Please try again.', 'danger');
    });
}
</script>