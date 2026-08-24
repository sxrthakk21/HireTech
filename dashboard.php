<?php

session_start();

require_once __DIR__ . '/config.php';

/*
|--------------------------------------------------------------------------
| Authentication Protection
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['admin_logged_in']) ||
    $_SESSION['admin_logged_in'] !== true
) {
    header('Location: login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Load JSON Data
|--------------------------------------------------------------------------
*/

$jobs = [];
$resumes = [];

if (file_exists(JOBS_FILE)) {
    $jobs = json_decode(
        file_get_contents(JOBS_FILE),
        true
    ) ?? [];
}

if (file_exists(RESUMES_FILE)) {
    $resumes = json_decode(
        file_get_contents(RESUMES_FILE),
        true
    ) ?? [];
}

/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

$totalJobs = count($jobs);

$totalCandidates = count($resumes);

$activeJobs = $totalJobs;

$parsedCandidates = 0;

$skillsDetected = 0;

foreach ($resumes as $resume) {

    if (!empty($resume['parsedText'])) {
        $parsedCandidates++;
    }

    if (!empty($resume['skills']) && is_array($resume['skills'])) {
        $skillsDetected += count($resume['skills']);
    }
}

/*
|--------------------------------------------------------------------------
| Recent Jobs
|--------------------------------------------------------------------------
*/

$recentJobs = array_slice(
    array_reverse($jobs),
    0,
    5
);

/*
|--------------------------------------------------------------------------
| Username
|--------------------------------------------------------------------------
*/

$username = $_SESSION['admin_username'] ?? 'Admin';

/*
|--------------------------------------------------------------------------
| Chart Data
|--------------------------------------------------------------------------
*/

$chartData = [
    'jobs' => $totalJobs,
    'candidates' => $totalCandidates,
    'parsed' => $parsedCandidates,
    'skills' => $skillsDetected
];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Dashboard | Intelligent Resume Screening
    </title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f6f9;
            font-family: Arial, sans-serif;
            color: #1f2937;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #111827;
            color: white;
            padding: 25px 15px;
            z-index: 1000;
        }

        .brand {
            padding: 0 15px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .brand h5 {
            font-weight: 700;
            margin: 0;
        }

        .brand small {
            color: #9ca3af;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #d1d5db;
            text-decoration: none;
            padding: 13px 15px;
            border-radius: 10px;
            margin-bottom: 6px;
            transition: 0.2s;
        }

        .nav-link-custom:hover {
            background: #1f2937;
            color: white;
        }

        .nav-link-custom.active {
            background: #2563eb;
            color: white;
        }

        .sidebar-bottom {
            position: absolute;
            bottom: 20px;
            left: 15px;
            right: 15px;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 260px;
            min-height: 100vh;
        }

        /* =========================
           TOPBAR
        ========================= */

        .topbar {
            background: white;
            padding: 18px 30px;
            border-bottom: 1px solid #e5e7eb;

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #2563eb;
            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: bold;
        }

        /* =========================
           CONTENT
        ========================= */

        .content {
            padding: 30px;
        }

        /* =========================
           STAT CARDS
        ========================= */

        .stat-card {
            background: white;
            border: none;
            border-radius: 15px;
            padding: 25px;

            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.04);

            transition: 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow:
                0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 52px;
            height: 52px;

            border-radius: 12px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 22px;

            background: #eff6ff;
            color: #2563eb;
        }

        .stat-number {
            font-size: 30px;
            font-weight: 700;
            margin-top: 5px;
        }

        /* =========================
           CARDS
        ========================= */

        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 25px;

            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.04);
        }

        /* =========================
           WORKFLOW
        ========================= */

        .workflow-step {
            position: relative;
            padding: 20px 10px;
        }

        .workflow-icon {
            width: 60px;
            height: 60px;

            margin: auto;

            border-radius: 50%;

            background: #eff6ff;
            color: #2563eb;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 25px;
        }

        .workflow-number {
            position: absolute;

            top: 5px;
            right: 25px;

            width: 25px;
            height: 25px;

            border-radius: 50%;

            background: #2563eb;
            color: white;

            font-size: 12px;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* =========================
           TABLE
        ========================= */

        .table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;

            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.04);
        }

        .table thead th {
            color: #6b7280;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-skill {
            background: #eef2ff;
            color: #3730a3;

            padding: 6px 9px;
            border-radius: 6px;

            font-size: 12px;
        }

        /* =========================
           CHART
        ========================= */

        .chart-container {
            height: 320px;
            position: relative;
        }

        /* =========================
           MOBILE
        ========================= */

        @media(max-width: 768px) {

            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }

            .brand h5,
            .brand small,
            .nav-text {
                display: none;
            }

            .brand {
                text-align: center;
            }

            .nav-link-custom {
                justify-content: center;
            }

            .main {
                margin-left: 70px;
            }

            .content {
                padding: 20px;
            }

            .topbar {
                padding: 15px 20px;
            }

        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: 700;
        }

        .brand img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }
    </style>

