<?php
// admin/pwd_list.php
// List of all registered PWDs for PWD Admin / Super Admin

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['PWD_ADMIN','SUPER']);

// Optional search
$search = trim($_GET['q'] ?? '');

// Build simple query
if ($search !== '') {
    $like = '%' . $search . '%';
    $sql = "SELECT
                pwd_number,
                full_name,
                sex,
                municipality,
                province,
                disability_category,
                educational_level,
                employment_status,
                is_verified,
                verified_at
            FROM pwd_profiles
            WHERE pwd_number LIKE ?
               OR full_name LIKE ?
               OR municipality LIKE ?
            ORDER BY full_name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $like, $like, $like);
} else {
    $sql = "SELECT
                pwd_number,
                full_name,
                sex,
                municipality,
                province,
                disability_category,
                educational_level,
                employment_status,
                is_verified,
                verified_at
            FROM pwd_profiles
            ORDER BY full_name ASC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$pageTitle = 'PWD List';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="card">
    <header class="page-header">
        <h1 class="page-title">PWD Master List</h1>
        <p class="page-subtitle">All registered Persons with Disability</p>
    </header>

    <p>
        <a href="register_pwd.php" class="btn primary-btn">
            + Register New PWD
        </a>
    </p>

    <!-- SEARCH FORM (UNCHANGED) -->
    <form method="get" style="margin: 10px 0;">
        <label for="q">Search (Name / ID / Municipality):</label>
        <input type="text" id="q" name="q" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        <?php if ($search !== ''): ?>
            <a href="pwd_list.php">Clear</a>
        <?php endif; ?>
    </form>

    <?php if ($result->num_rows === 0): ?>
        <p>No PWD records found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>PWD ID Number</th>
                        <th>Full Name</th>
                        <th>Sex</th>
                        <th>Location</th>
                        <th>Disability</th>
                        <th>Education</th>
                        <th>Employment</th>
                        <th>Verification</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['pwd_number']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['sex']) ?></td>
                        <td><?= htmlspecialchars($row['municipality'] . ', ' . $row['province']) ?></td>
                        <td><?= htmlspecialchars($row['disability_category']) ?></td>
                        <td><?= htmlspecialchars($row['educational_level']) ?></td>
                        <td><?= htmlspecialchars($row['employment_status']) ?></td>

                        <!-- VERIFICATION STATUS (UNCHANGED) -->
                        <td>
                            <?php if ($row['is_verified']): ?>
                                <span class="badge badge-success">Verified</span><br>
                                <small><?= htmlspecialchars($row['verified_at']) ?></small>
                            <?php else: ?>
                                <span class="badge badge-danger">Not Verified</span>
                            <?php endif; ?>
                        </td>

                        <!-- ACTIONS (ADDED ONLY) -->
                        <td>
                            <!-- EDIT PROFILE (NEW) -->
                            <a href="edit_pwd.php?pwd_number=<?= urlencode($row['pwd_number']) ?>"
                               class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <!-- PRINT ID (EXISTING) -->
                            <a href="pwd_id_card.php?pwd_number=<?= urlencode($row['pwd_number']) ?>"
                               target="_blank"
                               class="btn btn-sm btn-secondary">
                                Print ID
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    <?php endif; ?>

</section>

<?php
$stmt->close();
require_once __DIR__ . '/../includes/footer.php';
?>