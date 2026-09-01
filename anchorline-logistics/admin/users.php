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

$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();

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
        <div class="table-scroll">
          <table class="manifest">
            <caption><?php echo count($users); ?> users</caption>
            <thead>
              <tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Joined</th><th scope="col">Role</th><th scope="col">Change role</th></tr>
            </thead>
            <tbody>
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
