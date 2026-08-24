<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Intelligent Resume Screening</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="assets/css/style.css">

</head>

<body>

    <nav class="navbar navbar-dark bg-dark">

        <div class="container">

            <a class="navbar-brand fw-bold" href="#">
                Intelligent Resume Screening
            </a>

        </div>

    </nav>


    <div class="container py-5">

        <div class="row mb-4">

            <div class="col-md-12">

                <h1 class="fw-bold">
                    Resume Screening & Semantic Ranking
                </h1>

                <p class="text-muted">
                    Explainable AI-based candidate screening system
                </p>

            </div>

        </div>


        <div class="row g-4">


            <div class="col-md-4">

                <div class="card dashboard-card">

                    <div class="card-body">

                        <h5>Create Job</h5>

                        <p class="text-muted">
                            Add a new job description.
                        </p>

                        <a
                            href="views/create-job.php"
                            class="btn btn-primary">
                            Create Job
                        </a>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card dashboard-card">

                    <div class="card-body">

                        <h5>Upload Resumes</h5>

                        <p class="text-muted">
                            Upload candidate resumes.
                        </p>

                        <a
                            href="views/upload-resumes.php"
                            class="btn btn-success">
                            Upload Resumes
                        </a>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card dashboard-card">

                    <div class="card-body">

                        <h5>Ranking</h5>

                        <p class="text-muted">
                            View candidate ranking.
                        </p>

                        <a
                            href="views/ranking.php"
                            class="btn btn-dark">
                            View Ranking
                        </a>

                    </div>

                </div>

            </div>


        </div>


        <div class="row mt-4">

            <div class="col-md-6">

                <div class="card">

                    <div class="card-body">

                        <h5>
                            Semantic Matching
                        </h5>

                        <p>
                            Compare candidate resumes against
                            job requirements using semantic-style
                            similarity and skill matching.
                        </p>

                    </div>

                </div>

            </div>


            <div class="col-md-6">

                <div class="card">

                    <div class="card-body">

                        <h5>
                            Explainable Ranking
                        </h5>

                        <p>
                            Understand why a candidate received
                            a particular score.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>