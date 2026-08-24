<?php

header(
    'Content-Type: application/json'
);


require_once __DIR__ .
    '/../../../config.php';

require_once __DIR__ .
    '/../../../services/JsonStorage.php';

require_once __DIR__ .
    '/../../../services/RankingService.php';


/*
 * Only GET is allowed.
 */

if (
    $_SERVER['REQUEST_METHOD']
    !== 'GET'
) {

    http_response_code(405);

    echo json_encode([

        'success' => false,

        'message' =>
        'Only GET method is allowed'

    ]);

    exit;
}


/*
 * Get Job ID.
 */

$jobId =
    $_GET['jobId'] ?? '';


if ($jobId === '') {

    http_response_code(400);

    echo json_encode([

        'success' => false,

        'message' =>
        'Job ID is required'

    ]);

    exit;
}


/*
 * Read jobs.
 */

$jobs =
    JsonStorage::read(
        JOBS_FILE
    );


/*
 * Find requested job.
 */

$job = null;


foreach ($jobs as $item) {

    if (
        $item['jobId']
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

        'message' =>
        'Job not found'

    ]);

    exit;
}


/*
 * Read candidates.
 */

$candidates =
    JsonStorage::read(
        RESUMES_FILE
    );


if (empty($candidates)) {

    echo json_encode([

        'success' => true,

        'jobId' =>
        $jobId,

        'message' =>
        'No candidates available',

        'ranking' => []

    ], JSON_PRETTY_PRINT);

    exit;
}


/*
 * Ranking service.
 */

$rankingService =
    new RankingService();


$ranking = [];


foreach (
    $candidates
    as $candidate
) {

    $score =
        $rankingService
        ->scoreCandidate(
            $job,
            $candidate
        );


    $ranking[] = [

        'candidateId' =>
        $candidate['candidateId'],

        'candidateName' =>
        pathinfo(
            $candidate['originalName'],
            PATHINFO_FILENAME
        ),

        'resumeFile' =>
        $candidate['originalName'],

        'semanticScore' =>
        $score['semanticScore'],

        'skillScore' =>
        $score['skillScore'],

        'experienceScore' =>
        $score['experienceScore'],

        'keywordPenalty' =>
        $score['keywordPenalty'],

        'matchedSkills' =>
        $score['matchedSkills'],

        'missingSkills' =>
        $score['missingSkills'],

        'finalScore' =>
        $score['finalScore']

    ];
}


/*
 * Sort highest score first.
 */

usort(
    $ranking,
    function (
        $a,
        $b
    ) {

        return
            $b['finalScore']
            <=>
            $a['finalScore'];
    }
);


/*
 * Assign rank.
 */

$rank = 1;


foreach (
    $ranking
    as &$candidate
) {

    $candidate['rank'] =
        $rank;

    $rank++;
}


echo json_encode([

    'success' => true,

    'jobId' =>
    $jobId,

    'jobTitle' =>
    $job['title'],

    'totalCandidates' =>
    count($ranking),

    'ranking' =>
    $ranking

], JSON_PRETTY_PRINT);
