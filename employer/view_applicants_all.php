<?php
// employer/view_applicants_all.php
// Shows ALL applicants across ALL jobs of this employer

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireEmployerLogin();

$permitNo = $_SESSION['business_permit_no'] ?? '';
$pageTitle = 'All Job Applicants';

require_once __DIR__ . '/../includes/header.php';

/* =====================================================
   LOAD ALL APPLICATIONS FOR THIS EMPLOYER
   ===================================================== */
$sql = "
SELECT
    a.application_id,
    a.job_id,
    a.status AS application_status,
    a.date_applied,

    j.job_title,
    j.status AS job_status,

    p.pwd_number,
    p.full_name,
    p.disability_category,
    p.employment_status

FROM job_applications a
JOIN job_postings j
    ON a.job_id = j.job_id
JOIN pwd_profiles p
    ON a.pwd_number = p.pwd_number

WHERE j.business_permit_no = ?
ORDER BY a.date_applied DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $permitNo);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<section class="card">
    <h1 class="page-title">All Applicants</h1>
    <p class="page-subtitle">
        List of all PWDs who applied to your job postings
    </p>

    

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info">
            No applicants found for your job postings.
        </div>
    <?php else: ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>PWD Name</th>
                    <th>Disability</th>
                    <th>PWD Status</th>
                    <th>Application Status</th>
                    <th>Date Applied</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['job_title']) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['disability_category']) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($row['employment_status']) ?></strong>
                    </td>
                    <td><?= htmlspecialchars($row['application_status']) ?></td>
                    <td><?= htmlspecialchars($row['date_applied']) ?></td>
                    <td>
                        <a href="view_applicants.php?job_id=<?= (int)$row['job_id'] ?>"
                           class="btn btn-sm">
                            View Job
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>

    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>