</head>

<body>


    <!-- =========================================================
     SIDEBAR
========================================================= -->

    <div class="sidebar">

        <div class="brand">
            <img src="assets/img/logo.png" alt="HIRETECH Logo">
            <span>HIRETECH</span>
        </div>

        <!-- Dashboard -->

        <a
            href="dashboard.php"
            class="nav-link-custom active">

            <i class="bi bi-speedometer2"></i>

            <span class="nav-text">
                Dashboard
            </span>

        </a>


        <!-- Upload Resumes -->

        <a
            href="views/upload-resumes.php"
            class="nav-link-custom">

            <i class="bi bi-file-earmark-arrow-up"></i>

            <span class="nav-text">
                Upload Resumes
            </span>

        </a>


        <!-- Ranking -->

        <a
            href="views/ranking.php"
            class="nav-link-custom">

            <i class="bi bi-trophy"></i>

            <span class="nav-text">
                Candidate Ranking
            </span>

        </a>


        <!-- Create Job -->

        <a
            href="views/create-job.php"
            class="nav-link-custom">

            <i class="bi bi-briefcase"></i>

            <span class="nav-text">
                Create Job
            </span>

        </a>


        <!-- Logout -->

        <div class="sidebar-bottom">

            <a
                href="logout.php"
                class="nav-link-custom text-danger">

                <i class="bi bi-box-arrow-right"></i>

                <span class="nav-text">
                    Logout
                </span>

            </a>

        </div>

    </div>


    <!-- =========================================================
     MAIN
