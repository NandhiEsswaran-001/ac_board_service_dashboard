<?php
require_once '../includes/config.php';
$pageTitle = 'Manage Users';
if (!isOwner()) { header('Location: dashboard.php'); exit; }

$db      = getDB();
$success = '';
$error   = '';

if (isset($_GET['success'])) {
    $map = [
        'add' => 'User added successfully.',
        'pass' => 'Password updated.',
        'delete' => 'User deleted.'
    ];
    $key = $_GET['success'];
    if (isset($map[$key])) $success = $map[$key];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $username  = trim($_POST['username'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $role      = $_POST['role'] ?? 'staff';
        $password  = $_POST['password'] ?? '';

        $validRoles = ['staff', 'technician', 'owner'];
        if (!in_array($role, $validRoles)) $role = 'staff';

        if (!$username || !$full_name || !$password) {
            $error = 'All fields are required.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } else {
            $check = $db->prepare("SELECT id FROM users WHERE username=?");
            $check->execute([$username]);
            if ($check->fetch()) {
                $error = 'Username already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $db->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?,?,?,?)")
                   ->execute([$username, $hash, $full_name, $role]);
                header('Location: users.php?success=add');
                exit;
            }
        }
    }

    if ($action === 'change_pass') {
        $uid      = intval($_POST['uid'] ?? 0);
        $new_pass = $_POST['new_pass'] ?? '';
        if ($uid && strlen($new_pass) >= 6) {
            $hash = password_hash($new_pass, PASSWORD_BCRYPT);
            $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hash, $uid]);
            header('Location: users.php?success=pass');
            exit;
        } else {
            $error = 'Password must be at least 6 characters.';
        }
    }

    if ($action === 'delete') {
        $del_id = intval($_POST['del_id'] ?? 0);
        if ($del_id === (int)$_SESSION['user_id']) {
            $error = 'You cannot delete your own account.';
        } elseif ($del_id > 0) {
            // Null out FK references before deleting
            $db->prepare("UPDATE field_services SET assigned_employee=NULL WHERE assigned_employee=?")->execute([$del_id]);
            $db->prepare("UPDATE field_services SET created_by=NULL WHERE created_by=?")->execute([$del_id]);
            $db->prepare("UPDATE board_services  SET created_by=NULL WHERE created_by=?")->execute([$del_id]);
            $db->prepare("DELETE FROM users WHERE id=?")->execute([$del_id]);
            header('Location: users.php?success=delete');
            exit;
        }
    }
}

$users = $db->query("SELECT * FROM users ORDER BY role, full_name")->fetchAll();

include '../includes/header.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="users-grid">

<div class="card">
    <div class="card-header"><span class="card-title">All Users</span></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Full Name</th><th>Username</th><th>Role</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= statusBadge(ucfirst($u['role'])) ?></td>
                    <td>
                        <div class="flex-gap">
                            <button class="btn btn-sm btn-light"
                                    onclick="showChangePass(<?= $u['id'] ?>, '<?= htmlspecialchars($u['full_name'], ENT_QUOTES) ?>')">
                                Change Pass
                            </button>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" style="display:inline"
                                  onsubmit="return confirm('Delete user <?= htmlspecialchars($u['full_name'], ENT_QUOTES) ?>?')">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="del_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Add New User</span></div>
    <div class="card-body">
        <form method="POST" class="form-stack">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Full name" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Login username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="password-wrap">
                    <input type="password" id="newUserPass" name="password" placeholder="Min 6 characters" required>
                    <button type="button" class="password-toggle" data-toggle-password="newUserPass" aria-label="Show password">Show</button>
                </div>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="staff">Staff</option>
                    <option value="technician">Technician</option>
                    <option value="owner">Owner</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Add User</button>
        </form>
    </div>
</div>

</div>

<!-- Change Password Modal -->
<div id="changePasDlg" class="modal-overlay">
    <div class="modal-card">
        <h3 class="modal-title">Change Password for <span id="dlgName"></span></h3>
        <form method="POST" class="form-stack">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="change_pass">
            <input type="hidden" name="uid" id="dlgUid">
            <div class="form-group">
                <label>New Password (min 6 chars)</label>
                <div class="password-wrap">
                    <input type="password" name="new_pass" id="dlgPass" placeholder="New password" required>
                    <button type="button" class="password-toggle" data-toggle-password="dlgPass" aria-label="Show password">Show</button>
                </div>
            </div>
            <div class="flex-gap">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-light"
                        onclick="document.getElementById('changePasDlg').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showChangePass(uid, name) {
    document.getElementById('dlgUid').value = uid;
    document.getElementById('dlgName').textContent = name;
    document.getElementById('dlgPass').value = '';
    document.getElementById('changePasDlg').style.display = 'flex';
}
</script>

<?php include '../includes/footer.php'; ?>
