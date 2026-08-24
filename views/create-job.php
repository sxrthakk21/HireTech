<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Create Job</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">


    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card shadow-sm">

                    <div class="card-body p-4">

                        <h3 class="mb-4">
                            Create Job Description
                        </h3>


                        <div id="message"></div>


                        <form id="jobForm">


                            <div class="mb-3">

                                <label class="form-label">
                                    Job Title
                                </label>

                                <input
                                    type="text"
                                    id="title"
                                    class="form-control"
                                    placeholder="PHP Laravel Developer"
                                    required>

                            </div>


                            <div class="mb-3">

                                <label class="form-label">
                                    Job Description
                                </label>

                                <textarea
                                    id="description"
                                    class="form-control"
                                    rows="10"
                                    placeholder="Enter complete job description..."
                                    required></textarea>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-primary">
                                Create Job
                            </button>


                            <a
                                href="../index.php"
                                class="btn btn-secondary">
                                Back
                            </a>


                        </form>


                    </div>

                </div>

            </div>

        </div>

    </div>


    <script>
        document
            .getElementById('jobForm')
            .addEventListener('submit', async function(event) {

                event.preventDefault();


                const title =
                    document.getElementById('title').value;

                const description =
                    document.getElementById('description').value;


                try {

                    const response = await fetch(
                        '../api/v1/screening/jobs.php', {
                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json'
                            },

                            body: JSON.stringify({
                                title: title,
                                description: description
                            })
                        }
                    );


                    const data = await response.json();


                    if (data.success) {

                        document.getElementById('message').innerHTML = `

                    <div class="alert alert-success">

                        <strong>Job Created!</strong>

                        <br>

                        Job ID:
                        ${data.job.jobId}

                        <br>

                        Skills detected:
                        ${data.job.skills.join(', ')}

                    </div>

                `;

                        document.getElementById('jobForm').reset();

                    } else {

                        document.getElementById('message').innerHTML = `

                    <div class="alert alert-danger">

                        ${data.message}

                    </div>

                `;

                    }

                } catch (error) {

                    document.getElementById('message').innerHTML = `

                <div class="alert alert-danger">

                    Server error occurred.

                </div>

            `;

                    console.error(error);

                }

            });
    </script>


</body>

</html>