<?php
require_once __DIR__ . '/config/page_guard.php';
requireLogin();

$go = $_POST['go'] ?? '';

switch ($go) {

    case 'PWD_JOBS':
        if ($_SESSION['role'] === 'PWD') {
            header('Location: pwd/view_jobs.php');
            exit;
        }
        break;

    case 'EMPLOYER_POST_JOB':
        if ($_SESSION['role'] === 'EMPLOYER') {
            header('Location: employer/post_job.php');
            exit;
        }
        break;

    case 'REGISTER_PWD':
        if ($_SESSION['role'] === 'ADMIN') {
            header('Location: admin/register_pwd.php');
            exit;
        }
        break;
}

http_response_code(403);
exit('Unauthorized action');