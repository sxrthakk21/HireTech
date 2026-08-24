<?php

header('Content-Type: application/json');


require_once __DIR__ . '/../../../config.php';

require_once __DIR__ . '/../../../services/JsonStorage.php';

require_once __DIR__ . '/../../../services/RankingService.php';


/*
|--------------------------------------------------------------------------
| Request Method
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Only GET method is allowed'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Candidate ID
|--------------------------------------------------------------------------
*/

$candidateId =
    $_GET['candidateId'] ?? '';


if ($candidateId === '') {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Candidate ID is required'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Optional Job ID
|--------------------------------------------------------------------------
|
| We recommend passing jobId because the same candidate
| can be evaluated against multiple jobs.
|
*/

$jobId =
    $_GET['jobId'] ?? '';


if ($jobId === '') {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Job ID is required'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Read candidates
|--------------------------------------------------------------------------
*/

$candidates =
    JsonStorage::read(
        RESUMES_FILE
    );


/*
|--------------------------------------------------------------------------
| Find candidate
|--------------------------------------------------------------------------
*/

$candidate = null;


foreach ($candidates as $item) {

    if (
        ($item['candidateId'] ?? '')
        ===
        $candidateId
    ) {

        $candidate = $item;

        break;
    }
}


if ($candidate === null) {

    http_response_code(404);

    echo json_encode([
        'success' => false,
        'message' => 'Candidate not found'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Read jobs
|--------------------------------------------------------------------------
*/

$jobs =
    JsonStorage::read(
        JOBS_FILE
    );


/*
|--------------------------------------------------------------------------
| Find job
|--------------------------------------------------------------------------
*/

$job = null;


foreach ($jobs as $item) {

    if (
        ($item['jobId'] ?? '')
        ===
        $jobId
    ) {

        $job = $item;

        break;
    }
}


if ($job === null) {

    http_response_code(404);

    echo json_encode([
        'success' => false,
        'message' => 'Job not found'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Calculate score
|--------------------------------------------------------------------------
*/

$rankingService =
    new RankingService();


$score =
    $rankingService->scoreCandidate(
        $job,
        $candidate
    );


/*
|--------------------------------------------------------------------------
| Generate recruiter justification
|--------------------------------------------------------------------------
*/

$matchedCount =
    count(
        $score['matchedSkills']
    );


$missingCount =
    count(
        $score['missingSkills']
    );


if (
    $score['finalScore'] >= 85
) {

    $recommendation =
        'Strong Match';
} elseif (
    $score['finalScore'] >= 70
) {

    $recommendation =
        'Good Match';
} elseif (
    $score['finalScore'] >= 50
) {

    $recommendation =
        'Moderate Match';
} else {

    $recommendation =
        'Low Match';
}


/*
|--------------------------------------------------------------------------
| Justification
|--------------------------------------------------------------------------
*/

$justification =
    "Candidate matches " .
    $matchedCount .
    " required skill(s).";


if ($missingCount > 0) {

    $justification .=
        " " .
        $missingCount .
        " required skill(s) are missing.";
}


if (
    $score['keywordPenalty'] > 0
) {

    $justification .=
        " A keyword-stuffing penalty of " .
        $score['keywordPenalty'] .
        " points was applied.";
}


if (
    $score['experienceScore'] >= 80
) {

    $justification .=
        " The candidate has a strong amount of relevant experience information.";
} elseif (
    $score['experienceScore'] >= 60
) {

    $justification .=
        " The candidate has moderate experience information.";
} else {

    $justification .=
        " Limited experience information was detected.";
}


/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

echo json_encode([

    'success' => true,

    'candidate' => [

        'candidateId' =>
        $candidate['candidateId'],

        'name' =>
        pathinfo(
            $candidate['originalName'],
            PATHINFO_FILENAME
        ),

        'resumeFile' =>
        $candidate['originalName']

    ],

    'job' => [

        'jobId' =>
        $job['jobId'],

        'title' =>
        $job['title']

    ],

    'score' => [

        'finalScore' =>
        $score['finalScore'],

        'semanticScore' =>
        $score['semanticScore'],

        'skillScore' =>
        $score['skillScore'],

        'experienceScore' =>
        $score['experienceScore'],

        'keywordPenalty' =>
        $score['keywordPenalty']

    ],

    'skills' => [

        'matched' =>
        $score['matchedSkills'],

        'missing' =>
        $score['missingSkills']

    ],

    'recommendation' =>
    $recommendation,

    'justification' =>
    $justification,

    'experience' =>
    $candidate['experience'] ?? '',

    'education' =>
    $candidate['education'] ?? ''

], JSON_PRETTY_PRINT);
