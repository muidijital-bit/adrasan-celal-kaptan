<?php include 'header.php';

$db = getDB();

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$_GET['delete']]);
    header('Location: messages.php'); exit;
}

if (isset($_GET['read'])) {
    $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$_GET['read']]);
}

$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();

$viewing = null;
if (isset($_GET['view'])) {
    $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$_GET['view']]);
    $viewing = $stmt->fetch();
    if ($viewing && !$viewing['is_read']) {
        $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$viewing['id']]);
    }
}
?>

<?php if ($viewing): ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-envelope-open"></i> Mesaj Detayı</h3>
        <a href="messages.php" class="btn btn-sm btn-outline">Geri Dön</a>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
            <div><strong>İsim:</strong> <?= sanitize($viewing['name']) ?></div>
            <div><strong>E-posta:</strong> <?= sanitize($viewing['email'] ?: '-') ?></div>
            <div><strong>Telefon:</strong> <a href="tel:<?= sanitize($viewing['phone']) ?>"><?= sanitize($viewing['phone']) ?></a></div>
            <div><strong>Konu:</strong> <?= sanitize($viewing['subject'] ?: '-') ?></div>
            <div><strong>Tarih:</strong> <?= date('d.m.Y H:i', strtotime($viewing['created_at'])) ?></div>
        </div>
        <div style="background:#f8f9fa;padding:20px;border-radius:10px;">
            <strong>Mesaj:</strong><br><br>
            <?= nl2br(sanitize($viewing['message'])) ?>
        </div>
        <div style="margin-top:20px;display:flex;gap:10px;">
            <a href="https://wa.me/90<?= preg_replace('/\D/', '', $viewing['phone']) ?>" target="_blank" class="btn btn-success"><i class="fab fa-whatsapp"></i> WhatsApp ile Yanıtla</a>
            <?php if ($viewing['email']): ?>
            <a href="mailto:<?= sanitize($viewing['email']) ?>" class="btn btn-primary"><i class="fas fa-reply"></i> E-posta ile Yanıtla</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-envelope"></i> İletişim Mesajları</h3>
    </div>
    <table>
        <thead><tr><th>İsim</th><th>Telefon</th><th>Konu</th><th>Tarih</th><th>Durum</th><th>İşlem</th></tr></thead>
        <tbody>
            <?php foreach ($messages as $m): ?>
            <tr style="<?= !$m['is_read'] ? 'background:#fff9e6;' : '' ?>">
                <td><strong><?= sanitize($m['name']) ?></strong></td>
                <td><?= sanitize($m['phone']) ?></td>
                <td><?= sanitize($m['subject'] ?: '-') ?></td>
                <td><?= date('d.m.Y H:i', strtotime($m['created_at'])) ?></td>
                <td><?= $m['is_read'] ? '<span style="color:#25d366">Okundu</span>' : '<span style="color:#f77f00;font-weight:600">Yeni</span>' ?></td>
                <td>
                    <a href="?view=<?= $m['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                    <a href="?delete=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
            <tr><td colspan="6" style="text-align:center;color:#6c757d;">Henüz mesaj yok.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
