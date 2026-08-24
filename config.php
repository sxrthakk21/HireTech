<?php

/*
|--------------------------------------------------------------------------
| START SESSION
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| BASE PATH
|--------------------------------------------------------------------------
*/

define(
    'BASE_PATH',
    __DIR__
);


/*
|--------------------------------------------------------------------------
| STORAGE PATH
|--------------------------------------------------------------------------
*/

define(
    'STORAGE_PATH',
    BASE_PATH . '/storage/'
);


/*
|--------------------------------------------------------------------------
| UPLOAD PATH
|--------------------------------------------------------------------------
*/

define(
    'UPLOAD_PATH',
    BASE_PATH . '/uploads/resumes/'
);


/*
|--------------------------------------------------------------------------
| JSON FILES
|--------------------------------------------------------------------------
*/

define(
    'JOBS_FILE',
    STORAGE_PATH . 'jobs.json'
);

define(
    'RESUMES_FILE',
    STORAGE_PATH . 'resumes.json'
);

define(
    'RESULTS_FILE',
    STORAGE_PATH . 'results.json'
);


/*
|--------------------------------------------------------------------------
| ADMIN LOGIN
|--------------------------------------------------------------------------
*/

define(
    'ADMIN_USERNAME',
    'admin'
);

define(
    'ADMIN_PASSWORD',
    'admin123'
);


/*
|--------------------------------------------------------------------------
| CREATE STORAGE DIRECTORY
|--------------------------------------------------------------------------
*/

if (!is_dir(STORAGE_PATH)) {

    mkdir(
        STORAGE_PATH,
        0777,
        true
    );
}


/*
|--------------------------------------------------------------------------
| CREATE UPLOAD DIRECTORY
|--------------------------------------------------------------------------
*/

if (!is_dir(UPLOAD_PATH)) {

    mkdir(
        UPLOAD_PATH,
        0777,
        true
    );
}


/*
|--------------------------------------------------------------------------
| CREATE JSON FILES
|--------------------------------------------------------------------------
*/

if (!file_exists(JOBS_FILE)) {

    file_put_contents(
        JOBS_FILE,
        json_encode(
            [],
            JSON_PRETTY_PRINT
        )
    );
}


if (!file_exists(RESUMES_FILE)) {

    file_put_contents(
        RESUMES_FILE,
        json_encode(
            [],
            JSON_PRETTY_PRINT
        )
    );
}


if (!file_exists(RESULTS_FILE)) {

    file_put_contents(
        RESULTS_FILE,
        json_encode(
            [],
            JSON_PRETTY_PRINT
        )
    );
}
