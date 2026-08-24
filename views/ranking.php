<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Candidate Ranking</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .navbar {
            background: #17202a;
        }

        .dashboard-card {
            border: none;
            border-radius: 15px;
        }

        .rank-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #212529;
            color: white;
            font-weight: bold;
        }

        .skill-badge {
            margin: 2px;
        }

        .score-card {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .table th {
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .loading {
            padding: 40px;
            text-align: center;
        }
    </style>

</head>


<body>


    <!-- =========================================================
     NAVBAR
========================================================= -->

    <nav class="navbar navbar-dark">

        <div class="container">

            <a
                class="navbar-brand fw-bold"
                href="../index.php">
                Intelligent Resume Screening
            </a>

        </div>

    </nav>


    <!-- =========================================================
     MAIN CONTAINER
========================================================= -->

    <div class="container py-5">


        <!-- HEADER -->

        <div class="row mb-4">

            <div class="col-md-8">

                <h2 class="fw-bold">
                    Candidate Ranking
                </h2>

                <p class="text-muted">
                    Rank candidates against a Job Description
                </p>

            </div>

        </div>


        <!-- =====================================================
         JOB ID INPUT
    ====================================================== -->

        <div class="card dashboard-card shadow-sm mb-4">

            <div class="card-body">

                <div class="row align-items-end">

                    <div class="col-md-8">

                        <label
                            for="jobId"
                            class="form-label fw-semibold">
                            Job ID
                        </label>

                        <input
                            type="text"
                            id="jobId"
                            class="form-control"
                            placeholder="Example: JOB-ABC123">

                    </div>


                    <div class="col-md-4 mt-3 mt-md-0">

                        <button
                            type="button"
                            id="rankButton"
                            class="btn btn-primary w-100"
                            onclick="loadRanking()">
                            Rank Candidates
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- MESSAGE -->

        <div id="message"></div>


        <!-- JOB INFO -->

        <div
            id="jobInfo"
            class="mb-4"></div>


        <!-- =====================================================
         RANKING TABLE
    ====================================================== -->

        <div
            id="rankingContainer"
            class="card dashboard-card shadow-sm">

            <div class="card-body">

                <div class="text-center text-muted py-5">

                    Enter a Job ID to see candidate ranking.

                </div>

            </div>

        </div>


    </div>


    <!-- =========================================================
     EXPLANATION MODAL
========================================================= -->

    <div
        class="modal fade"
        id="explainModal"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-scrollable">

            <div class="modal-content">


                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="explainTitle">
                        Candidate Explanation
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>


                <div
                    class="modal-body"
                    id="explainBody">
                </div>


            </div>

        </div>

    </div>


    <!-- Bootstrap JS -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        /*
|--------------------------------------------------------------------------
| API BASE PATH
|--------------------------------------------------------------------------
|
| ranking.php is inside:
|
| views/ranking.php
|
| APIs are inside:
|
| api/v1/screening/
|
*/

        const API_BASE = '../api/v1/screening/';

        /*
        |--------------------------------------------------------------------------
        | Load Job ID From URL
        |--------------------------------------------------------------------------
        */

        const urlParams =
            new URLSearchParams(
                window.location.search
            );

        const urlJobId =
            urlParams.get('jobId');


        if (urlJobId) {

            document
                .getElementById('jobId')
                .value = urlJobId;

        }

        /*
        |--------------------------------------------------------------------------
        | Load Ranking
        |--------------------------------------------------------------------------
        */

        async function loadRanking() {

            const jobId =
                document
                .getElementById('jobId')
                .value
                .trim();


            /*
            |--------------------------------------------------------------------------
            | Validate Job ID
            |--------------------------------------------------------------------------
            */

            if (jobId === '') {

                showMessage(
                    'Please enter a Job ID.',
                    'warning'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Disable button
            |--------------------------------------------------------------------------
            */

            const button =
                document.getElementById(
                    'rankButton'
                );

            button.disabled = true;

            button.innerHTML =
                'Loading...';


            showMessage(
                'Loading candidate ranking...',
                'info'
            );


            /*
            |--------------------------------------------------------------------------
            | Clear old data
            |--------------------------------------------------------------------------
            */

            document
                .getElementById('jobInfo')
                .innerHTML = '';


            document
                .getElementById('rankingContainer')
                .innerHTML = `

            <div class="loading">

                <div
                    class="spinner-border text-primary mb-3"
                ></div>

                <div>
                    Calculating candidate ranking...
                </div>

            </div>

        `;


            try {


                /*
                |--------------------------------------------------------------------------
                | IMPORTANT
                |--------------------------------------------------------------------------
                |
                | Actual PHP file:
                |
                | api/v1/screening/rank.php
                |
                */

                const url =
                    API_BASE +
                    'rank.php?jobId=' +
                    encodeURIComponent(jobId);


                console.log(
                    'Ranking API:',
                    url
                );


                const response =
                    await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });


                /*
                |--------------------------------------------------------------------------
                | Check HTTP response
                |--------------------------------------------------------------------------
                */

                if (!response.ok) {

                    throw new Error(
                        'HTTP Error: ' +
                        response.status
                    );
                }


                const data =
                    await response.json();


                console.log(
                    'Ranking Response:',
                    data
                );


                /*
                |--------------------------------------------------------------------------
                | API Error
                |--------------------------------------------------------------------------
                */

                if (!data.success) {

                    showMessage(
                        data.message ||
                        'Unable to load ranking.',
                        'danger'
                    );

                    document
                        .getElementById(
                            'rankingContainer'
                        )
                        .innerHTML = '';

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Success
                |--------------------------------------------------------------------------
                */

                document
                    .getElementById('message')
                    .innerHTML = '';


                showJobInfo(data);

                showRanking(data);


            } catch (error) {

                console.error(
                    'Ranking Error:',
                    error
                );


                showMessage(
                    'Unable to connect to the ranking API. ' +
                    'Check that rank.php exists and Apache is running.',
                    'danger'
                );


                document
                    .getElementById(
                        'rankingContainer'
                    )
                    .innerHTML = `

                <div class="card-body text-center py-5">

                    <h5 class="text-danger">
                        Ranking failed
                    </h5>

                    <p class="text-muted">
                        ${escapeHtml(
                            error.message
                        )}
                    </p>

                </div>

            `;

            } finally {

                button.disabled = false;

                button.innerHTML =
                    'Rank Candidates';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Show Job Information
        |--------------------------------------------------------------------------
        */

        function showJobInfo(data) {

            document
                .getElementById('jobInfo')
                .innerHTML = `

            <div class="card dashboard-card shadow-sm">

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col-md-8">

                            <h4 class="fw-bold mb-1">

                                ${escapeHtml(
                                    data.jobTitle ||
                                    'Job'
                                )}

                            </h4>

                            <span class="text-muted">

                                Job ID:
                                ${escapeHtml(
                                    data.jobId
                                )}

                            </span>

                        </div>


                        <div class="col-md-4 text-md-end mt-3 mt-md-0">

                            <div class="text-muted">
                                Total Candidates
                            </div>

                            <h3 class="fw-bold mb-0">

                                ${Number(
                                    data.totalCandidates || 0
                                )}

                            </h3>

                        </div>

                    </div>

                </div>

            </div>

        `;

        }


        /*
        |--------------------------------------------------------------------------
        | Show Ranking
        |--------------------------------------------------------------------------
        */

        function showRanking(data) {

            const container =
                document.getElementById(
                    'rankingContainer'
                );


            /*
            |--------------------------------------------------------------------------
            | No candidates
            |--------------------------------------------------------------------------
            */

            if (
                !Array.isArray(data.ranking) ||
                data.ranking.length === 0
            ) {

                container.innerHTML = `

            <div class="card-body text-center py-5">

                <h5>
                    No candidates found
                </h5>

                <p class="text-muted mb-0">
                    Upload resumes first.
                </p>

            </div>

        `;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Build table
            |--------------------------------------------------------------------------
            */

            let html = `

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>
                                Rank
                            </th>

                            <th>
                                Candidate
                            </th>

                            <th>
                                Final Score
                            </th>

                            <th>
                                Semantic
                            </th>

                            <th>
                                Skills
                            </th>

                            <th>
                                Experience
                            </th>

                            <th>
                                Penalty
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>
    `;


            data.ranking.forEach(
                function(candidate) {

                    const score =
                        Number(
                            candidate.finalScore || 0
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Score Badge
                    |--------------------------------------------------------------------------
                    */

                    let badgeClass =
                        'bg-danger';


                    if (score >= 85) {

                        badgeClass =
                            'bg-success';

                    } else if (score >= 70) {

                        badgeClass =
                            'bg-primary';

                    } else if (score >= 50) {

                        badgeClass =
                            'bg-warning text-dark';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Candidate ID
                    |--------------------------------------------------------------------------
                    */

                    const candidateId =
                        String(
                            candidate.candidateId || ''
                        );


                    html += `

                <tr>


                    <!-- RANK -->

                    <td>

                        <div class="rank-number">

                            ${Number(
                                candidate.rank || 0
                            )}

                        </div>

                    </td>


                    <!-- CANDIDATE -->

                    <td>

                        <strong>

                            ${escapeHtml(
                                candidate.candidateName ||
                                'Unknown'
                            )}

                        </strong>

                        <br>

                        <small class="text-muted">

                            ${escapeHtml(
                                candidateId
                            )}

                        </small>

                    </td>


                    <!-- FINAL SCORE -->

                    <td>

                        <span
                            class="badge ${badgeClass} fs-6"
                        >

                            ${Number(
                                candidate.finalScore || 0
                            ).toFixed(2)}%

                        </span>

                    </td>


                    <!-- SEMANTIC -->

                    <td>

                        ${Number(
                            candidate.semanticScore || 0
                        ).toFixed(2)}%

                    </td>


                    <!-- SKILL -->

                    <td>

                        ${Number(
                            candidate.skillScore || 0
                        ).toFixed(2)}%

                    </td>


                    <!-- EXPERIENCE -->

                    <td>

                        ${Number(
                            candidate.experienceScore || 0
                        ).toFixed(2)}%

                    </td>


                    <!-- PENALTY -->

                    <td>

                        ${
                            Number(
                                candidate.keywordPenalty || 0
                            ) > 0

                            ?

                            `<span class="text-danger">
                                -${Number(
                                    candidate.keywordPenalty
                                ).toFixed(2)}
                            </span>`

                            :

                            `<span class="text-success">
                                0
                            </span>`
                        }

                    </td>


                    <!-- ACTION -->

                    <td>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            onclick="showExplanation(
                                ${JSON.stringify(candidateId)}
                            )"
                        >

                            Explain

                        </button>

                    </td>


                </tr>

            `;

                }
            );


            html += `

                    </tbody>

                </table>

            </div>

        </div>

    `;


            container.innerHTML =
                html;

        }


        /*
        |--------------------------------------------------------------------------
        | Show Candidate Explanation
        |--------------------------------------------------------------------------
        */

        async function showExplanation(
            candidateId
        ) {

            const jobId =
                document
                .getElementById('jobId')
                .value
                .trim();


            if (
                jobId === '' ||
                candidateId === ''
            ) {

                alert(
                    'Job ID and Candidate ID are required.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Show loading modal content
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'explainTitle'
                )
                .innerText =
                'Loading Explanation...';


            document
                .getElementById(
                    'explainBody'
                )
                .innerHTML = `

            <div class="text-center py-5">

                <div
                    class="spinner-border text-primary"
                ></div>

                <p class="mt-3">
                    Analyzing candidate...
                </p>

            </div>

        `;


            const modalElement =
                document.getElementById(
                    'explainModal'
                );


            const modal =
                bootstrap.Modal.getOrCreateInstance(
                    modalElement
                );


            modal.show();


            try {


                /*
                |--------------------------------------------------------------------------
                | IMPORTANT
                |--------------------------------------------------------------------------
                |
                | Actual PHP file:
                |
                | api/v1/screening/explain.php
                |
                */

                const url =
                    API_BASE +
                    'explain.php' +
                    '?candidateId=' +
                    encodeURIComponent(
                        candidateId
                    ) +
                    '&jobId=' +
                    encodeURIComponent(
                        jobId
                    );


                console.log(
                    'Explain API:',
                    url
                );


                const response =
                    await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });


                if (!response.ok) {

                    throw new Error(
                        'HTTP Error: ' +
                        response.status
                    );
                }


                const data =
                    await response.json();


                console.log(
                    'Explain Response:',
                    data
                );


                if (!data.success) {

                    document
                        .getElementById(
                            'explainBody'
                        )
                        .innerHTML = `

                    <div class="alert alert-danger">

                        ${escapeHtml(
                            data.message ||
                            'Unable to load explanation.'
                        )}

                    </div>

                `;

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Display explanation
                |--------------------------------------------------------------------------
                */

                renderExplanation(data);


            } catch (error) {

                console.error(
                    'Explanation Error:',
                    error
                );


                document
                    .getElementById(
                        'explainBody'
                    )
                    .innerHTML = `

                <div class="alert alert-danger">

                    <strong>
                        Unable to load explanation.
                    </strong>

                    <br>

                    ${escapeHtml(
                        error.message
                    )}

                </div>

            `;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Render Explanation
        |--------------------------------------------------------------------------
        */

        function renderExplanation(data) {

            const candidate =
                data.candidate || {};


            const score =
                data.score || {};


            const skills =
                data.skills || {};


            const matched =
                Array.isArray(
                    skills.matched
                ) ?
                skills.matched : [];


            const missing =
                Array.isArray(
                    skills.missing
                ) ?
                skills.missing : [];


            document
                .getElementById(
                    'explainTitle'
                )
                .innerText =
                candidate.name ||
                'Candidate';


            document
                .getElementById(
                    'explainBody'
                )
                .innerHTML = `


        <!-- SCORE CARDS -->

        <div class="row g-3 mb-4">


            <div class="col-md-3">

                <div class="card score-card text-center">

                    <div class="card-body">

                        <small class="text-muted">
                            Final Score
                        </small>

                        <h2 class="fw-bold mb-0">

                            ${formatScore(
                                score.finalScore
                            )}%

                        </h2>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card score-card text-center">

                    <div class="card-body">

                        <small class="text-muted">
                            Semantic
                        </small>

                        <h4 class="fw-bold mb-0">

                            ${formatScore(
                                score.semanticScore
                            )}%

                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card score-card text-center">

                    <div class="card-body">

                        <small class="text-muted">
                            Skills
                        </small>

                        <h4 class="fw-bold mb-0">

                            ${formatScore(
                                score.skillScore
                            )}%

                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card score-card text-center">

                    <div class="card-body">

                        <small class="text-muted">
                            Experience
                        </small>

                        <h4 class="fw-bold mb-0">

                            ${formatScore(
                                score.experienceScore
                            )}%

                        </h4>

                    </div>

                </div>

            </div>


        </div>


        <!-- RECOMMENDATION -->

        <h6 class="fw-bold">
            Recommendation
        </h6>


        <div class="alert alert-primary">

            <strong>

                ${escapeHtml(
                    data.recommendation ||
                    'Not available'
                )}

            </strong>

        </div>


        <!-- MATCHED SKILLS -->

        <h6 class="fw-bold">
            Matched Skills
        </h6>


        <div class="mb-4">

            ${
                matched.length > 0

                ?

                matched
                .map(
                    function(skill)
                    {

                        return `

                            <span
                                class="badge bg-success skill-badge"
                            >
                                ${escapeHtml(skill)}
                            </span>

                        `;

                    }
                )
                .join('')

                :

                '<span class="text-muted">No matched skills</span>'
            }

        </div>


        <!-- MISSING SKILLS -->

        <h6 class="fw-bold">
            Missing Skills
        </h6>


        <div class="mb-4">

            ${
                missing.length > 0

                ?

                missing
                .map(
                    function(skill)
                    {

                        return `

                            <span
                                class="badge bg-danger skill-badge"
                            >
                                ${escapeHtml(skill)}
                            </span>

                        `;

                    }
                )
                .join('')

                :

                '<span class="text-success">No missing skills</span>'
            }

        </div>


        <!-- KEYWORD PENALTY -->

        <h6 class="fw-bold">
            Keyword Stuffing Penalty
        </h6>


        <div class="mb-4">

            ${
                Number(
                    score.keywordPenalty || 0
                ) > 0

                ?

                `<span class="text-danger fw-bold">
                    -${formatScore(
                        score.keywordPenalty
                    )} points
                </span>`

                :

                `<span class="text-success fw-bold">
                    No penalty
                </span>`
            }

        </div>


        <!-- JUSTIFICATION -->

        <h6 class="fw-bold">
            Recruiter Justification
        </h6>


        <div class="alert alert-light border">

            ${escapeHtml(
                data.justification ||
                'No justification available.'
            )}

        </div>


        <!-- EDUCATION -->

        <h6 class="fw-bold">
            Education
        </h6>


        <div class="mb-4">

            ${
                data.education

                ?

                escapeHtml(
                    data.education
                )

                :

                '<span class="text-muted">Not detected</span>'
            }

        </div>


        <!-- EXPERIENCE -->

        <h6 class="fw-bold">
            Experience
        </h6>


        <div>

            ${
                data.experience

                ?

                escapeHtml(
                    data.experience
                )

                :

                '<span class="text-muted">Not detected</span>'
            }

        </div>

    `;

        }


        /*
        |--------------------------------------------------------------------------
        | Display Message
        |--------------------------------------------------------------------------
        */

        function showMessage(
            message,
            type
        ) {

            document
                .getElementById(
                    'message'
                )
                .innerHTML = `

            <div
                class="alert alert-${type} alert-dismissible fade show"
            >

                ${escapeHtml(message)}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        `;

        }


        /*
        |--------------------------------------------------------------------------
        | Format Score
        |--------------------------------------------------------------------------
        */

        function formatScore(value) {

            const number =
                Number(value);


            if (
                Number.isNaN(number)
            ) {

                return '0.00';
            }


            return number.toFixed(2);

        }


        /*
        |--------------------------------------------------------------------------
        | Escape HTML
        |--------------------------------------------------------------------------
        */

        function escapeHtml(text) {

            if (
                text === null ||
                text === undefined
            ) {

                return '';
            }


            const div =
                document.createElement(
                    'div'
                );


            div.textContent =
                String(text);


            return div.innerHTML;

        }


        /*
        |--------------------------------------------------------------------------
        | Enter Key Support
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('jobId')
            .addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Enter'
                    ) {

                        loadRanking();

                    }

                }
            );
    </script>


</body>

</html>