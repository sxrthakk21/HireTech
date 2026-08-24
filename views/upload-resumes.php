<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Upload Resumes</title>


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

                            Upload Candidate Resumes

                        </h3>


                        <div id="message"></div>


                        <form
                            id="resumeForm"
                            enctype="multipart/form-data">


                            <div class="mb-4">

                                <label class="form-label">

                                    Select Resumes

                                </label>


                                <input
                                    type="file"
                                    name="resumes[]"
                                    id="resumes"
                                    class="form-control"
                                    accept=".pdf,.docx"
                                    multiple
                                    required>


                                <small class="text-muted">

                                    PDF and DOCX only. Maximum
                                    5 MB per file.

                                </small>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-success">

                                Upload Resumes

                            </button>


                            <a
                                href="../index.php"
                                class="btn btn-secondary">

                                Back

                            </a>


                        </form>


                        <div
                            id="results"
                            class="mt-4"></div>


                    </div>

                </div>

            </div>

        </div>

    </div>


    <script>
        document
            .getElementById('resumeForm')
            .addEventListener(
                'submit',
                async function(event) {

                    event.preventDefault();


                    const files =
                        document.getElementById(
                            'resumes'
                        ).files;


                    if (files.length === 0) {

                        alert(
                            'Please select at least one resume.'
                        );

                        return;
                    }


                    const formData =
                        new FormData();


                    for (
                        let i = 0; i < files.length; i++
                    ) {

                        formData.append(
                            'resumes[]',
                            files[i]
                        );

                    }


                    document.getElementById(
                        'message'
                    ).innerHTML = `

            <div class="alert alert-info">

                Uploading resumes...

            </div>

        `;


                    try {

                        const response =
                            await fetch(
                                '../api/v1/screening/resumes.php', {
                                    method: 'POST',
                                    body: formData
                                }
                            );


                        const data =
                            await response.json();


                        if (data.success) {

                            document.getElementById(
                                'message'
                            ).innerHTML = `

                    <div class="alert alert-success">

                        ${data.message}

                    </div>

                `;


                            let html = `

                    <h5>
                        Uploaded Candidates
                    </h5>

                    <div class="table-responsive">

                    <table class="table table-bordered">

                    <thead>

                    <tr>

                       <th>Candidate ID</th>
<th>File</th>
<th>Type</th>
<th>Skills</th>
<th>Words</th>
<th>Status</th>
                    </tr>

                    </thead>

                    <tbody>

                `;


                            data.candidates.forEach(
                                function(candidate) {

                                    html += `

                            <tr>

                               <td>
    ${candidate.candidateId}
</td>

<td>
    ${candidate.originalName}
</td>

<td>
    ${candidate.fileType.toUpperCase()}
</td>

<td>
    ${
        candidate.skills.length > 0
        ? candidate.skills.join(', ')
        : 'No skills detected'
    }
</td>

<td>
    ${candidate.wordCount}
</td>

<td>

    <span class="badge bg-success">
        Processed
    </span>

</td>

                                    <span
                                        class="badge bg-success"
                                    >

                                        Uploaded

                                    </span>

                                </td>

                            </tr>

                        `;

                                }
                            );


                            html += `

                    </tbody>

                    </table>

                    </div>

                `;


                            document.getElementById(
                                'results'
                            ).innerHTML = html;


                            document.getElementById(
                                'resumeForm'
                            ).reset();


                        } else {

                            document.getElementById(
                                'message'
                            ).innerHTML = `

                    <div class="alert alert-danger">

                        ${data.message}

                    </div>

                `;

                        }


                    } catch (error) {

                        console.error(error);


                        document.getElementById(
                            'message'
                        ).innerHTML = `

                <div class="alert alert-danger">

                    Server error occurred.

                </div>

            `;

                    }

                }
            );
    </script>


</body>

</html>