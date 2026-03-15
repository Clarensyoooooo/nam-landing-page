<?php
// Messages Management Page
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($query);
$messages = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
displayAlert();
?>

<style>
/* ── Search bar ── */
.msg-search-wrap {
    display: flex; align-items: center; gap: .6rem;
    background: #fff; border: 1.5px solid var(--border-color);
    border-radius: 8px; padding: .45rem 1rem; max-width: 280px;
    transition: border-color .2s, box-shadow .2s;
}
.msg-search-wrap:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(21,101,192,.1);
}
.msg-search-wrap i { color: #9CA3AF; font-size: .9rem; flex-shrink: 0; }
.msg-search-wrap input {
    border: none; outline: none; font-size: .88rem; color: #374151;
    width: 100%; font-family: inherit; background: transparent;
}

/* ══ Confirm Modal ══ */
.confirm-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
    z-index: 10000; align-items: center; justify-content: center; padding: 1rem;
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

/* ══ Status badges ══ */
.status-badge {
    display: inline-flex; align-items: center; gap: .32rem;
    padding: .25rem .7rem; border-radius: 50px;
    font-size: .72rem; font-weight: 800; white-space: nowrap;
}
.status-badge.unread   { background: #FEF9C3; color: #854D0E; border: 1px solid #FDE68A; }
.status-badge.read     { background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; }
.status-badge.replied  { background: #EFF6FF; color: #1D4ED8; border: 1px solid #93C5FD; }
.status-badge i { font-size: .65rem; }

/* ══ Message Details Modal ══ */
.msg-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
    z-index: 9999; align-items: center; justify-content: center; padding: 1rem;
}
.msg-modal-overlay.active { display: flex; animation: mmFadeIn .25s ease; }
@keyframes mmFadeIn { from{opacity:0} to{opacity:1} }

.msg-modal-box {
    background: #fff; border-radius: 20px; max-width: 680px; width: 100%;
    max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;
    box-shadow: 0 32px 80px rgba(0,0,0,.3);
    animation: mmSlideUp .32s cubic-bezier(.22,.68,0,1.1);
}
@keyframes mmSlideUp {
    from { transform: translateY(28px) scale(.97); opacity: 0; }
    to   { transform: translateY(0) scale(1); opacity: 1; }
}
.mm-header {
    background: linear-gradient(135deg, #0D47A1 0%, #1565C0 60%, #1E88E5 100%);
    padding: 1.5rem 1.8rem 1.3rem; position: relative; flex-shrink: 0;
}
.mm-header-inner { display: flex; align-items: center; gap: 1rem; }
.mm-avatar {
    width: 54px; height: 54px; border-radius: 50%;
    background: rgba(255,255,255,.2); border: 2px solid rgba(255,255,255,.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem; font-weight: 800; color: #fff;
    text-transform: uppercase; flex-shrink: 0;
    font-family: 'Barlow Condensed', sans-serif;
}
.mm-header-text h3 { color: #fff; margin: 0 0 3px; font-size: 1.1rem; font-weight: 800; }
.mm-header-text p  { color: rgba(255,255,255,.75); margin: 0; font-size: .83rem; }
.mm-close-btn {
    position: absolute; top: 1rem; right: 1rem;
    background: rgba(255,255,255,.18); border: none; border-radius: 50%;
    width: 34px; height: 34px; color: #fff; font-size: 1.25rem; line-height: 34px;
    text-align: center; cursor: pointer; transition: background .2s, transform .2s;
}
.mm-close-btn:hover { background: rgba(255,255,255,.32); transform: scale(1.1); }
.mm-meta-row {
    display: flex; gap: .45rem; flex-wrap: wrap;
    padding: .85rem 1.8rem; background: #F8FAFC;
    border-bottom: 1px solid #e2e8f0; flex-shrink: 0;
}
.mm-meta-chip {
    display: inline-flex; align-items: center; gap: 5px;
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 50px; padding: .28rem .8rem;
    font-size: .78rem; color: #374151; font-weight: 600;
}
.mm-meta-chip i { color: #1565C0; font-size: .72rem; }
.mm-meta-chip.svc-chip { background: #FEF9C3; border-color: #FDE68A; color: #92400E; }
.mm-meta-chip.svc-chip i { color: #D97706; }
.mm-status-chip.unread  { background: #FEF9C3; border-color: #FDE68A; color: #854D0E; }
.mm-status-chip.read    { background: #D1FAE5; border-color: #6EE7B7; color: #065F46; }
.mm-status-chip.replied { background: #EFF6FF; border-color: #93C5FD; color: #1D4ED8; }
.mm-body { overflow-y: auto; flex: 1; padding: 1.6rem 1.8rem; }
.mm-field-label {
    font-size: .7rem; font-weight: 800; letter-spacing: .1em;
    text-transform: uppercase; color: #9CA3AF; margin-bottom: .5rem;
}
.mm-message-box {
    background: #F8FAFC; border: 1px solid #e2e8f0;
    border-left: 4px solid #1565C0; border-radius: 0 12px 12px 0;
    padding: 1.1rem 1.3rem; color: #1A202C; font-size: .95rem; line-height: 1.85;
    white-space: pre-line; margin-bottom: 1.6rem;
}
.mm-reply-section { border-top: 1.5px dashed #e2e8f0; padding-top: 1.4rem; }
.mm-reply-header { display: flex; align-items: center; gap: .55rem; margin-bottom: .9rem; }
.mm-reply-header h4 { margin: 0; font-size: .95rem; color: #0A0A0A; font-weight: 700; }
.mm-reply-to-tag {
    font-size: .77rem; color: #6B7280; background: #F1F5F9;
    padding: .2rem .65rem; border-radius: 50px; font-weight: 600;
    max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.mm-reply-textarea {
    width: 100%; min-height: 120px; padding: .85rem 1rem;
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: .92rem; font-family: inherit; color: #1A202C;
    resize: vertical; transition: border-color .2s, box-shadow .2s; line-height: 1.7; background: #fff;
}
.mm-reply-textarea:focus { outline: none; border-color: #1565C0; box-shadow: 0 0 0 3px rgba(21,101,192,.1); }
.mm-reply-alert {
    border-radius: 8px; padding: .6rem 1rem; font-size: .84rem;
    margin-bottom: 1rem; display: none; align-items: center; gap: .5rem; font-weight: 600;
}
.mm-reply-alert.show    { display: flex; }
.mm-reply-alert.success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
.mm-reply-alert.error   { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
.mm-footer {
    padding: .9rem 1.8rem 1.2rem; display: flex; gap: .65rem; justify-content: flex-end;
    border-top: 1px solid #e2e8f0; flex-shrink: 0; background: #FAFBFF;
}
.mm-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .58rem 1.3rem; border-radius: 8px;
    font-size: .88rem; font-weight: 700; cursor: pointer;
    border: none; transition: all .2s; font-family: inherit; letter-spacing: .02em;
}
.mm-btn-close { background: #F1F5F9; color: #4A5568; }
.mm-btn-close:hover { background: #E2E8F0; }
.mm-btn-reply { background: linear-gradient(135deg, #1565C0, #1E88E5); color: #fff; }
.mm-btn-reply:hover:not(:disabled) {
    background: linear-gradient(135deg, #0D47A1, #1565C0);
    transform: translateY(-1px); box-shadow: 0 6px 18px rgba(21,101,192,.3);
}
.mm-btn-reply:disabled { background: #93C5FD; cursor: not-allowed; transform: none; box-shadow: none; }

#msgNoResults { display: none; }

@media (max-width: 600px) {
    .msg-modal-box { border-radius: 16px 16px 0 0; max-height: 96vh; }
    .msg-modal-overlay { align-items: flex-end; padding: 0; }
    .mm-header { padding: 1.2rem 1.3rem 1rem; }
    .mm-body   { padding: 1.2rem 1.3rem; }
    .mm-footer { padding: .8rem 1.3rem 1rem; }
    .mm-meta-row { padding: .7rem 1.3rem; }
}
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: .75rem;">
    <h2 style="margin:0;">Contact Messages</h2>
    <div style="display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;">
        <!-- Filter tabs -->
        <div style="display:flex; gap:.35rem;">
            <button class="msg-filter-btn active" data-filter="all" onclick="filterByStatus(this,'all')"
                style="padding:.38rem .85rem;border-radius:50px;border:1.5px solid var(--border-color);background:#fff;font-size:.78rem;font-weight:700;cursor:pointer;font-family:inherit;transition:all .2s;">
                All <span id="countAll" style="opacity:.6;"></span>
            </button>
            <button class="msg-filter-btn" data-filter="unread" onclick="filterByStatus(this,'unread')"
                style="padding:.38rem .85rem;border-radius:50px;border:1.5px solid #FDE68A;background:#FEF9C3;color:#854D0E;font-size:.78rem;font-weight:700;cursor:pointer;font-family:inherit;transition:all .2s;">
                Unread <span id="countUnread" style="opacity:.7;"></span>
            </button>
            <button class="msg-filter-btn" data-filter="read" onclick="filterByStatus(this,'read')"
                style="padding:.38rem .85rem;border-radius:50px;border:1.5px solid #6EE7B7;background:#D1FAE5;color:#065F46;font-size:.78rem;font-weight:700;cursor:pointer;font-family:inherit;transition:all .2s;">
                Read <span id="countRead" style="opacity:.7;"></span>
            </button>
            <button class="msg-filter-btn" data-filter="replied" onclick="filterByStatus(this,'replied')"
                style="padding:.38rem .85rem;border-radius:50px;border:1.5px solid #93C5FD;background:#EFF6FF;color:#1D4ED8;font-size:.78rem;font-weight:700;cursor:pointer;font-family:inherit;transition:all .2s;">
                Replied <span id="countReplied" style="opacity:.7;"></span>
            </button>
        </div>
        <div class="msg-search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="msgSearchInput" placeholder="Search messages…" oninput="filterMessages(this.value)">
        </div>
    </div>
</div>

<div class="admin-card">
    <?php if (!empty($messages)): ?>
        <table class="admin-table" id="messagesTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message):
                    // Determine status
                    if ($message['is_replied']) {
                        $statusKey   = 'replied';
                        $statusLabel = 'Replied';
                        $statusIcon  = 'fa-reply';
                    } elseif ($message['is_read']) {
                        $statusKey   = 'read';
                        $statusLabel = 'Read';
                        $statusIcon  = 'fa-check';
                    } else {
                        $statusKey   = 'unread';
                        $statusLabel = 'Unread';
                        $statusIcon  = 'fa-circle';
                    }
                ?>
                    <tr data-status="<?php echo $statusKey; ?>"
                        data-search="<?php echo strtolower(htmlspecialchars(
                            $message['full_name'] . ' ' .
                            $message['email']     . ' ' .
                            $message['phone']     . ' ' .
                            $message['service_needed'] . ' ' .
                            $message['message']
                        )); ?>">
                        <td><?php echo sanitize($message['full_name']); ?></td>
                        <td><?php echo sanitize($message['email']); ?></td>
                        <td><?php echo sanitize($message['phone'] ?? 'N/A'); ?></td>
                        <td><?php echo sanitize($message['service_needed'] ?? 'General'); ?></td>
                        <td><?php echo formatDate($message['created_at']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $statusKey; ?>">
                                <i class="fas <?php echo $statusIcon; ?>"></i>
                                <?php echo $statusLabel; ?>
                            </span>
                        </td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-edit" onclick="viewMessage(<?php echo $message['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-delete" onclick="openMsgDeleteConfirm(<?php echo $message['id']; ?>, '<?php echo addslashes(sanitize($message['full_name'])); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr id="msgNoResults">
                    <td colspan="8" style="text-align:center; color:var(--text-light); padding:2rem;">
                        <i class="fas fa-search" style="margin-right:.4rem;"></i> No messages match your search.
                    </td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
            <i class="fas fa-inbox"></i> No messages yet.
        </p>
    <?php endif; ?>
</div>

<!-- Message Details Modal -->
<div class="msg-modal-overlay" id="messageModal">
    <div class="msg-modal-box">
        <div class="mm-header">
            <button class="mm-close-btn" onclick="closeMessageModal()">&times;</button>
            <div class="mm-header-inner">
                <div class="mm-avatar" id="mmAvatar">?</div>
                <div class="mm-header-text">
                    <h3 id="mmName">—</h3>
                    <p id="mmEmailSub">—</p>
                </div>
            </div>
        </div>
        <div class="mm-meta-row" id="mmMetaRow"></div>
        <div class="mm-body">
            <div class="mm-reply-alert" id="mmReplyAlert"></div>
            <div class="mm-field-label">Message</div>
            <div class="mm-message-box" id="mmMessageBox">—</div>
            <div class="mm-reply-section">
                <div class="mm-reply-header">
                    <i class="fas fa-reply" style="color:#1565C0;font-size:.95rem;"></i>
                    <h4>Reply to Sender</h4>
                    <span class="mm-reply-to-tag" id="mmReplyToTag">—</span>
                </div>
                <textarea class="mm-reply-textarea" id="mmReplyTextarea"
                    placeholder="Type your reply here…&#10;&#10;The sender will receive this message via email."></textarea>
            </div>
        </div>
        <div class="mm-footer">
            <button class="mm-btn mm-btn-close" onclick="closeMessageModal()">
                <i class="fas fa-times"></i> Close
            </button>
            <button class="mm-btn mm-btn-reply" id="mmSendReplyBtn" onclick="sendReply()">
                <i class="fas fa-paper-plane"></i> Send Reply
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="confirm-modal-overlay" id="msgDeleteConfirmModal">
    <div class="confirm-modal-box">
        <div class="confirm-modal-header">
            <div class="confirm-modal-icon"><i class="fas fa-trash-alt"></i></div>
            <div>
                <h3>Delete Message</h3>
                <p>This action cannot be undone</p>
            </div>
        </div>
        <div class="confirm-modal-body">
            <p>Are you sure you want to delete the message from <strong id="deleteMsgName">this sender</strong>?</p>
        </div>
        <div class="confirm-modal-footer">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeMsgDeleteConfirm()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="confirm-btn confirm-btn-delete" onclick="executeMsgDelete()">
                <i class="fas fa-trash-alt"></i> Yes, Delete
            </button>
        </div>
    </div>
</div>

<script>
var currentMessageId = null;
var _msgDeleteId     = null;

/* ── Count badges on load ── */
(function countBadges() {
    var rows = document.querySelectorAll('#messagesTable tbody tr:not(#msgNoResults)');
    var counts = { all: 0, unread: 0, read: 0, replied: 0 };
    rows.forEach(function(r) {
        var s = r.getAttribute('data-status');
        counts.all++;
        if (counts[s] !== undefined) counts[s]++;
    });
    document.getElementById('countAll').textContent     = '(' + counts.all + ')';
    document.getElementById('countUnread').textContent  = '(' + counts.unread + ')';
    document.getElementById('countRead').textContent    = '(' + counts.read + ')';
    document.getElementById('countReplied').textContent = '(' + counts.replied + ')';
}());

/* ── Status filter ── */
var _currentFilter = 'all';
function filterByStatus(btn, filter) {
    _currentFilter = filter;
    document.querySelectorAll('.msg-filter-btn').forEach(function(b) {
        b.style.outline = '';
        b.style.boxShadow = '';
    });
    btn.style.boxShadow = '0 0 0 2px var(--primary-color)';
    applyFilters();
}

/* ── Search ── */
function filterMessages(query) {
    applyFilters(query);
}

function applyFilters(query) {
    query = (query || document.getElementById('msgSearchInput').value).toLowerCase().trim();
    var rows = document.querySelectorAll('#messagesTable tbody tr:not(#msgNoResults)');
    var visible = 0;
    rows.forEach(function(row) {
        var matchSearch  = query === '' || (row.getAttribute('data-search') || '').includes(query);
        var matchStatus  = _currentFilter === 'all' || row.getAttribute('data-status') === _currentFilter;
        var show = matchSearch && matchStatus;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('msgNoResults').style.display = visible === 0 ? '' : 'none';
}

/* ── Delete confirm ── */
function openMsgDeleteConfirm(id, name) {
    _msgDeleteId = id;
    document.getElementById('deleteMsgName').textContent = '"' + name + '"';
    document.getElementById('msgDeleteConfirmModal').classList.add('active');
}
function closeMsgDeleteConfirm() {
    document.getElementById('msgDeleteConfirmModal').classList.remove('active');
    _msgDeleteId = null;
}
function executeMsgDelete() {
    if (_msgDeleteId) window.location.href = '../backend/delete_message.php?id=' + _msgDeleteId;
}
document.getElementById('msgDeleteConfirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeMsgDeleteConfirm();
});

/* ── View message modal ── */
function viewMessage(id) {
    currentMessageId = id;
    document.getElementById('mmReplyTextarea').value = '';
    hideReplyAlert();

    fetch('../backend/get_message.php?id=' + id)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.success) { alert('Could not load message.'); return; }
            var msg = data.data;

            document.getElementById('mmAvatar').textContent   = (msg.full_name || '?').trim().charAt(0).toUpperCase();
            document.getElementById('mmName').textContent     = msg.full_name || '—';
            document.getElementById('mmEmailSub').textContent = msg.email || '—';
            document.getElementById('mmReplyToTag').textContent = msg.email || '—';

            var date    = new Date(msg.created_at).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' });
            var isRead  = msg.is_read == 1;
            var isReplied = msg.is_replied == 1;

            /* Status chip */
            var statusClass, statusIcon, statusLabel;
            if (isReplied) {
                statusClass = 'replied'; statusIcon = 'fa-reply'; statusLabel = 'Replied';
            } else if (isRead) {
                statusClass = 'read'; statusIcon = 'fa-check'; statusLabel = 'Read';
            } else {
                statusClass = 'unread'; statusIcon = 'fa-circle'; statusLabel = 'Unread';
            }

            var chips = '<span class="mm-meta-chip mm-status-chip ' + statusClass + '">'
                      + '<i class="fas ' + statusIcon + '" style="font-size:.65rem;"></i> '
                      + statusLabel + '</span>'
                      + '<span class="mm-meta-chip"><i class="fas fa-clock"></i> ' + escHtml(date) + '</span>'
                      + '<span class="mm-meta-chip"><i class="fas fa-envelope"></i> ' + escHtml(msg.email) + '</span>';
            if (msg.phone)
                chips += '<span class="mm-meta-chip"><i class="fas fa-phone"></i> ' + escHtml(msg.phone) + '</span>';
            if (msg.service_needed)
                chips += '<span class="mm-meta-chip svc-chip"><i class="fas fa-cog"></i> ' + escHtml(msg.service_needed) + '</span>';
            document.getElementById('mmMetaRow').innerHTML = chips;

            document.getElementById('mmMessageBox').textContent = msg.message || '(no message body)';
            document.getElementById('messageModal').classList.add('active');
            document.body.style.overflow = 'hidden';

            /* Update row badge in table */
            document.querySelectorAll('#messagesTable tbody tr').forEach(function(row) {
                if (row.querySelector('[onclick="viewMessage(' + id + ')"]')) {
                    var badge = row.querySelector('.status-badge');
                    if (badge && badge.classList.contains('unread')) {
                        badge.className = 'status-badge read';
                        badge.innerHTML = '<i class="fas fa-check"></i> Read';
                        row.setAttribute('data-status', 'read');
                    }
                }
            });
        });
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.remove('active');
    document.body.style.overflow = '';
    currentMessageId = null;
}

/* ── Reply ── */
function sendReply() {
    var body = document.getElementById('mmReplyTextarea').value.trim();
    if (!body) {
        showReplyAlert('Please type a reply before sending.', 'error');
        document.getElementById('mmReplyTextarea').focus();
        return;
    }
    var btn = document.getElementById('mmSendReplyBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…';
    hideReplyAlert();

    var fd = new FormData();
    fd.append('message_id', currentMessageId);
    fd.append('reply_body', body);

    fetch('../backend/reply_message.php', { method: 'POST', body: fd })
        .then(function(r) { return r.text(); })
        .then(function(text) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reply';
            try {
                var data = JSON.parse(text);
                if (data.success) {
                    showReplyAlert(escHtml(data.message), 'success');
                    document.getElementById('mmReplyTextarea').value = '';

                    /* Update row badge to Replied */
                    document.querySelectorAll('#messagesTable tbody tr').forEach(function(row) {
                        if (row.querySelector('[onclick="viewMessage(' + currentMessageId + ')"]')) {
                            var badge = row.querySelector('.status-badge');
                            if (badge) {
                                badge.className = 'status-badge replied';
                                badge.innerHTML = '<i class="fas fa-reply"></i> Replied';
                                row.setAttribute('data-status', 'replied');
                            }
                        }
                    });

                    /* Update chip inside modal */
                    var chips = document.getElementById('mmMetaRow');
                    var statusChip = chips.querySelector('.mm-status-chip');
                    if (statusChip) {
                        statusChip.className = 'mm-meta-chip mm-status-chip replied';
                        statusChip.innerHTML = '<i class="fas fa-reply" style="font-size:.65rem;"></i> Replied';
                    }
                } else {
                    showReplyAlert(escHtml(data.message), 'error');
                }
            } catch(e) {
                showReplyAlert('Server error. Check browser console.', 'error');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reply';
            showReplyAlert('Could not reach the server. Please try again.', 'error');
        });
}

function showReplyAlert(msg, type) {
    var el = document.getElementById('mmReplyAlert');
    el.className = 'mm-reply-alert show ' + type;
    el.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + msg;
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
function hideReplyAlert() {
    var el = document.getElementById('mmReplyAlert');
    el.className = 'mm-reply-alert';
    el.innerHTML = '';
}

document.getElementById('messageModal').addEventListener('click', function(e) {
    if (e.target === this) closeMessageModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeMessageModal(); closeMsgDeleteConfirm(); }
});

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];
    });
}
</script>