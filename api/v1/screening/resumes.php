<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../../config.php';

require_once __DIR__ . '/../../../services/JsonStorage.php';

require_once __DIR__ . '/../../../services/IdGenerator.php';

require_once __DIR__ . '/../../../services/ResumeParser.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Only POST method is allowed'
    ]);

    exit;
}


if (!isset($_FILES['resumes'])) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'No resumes uploaded'
    ]);

    exit;
}


$files = $_FILES['resumes'];


$allowedExtensions = [
    'pdf',
    'docx'
];


$uploadedCandidates = [];

$errors = [];


$parser = new ResumeParser();


for (
    $i = 0;
    $i < count($files['name']);
    $i++
) {

    $originalName =
        $files['name'][$i];

    $tmpName =
        $files['tmp_name'][$i];

    $error =
        $files['error'][$i];

    $size =
        $files['size'][$i];


    /*
     * Upload error
     */

    if (
        $error !==
        UPLOAD_ERR_OK
    ) {

        $errors[] = [

            'file' =>
            $originalName,

            'message' =>
            'Upload failed'

        ];

        continue;
    }


    /*
     * Extension
     */

    $extension =
        strtolower(
            pathinfo(
                $originalName,
                PATHINFO_EXTENSION
            )
        );


    /*
     * Validate extension
     */

    if (
        !in_array(
            $extension,
            $allowedExtensions
        )
    ) {

        $errors[] = [

            'file' =>
            $originalName,

            'message' =>
            'Only PDF and DOCX files are allowed'

        ];

        continue;
    }


    /*
     * Validate size
     */

    if (
        $size >
        5 * 1024 * 1024
    ) {

        $errors[] = [

            'file' =>
            $originalName,

            'message' =>
            'Maximum file size is 5 MB'

        ];

        continue;
    }


    /*
     * Generate candidate ID
     */

    $candidateId =
        IdGenerator::candidate();


    /*
     * Generate safe filename
     */

    $newFileName =
        $candidateId .
        '.' .
        $extension;


    $destination =
        UPLOAD_PATH .
        $newFileName;


    /*
     * Move uploaded file
     */

    if (
        !move_uploaded_file(
            $tmpName,
            $destination
        )
    ) {

        $errors[] = [

            'file' =>
            $originalName,

            'message' =>
            'Could not save file'

        ];

        continue;
    }


    /*
     * Extract resume text
     */

    $resumeText =
        $parser->extractText(
            $destination,
            $extension
        );


    /*
     * Analyze resume
     */

    $analysis =
        $parser->analyze(
            $resumeText
        );


    /*
     * Candidate object
     */

    $candidate = [

        'candidateId' =>
        $candidateId,

        'originalName' =>
        $originalName,

        'fileName' =>
        $newFileName,

        'fileType' =>
        $extension,

        'fileSize' =>
        $size,

        'resumeText' =>
        $resumeText,

        'skills' =>
        $analysis['skills'],

        'experience' =>
        $analysis['experience'],

        'education' =>
        $analysis['education'],

        'wordCount' =>
        $analysis['wordCount'],

        'uploadedAt' =>
        date(
            'Y-m-d H:i:s'
        )

    ];


    /*
     * Save candidate
     */

    JsonStorage::add(
        RESUMES_FILE,
        $candidate
    );


    $uploadedCandidates[] =
        $candidate;
}


/*
 * Response
 */

echo json_encode([

    'success' => true,

    'message' =>
    count(
        $uploadedCandidates
    ) .
        ' resume(s) processed successfully',

    'uploaded' =>
    count(
        $uploadedCandidates
    ),

    'candidates' =>
    $uploadedCandidates,

    'errors' =>
    $errors

], JSON_PRETTY_PRINT);
