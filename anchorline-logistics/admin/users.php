<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);

$page_title = 'Manage users';
$meta_description = 'Admin: view accounts and change user roles.';
$robots = 'noindex, nofollow';

$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $role = $_POST['role'] ?? '';
    $validRoles = ['admin', 'member', 'normal'];

    if ($id === current_user()['id']) {
        $notice = 'You cannot change your own role.';
    } elseif (in_array($role, $validRoles, true)) {
        $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->execute([$role, $id]);
        flash_set('ok', 'Role updated.');
        redirect(BASE_URL . '/admin/users.php');
    }
}

// Optional search: filters by name or email. Bound LIKE parameter, same
// safe pattern used on the other admin list pages.
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC');
    $stmt->execute([$like, $like]);
    $users = $stmt->fetchAll();
} else {
    $users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
}

require ROOT_PATH . '/includes/header.php';
?>
    <section class="page-head">
      <div class="wrap">
        <p class="eyebrow">Admin</p>
        <h1>Users</h1>
        <p>Every registered account and its access level.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <?php if ($notice): ?>
        <p class="feedback feedback--error" role="alert"><?php echo e($notice); ?></p>
        <?php endif; ?>
        <form class="form-row" method="get" action="<?php echo e(BASE_URL); ?>/admin/users.php" style="align-items: flex-end;">
          <div class="field" style="margin-bottom: 0; flex: 1;">
            <label for="q">Search</label>
            <input type="text" id="q" name="q" value="<?php echo e($search); ?>" placeholder="Name or email">
          </div>
          <div class="btn-row" style="margin-bottom: 0.5rem;">
            <button class="btn btn--primary btn--sm" type="submit">Search</button>
            <?php if ($search !== ''): ?>
            <a class="btn btn--ghost btn--sm" href="<?php echo e(BASE_URL); ?>/admin/users.php">Clear</a>
            <?php endif; ?>
          </div>
        </form>
        <div class="table-scroll">
          <table class="manifest">
            <caption><?php echo count($users); ?> user<?php echo count($users) === 1 ? '' : 's'; ?><?php echo $search !== '' ? ' matching "' . e($search) . '"' : ''; ?></caption>
            <thead>
              <tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Joined</th><th scope="col">Role</th><th scope="col">Change role</th></tr>
            </thead>
            <tbody>
              <?php if (!$users): ?>
              <tr><td colspan="5">No users<?php echo $search !== '' ? ' match that search.' : '.'; ?></td></tr>
              <?php endif; ?>
              <?php foreach ($users as $u): ?>
              <tr>
                <td><?php echo e($u['name']); ?></td>
                <td><?php echo e($u['email']); ?></td>
                <td><?php echo e(date('d M Y', strtotime($u['created_at']))); ?></td>
                <td><span class="badge badge--<?php echo e($u['role']); ?>"><?php echo e($u['role']); ?></span></td>
                <td>
                  <?php if ((int) $u['id'] === current_user()['id']): ?>
                  <span class="hint">This is you</span>
                  <?php else: ?>
                  <form method="post" action="<?php echo e(BASE_URL); ?>/admin/users.php" class="actions">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int) $u['id']; ?>">
                    <label class="sr-only" for="role-<?php echo (int) $u['id']; ?>">Role for <?php echo e($u['name']); ?></label>
                    <select id="role-<?php echo (int) $u['id']; ?>" name="role">
                      <option value="normal"<?php echo $u['role'] === 'normal' ? ' selected' : ''; ?>>normal</option>
                      <option value="member"<?php echo $u['role'] === 'member' ? ' selected' : ''; ?>>member</option>
                      <option value="admin"<?php echo $u['role'] === 'admin' ? ' selected' : ''; ?>>admin</option>
                    </select>
                    <button class="btn btn--primary btn--sm" type="submit">Save</button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
<?php require ROOT_PATH . '/includes/footer.php'; ?>
