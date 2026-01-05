<?php
// =====================================================
// pwd/view_jobs.php
// FULL controller for viewing available jobs (PWD)
// =====================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requirePwdLogin();

// -----------------------------------------------------
// SESSION DATA
// -----------------------------------------------------
$pwd_number = $_SESSION['pwd_number'] ?? '';
$disability = $_SESSION['disability_category'] ?? '';
$full_name  = $_SESSION['full_name'] ?? '';

// SAFETY CHECK
if ($pwd_number === '' || $disability === '') {
    die('Session error. Please login again.');
}

// -----------------------------------------------------
// PAGE META
// -----------------------------------------------------
$pageTitle = 'Available Jobs';

// -----------------------------------------------------
// LOAD PWD PROFILE (employment status)
// -----------------------------------------------------
$sqlPwd = "SELECT employment_status FROM pwd_profiles WHERE pwd_number = ?";
$stmtPwd = $conn->prepare($sqlPwd);
$stmtPwd->bind_param('s', $pwd_number);
$stmtPwd->execute();
$resPwd = $stmtPwd->get_result();
$pwd = $resPwd->fetch_assoc();
$stmtPwd->close();

// Default if not found
if (!$pwd) {
    $pwd = ['employment_status' => 'Unemployed'];
}

// -----------------------------------------------------
// LOAD PWD SKILLS
// -----------------------------------------------------
$pwdSkillIds = [];

$sqlSkills = "SELECT skill_id FROM pwd_skills WHERE pwd_number = ?";
$stmtSkills = $conn->prepare($sqlSkills);
$stmtSkills->bind_param('s', $pwd_number);
$stmtSkills->execute();
$resSkills = $stmtSkills->get_result();

while ($row = $resSkills->fetch_assoc()) {
    $pwdSkillIds[] = (int)$row['skill_id'];
}
$stmtSkills->close();

// -----------------------------------------------------
// LOAD JOBS (OPEN + MATCH DISABILITY)
// -----------------------------------------------------
$jobs = [];

$sqlJobs = "
    SELECT
        j.job_id,
        j.job_title,
        j.job_description,
        j.job_location,
        j.salary_range,
        j.application_deadline,
        j.status,
        e.business_name
    FROM job_postings j
    JOIN employers e
        ON j.business_permit_no = e.business_permit_no
    WHERE j.status = 'Open'
      AND j.disability_target = ?
      AND (
            j.application_deadline IS NULL
            OR j.application_deadline = ''
            OR j.application_deadline >= CURDATE()
      )
    ORDER BY j.date_posted DESC
";

$stmtJobs = $conn->prepare($sqlJobs);
$stmtJobs->bind_param('s', $disability);
$stmtJobs->execute();
$resJobs = $stmtJobs->get_result();

while ($job = $resJobs->fetch_assoc()) {

    $jobId = (int)$job['job_id'];

    // ---------------------------------------------
    // LOAD REQUIRED SKILLS FOR JOB
    // ---------------------------------------------
    $jobSkillIds   = [];
    $jobSkillNames = [];

    $sqlReq = "
        SELECT sm.skill_id, sm.skill_name
        FROM job_required_skills jr
        JOIN skills_master sm ON jr.skill_id = sm.skill_id
        WHERE jr.job_id = ?
    ";
    $stmtReq = $conn->prepare($sqlReq);
    $stmtReq->bind_param('i', $jobId);
    $stmtReq->execute();
    $resReq = $stmtReq->get_result();

    while ($r = $resReq->fetch_assoc()) {
        $jobSkillIds[]   = (int)$r['skill_id'];
        $jobSkillNames[] = $r['skill_name'];
    }
    $stmtReq->close();

    // ---------------------------------------------
    // SMART MATCH LOGIC
    // ---------------------------------------------
    $requiresCount = count($jobSkillIds);
    $matchCount    = 0;

    if ($requiresCount > 0 && !empty($pwdSkillIds)) {
        $matchCount = count(array_intersect($jobSkillIds, $pwdSkillIds));
    }

    // ❌ HIDE JOB IF SKILLS REQUIRED BUT NO MATCH
    if ($requiresCount > 0 && $matchCount === 0) {
        continue;
    }

    // ADD COMPUTED VALUES
    $job['requires_skills'] = $requiresCount;
    $job['match_skills']    = $matchCount;
    $job['required_names'] = $jobSkillNames;

    $jobs[] = $job;
}
$stmtJobs->close();

// -----------------------------------------------------
// SORT JOBS BY BEST MATCH
// -----------------------------------------------------
usort($jobs, function ($a, $b) {
    return ($b['match_skills'] ?? 0) <=> ($a['match_skills'] ?? 0);
});

// -----------------------------------------------------
// LOAD HEADER
// -----------------------------------------------------
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ===================================================
     CONTENT
=================================================== -->

<section class="card">

    <h1 class="page-title" tabindex="0">
        Available Jobs
    </h1>

    <?php if ($pwd['employment_status'] === 'Employed'): ?>
        <div class="alert alert-info" tabindex="0">
            You are currently employed.
            You cannot apply for new jobs until your contract ends.
        </div>
    <?php endif; ?>

    <?php if (empty($jobs)): ?>
        <div class="alert alert-warning" tabindex="0">
            No available jobs matched your profile at this time.
        </div>
    <?php else: ?>

        <?php
        // LOAD DISABILITY VIEW
        if ($disability === 'Blind') {
            require __DIR__ . '/views/blind/view_jobs_view.php';
        } elseif ($disability === 'Deaf') {
            require __DIR__ . '/views/deaf/view_jobs_view.php';
        } else {
            require __DIR__ . '/views/physical/view_jobs_view.php';
        }
        ?>

    <?php endif; ?>

</section>

<?php
// -----------------------------------------------------
// FOOTER
// -----------------------------------------------------
require_once __DIR__ . '/../includes/footer.php';