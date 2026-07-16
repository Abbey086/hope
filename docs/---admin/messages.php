<?php
require_once __DIR__ . '/../api/db.php';

$stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['delete_msg'])) {
    $del = $pdo->prepare("DELETE FROM messages WHERE id=?");
    $del->execute([$_GET['delete_msg']]);
    echo "<script>window.location.href='index.php?page=messages';</script>";
    exit;
}

if (isset($_GET['read_msg'])) {
    $upd = $pdo->prepare("UPDATE messages SET status='read' WHERE id=?");
    $upd->execute([$_GET['read_msg']]);
    echo "<script>window.location.href='index.php?page=messages';</script>";
    exit;
}
?>

<div class="card shadow-sm border-0" style="height: calc(100vh - 120px);">
    <div class="row g-0 h-100">
        
        <div class="col-md-4 border-end h-100 overflow-auto bg-white" style="border-top-left-radius: 12px; border-bottom-left-radius: 12px;">
            <?php if(empty($messages)): ?>
                <div class="p-4 text-muted text-center">No messages received yet.</div>
            <?php else: ?>
                <div class="list-group list-group-flush rounded-0">
                    <?php foreach($messages as $msg): ?>
                        <button type="button" class="list-group-item list-group-item-action p-3 <?= $msg['status']=='unread' ? 'bg-light fw-bold' : '' ?>" onclick="openMessage(<?= $msg['id'] ?>)">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <span class="mb-1 text-truncate" style="max-width: 70%;"><?= htmlspecialchars($msg['name']) ?></span>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= date('M j', strtotime($msg['created_at'])) ?></small>
                            </div>
                            <p class="mb-1 small text-truncate text-secondary"><?= htmlspecialchars($msg['message']) ?></p>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8 d-flex flex-column h-100 bg-white" style="border-top-right-radius: 12px; border-bottom-right-radius: 12px;">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                <h5 class="mb-0 text-truncate" id="mailTitle" style="max-width: 60%;">Select a message</h5>
                <div id="mailActions"></div>
            </div>
            
            <div class="p-4 overflow-auto flex-grow-1" id="mailContent">
                <div class="d-flex h-100 align-items-center justify-content-center text-muted">
                    <p>Choose a message from the list to read it.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
const messages = <?= json_encode($messages) ?>;

function openMessage(id) {
    let msg = messages.find(m => m.id == id);
    let details = "";

    try {
        let json = JSON.parse(msg.details);
        for(let key in json) {
            if(key === 'files') {
                details += `<p><b>Attachment</b>: ${json[key]['resume']} (<a href='../uploads/${json[key]['resume']}' download class='text-decoration-none'>Download</a>)</p>`;
            } else {
                details += `<p class='mb-1'><strong class='text-capitalize'>${key.replace("_"," ")}</strong>: ${json[key]}</p>`;
            }
        }
    } catch(e) {}

    document.getElementById("mailTitle").innerHTML = `<span class="fw-bold">${msg.name}</span> <small class="text-muted fw-normal">&lt;${msg.email}&gt;</small>`;

    document.getElementById("mailContent").innerHTML = `
        <div class="mb-4">
            <span class="badge bg-secondary mb-2">${msg.form_type}</span>
            <p class="text-muted small mb-0"><i class="far fa-clock me-1"></i> ${msg.created_at}</p>
        </div>
        <div class="bg-light p-3 rounded mb-4" style="white-space: pre-wrap; font-family: inherit;">${msg.message}</div>
        ${details ? `<div class="card p-3 border-0 bg-light"><h6 class="border-bottom pb-2 mb-3">Additional Details</h6>${details}</div>` : ''}
    `;

    document.getElementById("mailActions").innerHTML = `
        <a href="?page=messages&read_msg=${msg.id}" class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-check"></i> Mark Read</a>
        <a href="?page=messages&delete_msg=${msg.id}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i></a>
    `;
}
</script>