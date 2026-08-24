<?php

header('Content-Type: application/json');

require_once '../../../config.php';
require_once '../../../services/JsonStorage.php';
require_once '../../../services/IdGenerator.php';
require_once '../../../services/JobParser.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Only POST method is allowed'
    ]);

    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON request'
    ]);

    exit;
}

$title = trim($input['title'] ?? '');
$description = trim($input['description'] ?? '');

if ($title === '' || $description === '') {

    http_response_code(422);

    echo json_encode([
        'success' => false,
        'message' => 'Job title and description are required'
    ]);

    exit;
}

$parser = new JobParser();

$analysis = $parser->analyze($description);

$job = [
    'jobId' => IdGenerator::job(),
    'title' => $title,
    'description' => $description,
    'skills' => $analysis['requiredSkills'],
    'wordCount' => $analysis['wordCount'],
    'createdAt' => date('Y-m-d H:i:s')
];

JsonStorage::add(JOBS_FILE, $job);

echo json_encode([
    'success' => true,
    'message' => 'Job created successfully',
    'job' => $job
], JSON_PRETTY_PRINT);
