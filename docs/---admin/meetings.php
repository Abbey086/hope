<?php
require_once __DIR__ . '/../api/db.php';

// Form Logic remains unchanged
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_meeting') {
    $title = $_POST['title'] ?? '';
    $meeting_url = $_POST['meeting_url'] ?? '';
    $platform = $_POST['platform'] ?? '';
    
    $pdo->query("UPDATE meeting_links SET is_active = 0");
    
    $stmt = $pdo->prepare("INSERT INTO meeting_links (title, meeting_url, platform, is_active) VALUES (?, ?, ?, 1)");
    $stmt->execute([$title, $meeting_url, $platform]);
    header("Location: index.php?page=meetings");
    exit;
}

if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['toggle_status'] == 1 ? 1 : 0;
    
    if ($status === 1) {
        $pdo->query("UPDATE meeting_links SET is_active = 0");
    }
    
    $stmt = $pdo->prepare("UPDATE meeting_links SET is_active = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    header("Location: index.php?page=meetings");
    exit;
}

if (isset($_GET['delete_meeting'])) {
    $stmt = $pdo->prepare("DELETE FROM meeting_links WHERE id = ?");
    $stmt->execute([$_GET['delete_meeting']]);
    header("Location: index.php?page=meetings");
    exit;
}

$meetings = $pdo->query("SELECT * FROM meeting_links ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row g-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1" style="color: var(--brand-green);">Meeting Links</h4>
                    <small class="text-muted">Only the Active meeting appears on the homepage.</small>
                </div>
            </div>
            
            <?php if(empty($meetings)): ?>
                <div class="alert alert-light text-center">No meeting links found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date Added</th>
                                <th>Details</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($meetings as $mtg): ?>
                            <tr>
                                <td class="text-nowrap text-secondary"><small><?= date('M j, Y', strtotime($mtg['created_at'])) ?></small></td>
                                <td>
                                    <span class="fw-semibold d-block"><?= htmlspecialchars($mtg['title']) ?></span>
                                    <small class="text-muted me-2"><i class="fas fa-video"></i> <?= htmlspecialchars($mtg['platform']) ?></small>
                                    <a href="<?= htmlspecialchars($mtg['meeting_url']) ?>" target="_blank" class="small text-success text-decoration-none">Test Link <i class="fas fa-external-link-alt ms-1"></i></a>
                                </td>
                                <td>
                                    <?php if($mtg['is_active']): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-2">Live</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <?php if(!$mtg['is_active']): ?>
                                        <a href="?page=meetings&toggle_status=1&id=<?= $mtg['id'] ?>" class="btn btn-sm btn-success" title="Set Active"><i class="fas fa-check"></i></a>
                                    <?php else: ?>
                                        <a href="?page=meetings&toggle_status=0&id=<?= $mtg['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Deactivate"><i class="fas fa-times"></i></a>
                                    <?php endif; ?>
                                    <a href="?page=meetings&delete_meeting=<?= $mtg['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this meeting record?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm p-4 h-100" style="border: 2px dashed #aadd68; background: #fdfdfd;">
            <h4 class="mb-2" style="color: var(--brand-green);">Post New Meeting</h4>
            <p class="text-muted small mb-4">Posting a new meeting auto-deactivates older ones.</p>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="save_meeting">
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Meeting Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Monthly Board Meeting" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Platform</label>
                    <select name="platform" class="form-select" required>
                        <option value="Zoom">Zoom</option>
                        <option value="Google Meet">Google Meet</option>
                        <option value="Microsoft Teams">Microsoft Teams</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary">Join URL</label>
                    <input type="url" name="meeting_url" class="form-control" placeholder="https://zoom.us/j/..." required>
                </div>
                
                <button type="submit" class="btn btn-brand w-100 py-2">Publish Live Link</button>
            </form>
        </div>
    </div>
</div>