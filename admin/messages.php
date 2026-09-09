<?php
require 'guard.php';
require '../data/admin.php';

$adminActive = 'messages';
$messages = getAllMessages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Admin | Curlétte</title>
    <link rel="icon" href="../assets/images/C-icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500,600;1,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'partials/nav.php'; ?>

<main class="admin-main">

    <div class="admin-page-header">
        <div>
            <h1>Messages</h1>
            <p><?= count($messages) ?> message<?= count($messages) === 1 ? '' : 's' ?> from the Contact page.</p>
        </div>
    </div>

    <?php if (empty($messages)): ?>
        <div class="admin-table-wrap">
            <p class="admin-empty">No messages yet.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <span class="muted"><?= htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if ($msg['phone']): ?>
                                    <span class="muted"><?= htmlspecialchars($msg['phone'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width:360px;"><?= nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')) ?></td>
                            <td><span class="muted"><?= date('M j, Y g:i A', strtotime($msg['created_at'])) ?></span></td>
                            <td><span class="admin-badge <?= $msg['is_read'] ? 'read' : 'unread' ?>"><?= $msg['is_read'] ? 'Read' : 'Unread' ?></span></td>
                            <td>
                                <?php if (!$msg['is_read']): ?>
                                    <form method="post" action="mark-message-read.php" class="admin-message-form">
                                        <input type="hidden" name="message_id" value="<?= (int) $msg['id'] ?>">
                                        <button type="submit">Mark as read</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</main>

</body>
</html>
