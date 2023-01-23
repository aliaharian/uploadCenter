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
                        <form method="POST" action="{{ url('upload') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <input name="file" type="file" id="file" class="form-control"><br />
                                <div class="progress">
                                    <div class="bar"></div>
                                    <div class="percent">0%</div>
                                </div>
                                <br>
                                <input type="submit" value="Submit" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        //check if file not grater than 5 mb
        document.getElementById('file').addEventListener('change', function() {
            if (this.files[0].size > 15000000) {
                alert('File size is greater than 15MB');
                this.value = "";
            }
        });
    </script>

    <script type="text/javascript">
        $(function() {
            $(document).ready(function() {
                var bar = $('.bar');
                var percent = $('.percent');
                $('form').ajaxForm({
                    beforeSend: function() {
                        var percentVal = '0%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                    },
                    complete: function(xhr) {
                        console.log("xhr",xhr.responseJSON.url);
                        alert(xhr.responseJSON.url);
                        window.location.href="/"
                    }
                });
            });
        });
    </script>
</body>


</html>
