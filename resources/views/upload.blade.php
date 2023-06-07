<!DOCTYPE html>
<html>

<head>
    {{-- mobile viewport --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    {{-- link resources/css/app.css --}}
    <link rel="stylesheet" href="/css/app.css">


    <title>Upload</title>
    {{-- bootstrap --}}
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
    <script src="/js/uploadChunk.js"></script>
    <style>
        .progress {
            position: relative;
            width: 100%;
        }

        .bar {
            background-color: #00ff00;
            width: 0%;
            height: 20px;
        }

        .percent {
            position: absolute;
            display: inline-block;
            left: 50%;
            color: #040608;
        }
    </style>

</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Ali Aharian Uploader</h4>
                    </div>
                    <div class="card-body">
                        {{-- <form id="uploadForm" method="POST" action="{{ url('upload') }}" enctype="multipart/form-data">
                            @csrf --}}

                        <div class="form-group">
                            <input name="file" type="file" id="file" class="form-control"><br />
                            <div class="progress">
                                <div style="transition:all 250ms" class="bar"></div>
                                <div class="percent">0%</div>
                            </div>
                            <br>
                            <input id="submitBtn" type="submit" value="Submit" class="btn btn-primary">
                        </div>
                        {{-- </form> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        let file;
        //check if file not grater than 5 mb
        document.getElementById('file').addEventListener('change', function() {
            if (this.files[0].size > 20*1024*1024) {
                alert('File size is greater than 20MB');
                this.value = "";
            } else {
                file = this.files[0];
            }
        });

        $(function() {
            var uploadChunk1 = new uploadFile(file);
            $(document).ready(function() {
                var bar = $('.bar');
                var percent = $('.percent');
                $('#submitBtn').click(async function() {
                    if (!file) {
                        alert('select file!')
                    } else {
                        uploadChunk1.setFile(file);
                        let res = await uploadChunk1.upload();
                        console.log('res', res)


                    }
                });
                // Register progress listener
                uploadChunk1.setProgressListener(function(progress) {
                    // console.log('pro', progress)
                    var percentVal = Math.ceil(progress) + '%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                    if (progress == 100) {
                        setTimeout(() => {
                            alert('upload successful!')
                            window.location.href = "/"
                        }, 500);

                    }
                });
            });
        });
    </script>
</body>


</html>