========================================================= -->

    <div class="main">


        <!-- TOPBAR -->

        <div class="topbar">

            <div>

                <h5 class="mb-0 fw-bold">
                    Dashboard
                </h5>

                <small class="text-muted">
                    Intelligent Resume Screening System
                </small>

            </div>


            <div class="profile">

                <div class="text-end d-none d-sm-block">

                    <strong>
                        <?= htmlspecialchars($username) ?>
                    </strong>

                    <br>

                    <small class="text-muted">
                        Recruiter
                    </small>

                </div>


                <div class="avatar">

                    <?= strtoupper(
                        substr($username, 0, 1)
                    ) ?>

                </div>

            </div>

        </div>


        <!-- CONTENT -->

        <div class="content">


            <!-- WELCOME -->

            <div class="mb-4">

                <h2 class="fw-bold">

                    Welcome back,
                    <?= htmlspecialchars($username) ?>

                    👋

                </h2>

                <p class="text-muted">

                    Here's what's happening with your recruitment system.

                </p>

            </div>


            <!-- =================================================
             STATISTICS
        ================================================== -->

            <div class="row g-4 mb-4">


                <!-- TOTAL JOBS -->

                <div class="col-xl-3 col-md-6">

                    <div class="stat-card">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Total Jobs
                                </small>

                                <div class="stat-number">
                                    <?= $totalJobs ?>
                                </div>

                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i>
                                    Active job records
                                </small>

                            </div>

                            <div class="stat-icon">
                                <i class="bi bi-briefcase"></i>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- CANDIDATES -->

                <div class="col-xl-3 col-md-6">

                    <div class="stat-card">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Candidates
                                </small>

                                <div class="stat-number">
                                    <?= $totalCandidates ?>
                                </div>

                                <small class="text-primary">
                                    Resumes uploaded
                                </small>

                            </div>

                            <div class="stat-icon">
                                <i class="bi bi-people"></i>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- PARSED -->

                <div class="col-xl-3 col-md-6">

                    <div class="stat-card">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Parsed Resumes
                                </small>

                                <div class="stat-number">
                                    <?= $parsedCandidates ?>
                                </div>

                                <small class="text-success">
                                    Successfully processed
                                </small>

                            </div>

                            <div class="stat-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- SKILLS -->

                <div class="col-xl-3 col-md-6">

                    <div class="stat-card">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Skills Detected
                                </small>

                                <div class="stat-number">
                                    <?= $skillsDetected ?>
                                </div>

                                <small class="text-info">
                                    Across resumes
                                </small>

                            </div>

                            <div class="stat-icon">
                                <i class="bi bi-cpu"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             CHARTS
        ================================================== -->

            <div class="row g-4 mb-4">


                <!-- BAR CHART -->

                <div class="col-lg-8">

                    <div class="dashboard-card">

                        <div class="d-flex justify-content-between mb-3">

                            <div>

                                <h5 class="fw-bold mb-1">
                                    System Statistics
                                </h5>

                                <small class="text-muted">
                                    Recruitment system overview
                                </small>

                            </div>

                            <i class="bi bi-bar-chart fs-4 text-primary"></i>

                        </div>


                        <div class="chart-container">

                            <canvas id="statisticsChart"></canvas>

                        </div>

                    </div>

                </div>


                <!-- PIE CHART -->

                <div class="col-lg-4">

                    <div class="dashboard-card">

                        <h5 class="fw-bold mb-1">
                            Resume Processing
                        </h5>

                        <small class="text-muted">
                            Parsed vs pending resumes
                        </small>

                        <div class="chart-container mt-3">

                            <canvas id="resumeChart"></canvas>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             QUICK ACTIONS + WORKFLOW
        ================================================== -->

            <div class="row g-4 mb-4">


                <!-- QUICK ACTIONS -->

                <div class="col-lg-4">

                    <div class="dashboard-card h-100">

                        <h5 class="fw-bold">
                            Quick Actions
                        </h5>

                        <p class="text-muted">
                            Start your recruitment workflow.
                        </p>


                        <div class="d-grid gap-2">


                            <a
                                href="index.php"
                                class="btn btn-primary">

                                <i class="bi bi-plus-circle"></i>

                                Create Job

                            </a>


                            <a
                                href="views/upload-resumes.php"
                                class="btn btn-outline-primary">

                                <i class="bi bi-upload"></i>

                                Upload Resumes

                            </a>


                            <a
                                href="views/ranking.php"
                                class="btn btn-outline-dark">

                                <i class="bi bi-trophy"></i>

                                Rank Candidates

                            </a>


                        </div>

                    </div>

                </div>


                <!-- WORKFLOW -->

                <div class="col-lg-8">

                    <div class="dashboard-card">

                        <h5 class="fw-bold">
                            Screening Workflow
                        </h5>

                        <p class="text-muted">
                            Follow these steps to screen candidates.
                        </p>


                        <div class="row text-center">


                            <!-- STEP 1 -->

                            <div class="col-md-3">

                                <div class="workflow-step">

                                    <span class="workflow-number">
                                        1
                                    </span>

                                    <div class="workflow-icon">
                                        <i class="bi bi-briefcase"></i>
                                    </div>

                                    <strong class="d-block mt-3">
                                        Create Job
                                    </strong>

                                    <small class="text-muted">
                                        Add JD & skills
                                    </small>

                                </div>

                            </div>


                            <!-- STEP 2 -->

                            <div class="col-md-3">

                                <div class="workflow-step">

                                    <span class="workflow-number">
                                        2
                                    </span>

                                    <div class="workflow-icon">
                                        <i class="bi bi-upload"></i>
                                    </div>

                                    <strong class="d-block mt-3">
                                        Upload
                                    </strong>

                                    <small class="text-muted">
                                        Add resumes
                                    </small>

                                </div>

                            </div>


                            <!-- STEP 3 -->

                            <div class="col-md-3">

                                <div class="workflow-step">

                                    <span class="workflow-number">
                                        3
                                    </span>

                                    <div class="workflow-icon">
                                        <i class="bi bi-bar-chart"></i>
                                    </div>

                                    <strong class="d-block mt-3">
                                        Rank
                                    </strong>

                                    <small class="text-muted">
                                        Calculate scores
                                    </small>

                                </div>

                            </div>


                            <!-- STEP 4 -->

                            <div class="col-md-3">

                                <div class="workflow-step">

                                    <span class="workflow-number">
                                        4
                                    </span>

                                    <div class="workflow-icon">
                                        <i class="bi bi-lightbulb"></i>
                                    </div>

                                    <strong class="d-block mt-3">
                                        Explain
                                    </strong>

                                    <small class="text-muted">
                                        Review matches
                                    </small>

                                </div>

                            </div>


                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             RECENT JOBS
        ================================================== -->

            <div class="table-card">


                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h5 class="fw-bold mb-1">
                            Recent Jobs
                        </h5>

                        <small class="text-muted">
                            Latest job descriptions added to the system
                        </small>

                    </div>


                    <a
                        href="index.php"
                        class="btn btn-sm btn-primary">

                        <i class="bi bi-plus"></i>

                        New Job

                    </a>

                </div>


                <?php if (empty($recentJobs)): ?>


                    <div class="text-center py-5">

                        <i
                            class="bi bi-briefcase"
                            style="font-size:50px;color:#9ca3af;"></i>

                        <h5 class="mt-3">
                            No jobs created yet
                        </h5>

                        <p class="text-muted">
                            Create your first job description.
                        </p>

                        <a
                            href="index.php"
                            class="btn btn-primary">
                            Create Job
                        </a>

                    </div>


                <?php else: ?>


                    <div class="table-responsive">

                        <table class="table align-middle">

                            <thead>

                                <tr>

                                    <th>
                                        Job ID
                                    </th>

                                    <th>
                                        Job Title
                                    </th>

                                    <th>
                                        Skills
                                    </th>

                                    <th>
                                        Words
                                    </th>

                                    <th>
                                        Created
                                    </th>

                                    <th>
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                                <?php foreach ($recentJobs as $job): ?>


                                    <tr>


                                        <td>

                                            <code>
                                                <?= htmlspecialchars(
                                                    $job['jobId'] ?? ''
                                                ) ?>
                                            </code>

                                        </td>


                                        <td>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    $job['title'] ?? ''
                                                ) ?>

                                            </strong>

                                        </td>


                                        <td>

                                            <?php

                                            $jobSkills =
                                                $job['skills'] ?? [];

                                            ?>


                                            <?php if (empty($jobSkills)): ?>

                                                <span class="text-muted">
                                                    None
                                                </span>

                                            <?php else: ?>


                                                <?php foreach (
                                                    array_slice(
                                                        $jobSkills,
                                                        0,
                                                        3
                                                    ) as $skill
                                                ): ?>

                                                    <span
                                                        class="badge-skill me-1">

                                                        <?= htmlspecialchars(
                                                            $skill
                                                        ) ?>

                                                    </span>

                                                <?php endforeach; ?>


                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <?= number_format(
                                                $job['wordCount'] ?? 0
                                            ) ?>

                                        </td>


                                        <td>

                                            <small>
                                                <?= htmlspecialchars(
                                                    $job['createdAt'] ?? ''
                                                ) ?>
                                            </small>

                                        </td>


                                        <td>

                                            <a
                                                href="views/ranking.php?jobId=<?= urlencode(
                                                                                    $job['jobId'] ?? ''
                                                                                ) ?>"
                                                class="btn btn-sm btn-outline-primary">

                                                <i class="bi bi-trophy"></i>

                                                Rank

                                            </a>

                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                            </tbody>

                        </table>

                    </div>


                <?php endif; ?>


            </div>


        </div>

    </div>


    <!-- =========================================================
     CHART JAVASCRIPT
