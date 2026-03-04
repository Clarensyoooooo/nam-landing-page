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
    display: flex;
    align-items: center;
    gap: .6rem;
    background: #fff;
    border: 1.5px solid var(--border-color);
    border-radius: 8px;
    padding: .45rem 1rem;
    max-width: 280px;
    transition: border-color .2s, box-shadow .2s;
}
.msg-search-wrap:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(21,101,192,.1);
}
.msg-search-wrap i { color: #9CA3AF; font-size: .9rem; flex-shrink: 0; }
.msg-search-wrap input {
    border: none; outline: none;
    font-size: .88rem; color: #374151;
    width: 100%; font-family: inherit;
    background: transparent;
}

/* ══════════════════════════════════════════
   MODERN MESSAGE DETAILS MODAL
══════════════════════════════════════════ */
.msg-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.msg-modal-overlay.active {
    display: flex;
    animation: mmFadeIn .25s ease;
}
@keyframes mmFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

.msg-modal-box {
    background: #fff;
    border-radius: 20px;
    max-width: 680px;
    width: 100%;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 32px 80px rgba(0,0,0,.3);
    animation: mmSlideUp .32s cubic-bezier(.22,.68,0,1.1);
}
@keyframes mmSlideUp {
    from { transform: translateY(28px) scale(.97); opacity: 0; }
    to   { transform: translateY(0)    scale(1);   opacity: 1; }
}

/* ── Header ── */
.mm-header {
    background: linear-gradient(135deg, #0D47A1 0%, #1565C0 60%, #1E88E5 100%);
    padding: 1.5rem 1.8rem 1.3rem;
    position: relative;
    flex-shrink: 0;
}
.mm-header-inner { display: flex; align-items: center; gap: 1rem; }
.mm-avatar {
    width: 54px; height: 54px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    border: 2px solid rgba(255,255,255,.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem; font-weight: 800; color: #fff;
    text-transform: uppercase; flex-shrink: 0;
    font-family: 'Barlow Condensed', sans-serif;
}
.mm-header-text h3 { color: #fff; margin: 0 0 3px; font-size: 1.1rem; font-weight: 800; }
.mm-header-text p  { color: rgba(255,255,255,.75); margin: 0; font-size: .83rem; }
.mm-close-btn {
    position: absolute; top: 1rem; right: 1rem;
    background: rgba(255,255,255,.18); border: none;
    border-radius: 50%; width: 34px; height: 34px;
    color: #fff; font-size: 1.25rem; line-height: 34px;
    text-align: center; cursor: pointer;
    transition: background .2s, transform .2s;
}
.mm-close-btn:hover { background: rgba(255,255,255,.32); transform: scale(1.1); }

/* ── Meta chips row ── */
.mm-meta-row {
    display: flex; gap: .45rem; flex-wrap: wrap;
    padding: .85rem 1.8rem;
    background: #F8FAFC;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
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
.mm-status-chip.unread { background: #EFF6FF; border-color: #93C5FD; color: #1D4ED8; }
.mm-status-chip.read   { background: #D1FAE5; border-color: #6EE7B7; color: #065F46; }

/* ── Scrollable body ── */
.mm-body { overflow-y: auto; flex: 1; padding: 1.6rem 1.8rem; }

.mm-field-label {
    font-size: .7rem; font-weight: 800; letter-spacing: .1em;
    text-transform: uppercase; color: #9CA3AF; margin-bottom: .5rem;
}
.mm-message-box {
    background: #F8FAFC; border: 1px solid #e2e8f0;
    border-left: 4px solid #1565C0;
    border-radius: 0 12px 12px 0;
    padding: 1.1rem 1.3rem;
    color: #1A202C; font-size: .95rem; line-height: 1.85;
    white-space: pre-line; margin-bottom: 1.6rem;
}

/* ── Reply section ── */
.mm-reply-section { border-top: 1.5px dashed #e2e8f0; padding-top: 1.4rem; }
.mm-reply-header { display: flex; align-items: center; gap: .55rem; margin-bottom: .9rem; }
.mm-reply-header h4 { margin: 0; font-size: .95rem; color: #0A0A0A; font-weight: 700; }
.mm-reply-to-tag {
    font-size: .77rem; color: #6B7280;
    background: #F1F5F9; padding: .2rem .65rem;
    border-radius: 50px; font-weight: 600;
    max-width: 240px; overflow: hidden;
    text-overflow: ellipsis; white-space: nowrap;
}
.mm-reply-textarea {
    width: 100%; min-height: 120px;
    padding: .85rem 1rem;
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: .92rem; font-family: inherit; color: #1A202C;
    resize: vertical; transition: border-color .2s, box-shadow .2s;
    line-height: 1.7; background: #fff;
}
.mm-reply-textarea:focus {
    outline: none; border-color: #1565C0;
    box-shadow: 0 0 0 3px rgba(21,101,192,.1);
}

/* ── Reply alert ── */
.mm-reply-alert {
    border-radius: 8px; padding: .6rem 1rem;
    font-size: .84rem; margin-bottom: 1rem;
    display: none; align-items: center; gap: .5rem; font-weight: 600;
}
.mm-reply-alert.show    { display: flex; }
.mm-reply-alert.success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
.mm-reply-alert.error   { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }

/* ── Footer ── */
.mm-footer {
    padding: .9rem 1.8rem 1.2rem;
    display: flex; gap: .65rem; justify-content: flex-end;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0; background: #FAFBFF;
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
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(21,101,192,.3);
}
.mm-btn-reply:disabled { background: #93C5FD; cursor: not-allowed; transform: none; box-shadow: none; }

/* ── No results row ── */
#msgNoResults { display: none; }

@media (max-width: 600px) {
    .msg-modal-box   { border-radius: 16px 16px 0 0; max-height: 96vh; }
    .msg-modal-overlay { align-items: flex-end; padding: 0; }
    .mm-header       { padding: 1.2rem 1.3rem 1rem; }
    .mm-body         { padding: 1.2rem 1.3rem; }
    .mm-footer       { padding: .8rem 1.3rem 1rem; }
    .mm-meta-row     { padding: .7rem 1.3rem; }
}
</style>

<!-- Page header with search -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: .75rem;">
    <h2 style="margin:0;">Contact Messages</h2>
    <div class="msg-search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" id="msgSearchInput" placeholder="Search messages…" oninput="filterMessages(this.value)">
    </div>
</div>

<!-- Messages Table -->
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
                <?php foreach ($messages as $message): ?>
                    <tr data-search="<?php echo strtolower(htmlspecialchars(
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
                            <span class="badge" style="background-color: <?php echo !$message['is_read'] ? '#FFC107' : '#28A745'; ?>;">
                                <?php echo !$message['is_read'] ? 'Unread' : 'Read'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-edit" onclick="viewMessage(<?php echo $message['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteMessage(<?php echo $message['id']; ?>)">
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

<!-- ══════════════════════════════════════════
     MODERN MESSAGE DETAILS MODAL
══════════════════════════════════════════ -->
<div class="msg-modal-overlay" id="messageModal">
    <div class="msg-modal-box">

        <!-- Header -->
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

        <!-- Meta chips -->
        <div class="mm-meta-row" id="mmMetaRow"></div>

        <!-- Body -->
        <div class="mm-body">
            <div class="mm-reply-alert" id="mmReplyAlert"></div>

            <div class="mm-field-label">Message</div>
            <div class="mm-message-box" id="mmMessageBox">—</div>

            <!-- Reply -->
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

        <!-- Footer -->
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

<script>
var currentMessageId = null;

/* ── Search / filter ── */
function filterMessages(query) {
    var q = query.toLowerCase().trim();
    var rows = document.querySelectorAll('#messagesTable tbody tr:not(#msgNoResults)');
    var visibleCount = 0;

    rows.forEach(function(row) {
        var text = row.getAttribute('data-search') || '';
        var show = q === '' || text.includes(q);
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    document.getElementById('msgNoResults').style.display = visibleCount === 0 ? '' : 'none';
}

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

            var initial = (msg.full_name || '?').trim().charAt(0).toUpperCase();
            document.getElementById('mmAvatar').textContent     = initial;
            document.getElementById('mmName').textContent       = msg.full_name || '—';
            document.getElementById('mmEmailSub').textContent   = msg.email     || '—';
            document.getElementById('mmReplyToTag').textContent = msg.email     || '—';

            var date   = new Date(msg.created_at).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' });
            var isRead = msg.is_read == 1;
            var chips  = '<span class="mm-meta-chip mm-status-chip ' + (isRead ? 'read' : 'unread') + '">'
                       + '<i class="fas fa-' + (isRead ? 'check-circle' : 'circle') + '" style="font-size:.65rem;"></i> '
                       + (isRead ? 'Read' : 'Unread') + '</span>'
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

            // Update table badge to Read
            document.querySelectorAll('#messagesTable tbody tr').forEach(function(row) {
                if (row.querySelector('[onclick="viewMessage(' + id + ')"]')) {
                    var badge = row.querySelector('.badge');
                    if (badge && badge.textContent.trim() === 'Unread') {
                        badge.textContent = 'Read';
                        badge.style.backgroundColor = '#28A745';
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

function deleteMessage(id) {
    if (confirm('Are you sure you want to delete this message?')) {
        window.location.href = '../backend/delete_message.php?id=' + id;
    }
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
    fd.append('reply_body',  body);

    fetch('../backend/reply_message.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reply';
            if (data.success) {
                showReplyAlert(escHtml(data.message), 'success');
                document.getElementById('mmReplyTextarea').value = '';
            } else {
                showReplyAlert(escHtml(data.message), 'error');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reply';
            showReplyAlert('Network error. Please try again.', 'error');
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

/* ── Close on backdrop / Escape ── */
document.getElementById('messageModal').addEventListener('click', function(e) {
    if (e.target === this) closeMessageModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMessageModal();
});

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];
    });
}
</script>