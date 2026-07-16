<?php
// docs/admin/events.php
require_once __DIR__ . '/../api/db.php';

// Handle Add/Edit Event Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_event') {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $location = $_POST['location'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $is_highlighted = isset($_POST['is_highlighted']) ? 1 : 0;

    if (!empty($id)) {
        // Update
        $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, event_date=?, location=?, image_url=?, is_highlighted=? WHERE id=?");
        $stmt->execute([$title, $description, $event_date, $location, $image_url, $is_highlighted, $id]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, location, image_url, is_highlighted) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $event_date, $location, $image_url, $is_highlighted]);
    }
    header("Location: index.php?page=events");
    exit;
}

// Handle Delete
if (isset($_GET['delete_event'])) {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$_GET['delete_event']]);
    header("Location: index.php?page=events");
    exit;
}

// Fetch all events
$events = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);

// If Edit requested, fetch that event
$edit_event = null;
if (isset($_GET['edit_event'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$_GET['edit_event']]);
    $edit_event = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="row g-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0" style="color: var(--brand-green);">All Events</h4>
                <button class="btn btn-brand d-lg-none btn-sm px-3 py-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#eventFormOffcanvas">
                    <i class="fas fa-plus me-1"></i> Add Event
                </button>
            </div>
            
            <?php if(empty($events)): ?>
                <div class="alert alert-light text-center">No events found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Location</th>
                                <th class="text-center">Highlight</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($events as $ev): ?>
                            <tr>
                                <td class="text-nowrap text-secondary"><small><?= date('M j, Y', strtotime($ev['event_date'])) ?></small></td>
                                <td class="fw-semibold"><?= htmlspecialchars($ev['title']) ?></td>
                                <td><?= htmlspecialchars($ev['location']) ?></td>
                                <td class="text-center">
                                    <?php if($ev['is_highlighted']): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="?page=events&edit_event=<?= $ev['id'] ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                                    <a href="?page=events&delete_event=<?= $ev['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this event?');"><i class="fas fa-trash"></i></a>
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
        
        <div class="offcanvas-lg offcanvas-end w-100 border-start" tabindex="-1" id="eventFormOffcanvas" aria-labelledby="eventFormOffcanvasLabel">
            
            <div class="offcanvas-header d-lg-none bg-light border-bottom p-3">
                <h5 class="offcanvas-title fw-bold m-0" id="eventFormOffcanvasLabel" style="color: var(--brand-green);">
                    <?= $edit_event ? 'Edit Event' : 'Add New Event' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#eventFormOffcanvas" aria-label="Close"></button>
            </div>
            
            <div class="offcanvas-body p-0 p-lg-0 h-100">
                <div class="card shadow-sm p-4 h-100 w-100 border-0" style="background: #fdfdfd; border: 2px dashed #aadd68 !important;">
                    
                    <h4 class="mb-4 d-none d-lg-block" style="color: var(--brand-green);">
                        <?= $edit_event ? 'Edit Event' : 'Add New Event' ?>
                    </h4>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="save_event">
                        <?php if($edit_event): ?>
                            <input type="hidden" name="id" value="<?= $edit_event['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Event Title</label>
                            <input type="text" name="title" class="form-control" required value="<?= $edit_event ? htmlspecialchars($edit_event['title']) : '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Event Date</label>
                            <input type="date" name="event_date" class="form-control" required value="<?= $edit_event ? htmlspecialchars($edit_event['event_date']) : '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Location / Venue</label>
                            <input type="text" name="location" class="form-control" value="<?= $edit_event ? htmlspecialchars($edit_event['location']) : '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Cover Image URL</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://..." value="<?= $edit_event ? htmlspecialchars($edit_event['image_url']) : '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Description</label>
                            <textarea name="description" class="form-control" rows="4" required><?= $edit_event ? htmlspecialchars($edit_event['description']) : '' ?></textarea>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" name="is_highlighted" id="is_highlighted" <?= ($edit_event && $edit_event['is_highlighted']) ? 'checked' : '' ?>>
                            <label class="form-check-label text-secondary" for="is_highlighted">Highlight in Top Carousel</label>
                        </div>
                        
                        <button type="submit" class="btn btn-brand w-100 py-2">
                            <?= $edit_event ? 'Save Changes' : 'Create Event' ?>
                        </button>
                        <?php if($edit_event): ?>
                            <a href="?page=events" class="btn btn-outline-secondary w-100 mt-2 py-2">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php if($edit_event): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (window.innerWidth < 992) {
        var myOffcanvas = document.getElementById('eventFormOffcanvas');
        var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);
        bsOffcanvas.show();
    }
});
</script>
<?php endif; ?>