========================================================= -->

    <script>
        const totalJobs =
            <?= (int)$totalJobs ?>;

        const totalCandidates =
            <?= (int)$totalCandidates ?>;

        const parsedCandidates =
            <?= (int)$parsedCandidates ?>;

        const skillsDetected =
            <?= (int)$skillsDetected ?>;


        /*
        |--------------------------------------------------------------------------
        | Statistics Bar Chart
        |--------------------------------------------------------------------------
        */

        const statisticsCanvas =
            document.getElementById(
                'statisticsChart'
            );

        new Chart(
            statisticsCanvas, {
                type: 'bar',

                data: {

                    labels: [
                        'Jobs',
                        'Candidates',
                        'Parsed Resumes',
                        'Skills'
                    ],

                    datasets: [

                        {
                            label: 'System Statistics',

                            data: [
                                totalJobs,
                                totalCandidates,
                                parsedCandidates,
                                skillsDetected
                            ],

                            borderWidth: 1,

                            borderRadius: 8

                        }

                    ]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    plugins: {

                        legend: {
                            display: false
                        }

                    },

                    scales: {

                        y: {

                            beginAtZero: true,

                            ticks: {
                                precision: 0
                            }

                        }

                    }

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Resume Processing Pie Chart
        |--------------------------------------------------------------------------
        */

        const pendingResumes =
            Math.max(
                totalCandidates - parsedCandidates,
                0
            );


        const resumeCanvas =
            document.getElementById(
                'resumeChart'
            );


        new Chart(
            resumeCanvas, {

                type: 'doughnut',

                data: {

                    labels: [
                        'Parsed',
                        'Pending'
                    ],

                    datasets: [

                        {

                            data: [
                                parsedCandidates,
                                pendingResumes
                            ],

                            borderWidth: 2

                        }

                    ]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    cutout: '65%',

                    plugins: {

                        legend: {

                            position: 'bottom'

                        }

                    }

                }

            }
        );
    </script>


    <!-- Bootstrap JS -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>