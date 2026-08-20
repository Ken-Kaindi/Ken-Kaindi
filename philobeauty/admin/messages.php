<?php
require_once '../includes/config.php';
requireAdmin();
$pageTitle = 'Messages';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $id = (int) ($_POST['read'] ?? 0);
    $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$id]);
    redirect('messages.php');
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();

require_once '../includes/admin-header.php';
?>

<div class="admin-header-row"><h1>Messages</h1></div>

<div class="admin-panel">
  <?php if (empty($messages)): ?>
    <p class="empty-state">No messages yet.</p>
  <?php else: ?>
    <?php foreach ($messages as $m): ?>
      <div class="message-card <?= $m['is_read'] ? '' : 'unread' ?>">
        <div class="message-head">
          <strong><?= sanitize($m['name']) ?></strong>
          <span><?= sanitize($m['email']) ?></span>
          <span><?= date('d M Y, H:i', strtotime($m['created_at'])) ?></span>
        </div>
        <p><?= nl2br(sanitize($m['message'])) ?></p>
        <?php if (!$m['is_read']): ?><form method="post"><input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>"><input type="hidden" name="read" value="<?= (int) $m['id'] ?>"><button type="submit" class="table-link">Mark as read</button></form><?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
