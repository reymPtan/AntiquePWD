<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

requireAdminRole(['SUPER']);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=PWD_Employment_System_COMPLETE_Report.xls");

/* =====================================================
   SECTION 1: PWD LIST
===================================================== */
echo "PWD LIST\n";
echo "PWD Number\tFull Name\tSex\tMunicipality\tDisability\tEmployment Status\n";

$res = $conn->query("
    SELECT pwd_number, full_name, sex, municipality,
           disability_category, employment_status
    FROM pwd_profiles
    ORDER BY municipality, full_name
");

while ($r = $res->fetch_assoc()) {
    echo "{$r['pwd_number']}\t{$r['full_name']}\t{$r['sex']}\t{$r['municipality']}\t{$r['disability_category']}\t{$r['employment_status']}\n";
}

echo "\n\n";

/* =====================================================
   SECTION 2: EMPLOYER LIST
===================================================== */
echo "EMPLOYER LIST\n";
echo "Business Permit No\tBusiness Name\tOwner\tMunicipality\tBusiness Type\tValid Until\n";

$res = $conn->query("
    SELECT business_permit_no, business_name, registered_owner,
           city_municipality, type_of_business, valid_until
    FROM employers
    ORDER BY city_municipality, business_name
");

while ($r = $res->fetch_assoc()) {
    echo "{$r['business_permit_no']}\t{$r['business_name']}\t{$r['registered_owner']}\t{$r['city_municipality']}\t{$r['type_of_business']}\t{$r['valid_until']}\n";
}

echo "\n\n";

/* =====================================================
   SECTION 3: JOB LIST (ALL JOBS)
===================================================== */
echo "JOB LIST (ALL JOB POSTS)\n";
echo "Job ID\tJob Title\tDisability Target\tMunicipality\tEmployer Permit\tJob Status\n";

$res = $conn->query("
    SELECT job_id, job_title, disability_target,
           job_location, business_permit_no, status
    FROM job_postings
    ORDER BY job_location, job_title
");

while ($r = $res->fetch_assoc()) {
    echo "{$r['job_id']}\t{$r['job_title']}\t{$r['disability_target']}\t{$r['job_location']}\t{$r['business_permit_no']}\t{$r['status']}\n";
}

echo "\n\n";

/* =====================================================
   SECTION 4: EMPLOYER + JOBS + APPLICATIONS + HIRED
===================================================== */
echo "EMPLOYER JOB PERFORMANCE\n";
echo "Business Permit\tBusiness Name\tMunicipality\tJob Title\tDisability Target\tJob Status\tApplicants\tHired\n";

$res = $conn->query("
    SELECT 
        e.business_permit_no,
        e.business_name,
        e.city_municipality,
        j.job_title,
        j.disability_target,
        j.status AS job_status,
        COUNT(a.application_id) AS total_applicants,
        SUM(CASE WHEN a.status = 'Accepted' THEN 1 ELSE 0 END) AS total_hired
    FROM employers e
    LEFT JOIN job_postings j ON e.business_permit_no = j.business_permit_no
    LEFT JOIN job_applications a ON j.job_id = a.job_id
    GROUP BY e.business_permit_no, j.job_id
    ORDER BY e.city_municipality, e.business_name, j.job_title
");

while ($r = $res->fetch_assoc()) {

    echo "{$r['business_permit_no']}\t";
    echo "{$r['business_name']}\t";
    echo "{$r['city_municipality']}\t";
    echo ($r['job_title'] ?? 'NO JOB POSTED') . "\t";
    echo ($r['disability_target'] ?? '-') . "\t";
    echo ($r['job_status'] ?? '-') . "\t";
    echo ($r['total_applicants'] ?? 0) . "\t";
    echo ($r['total_hired'] ?? 0) . "\n";
}

exit;