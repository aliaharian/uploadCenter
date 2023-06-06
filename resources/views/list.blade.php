<!DOCTYPE html>
<html>

<head>
    {{-- mobile viewport --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    {{-- link resources/css/app.css --}}
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
    <script src="/js/downloadChunk.js"></script>

    <title>Upload</title>
    {{-- bootstrap --}}

</head>

<body>
    {{-- list of links in a table with tailwind --}}

    {{-- download progress popup --}}
    <div id="uploadProgressDialog"
        class="hidden fixed w-full h-full bg-[rgba(0,0,0,0.7)] flex items-center justify-center">
        <div class="w-[calc(100%-32px)] h-[300px] bg-white flex items-center justify-center flex-col">

            <div class="mb-8 w-[100px] h-[100px] bg-white relative flex items-center justify-center">
                <div id="middle-circle"
                    class="absolute w-[80px] h-[80px] bg-white rounded-full flex items-center justify-center">0%</div>
                <div class="w-full h-full rounded-full"
                    style="background:conic-gradient(rgb(3, 133, 255) 0%, rgb(242, 242, 242) 0%)" id="progress-spinner">
                </div>
            </div>
            <p>Download in progress, Please wait...</p>

        </div>
    </div>


    <div class="p-4">
        {{-- create header with upload file button with tailwind --}}
        <div class="flex justify-between">
            <h1 class="text-2xl">List of files</h1>
            <a href="{{ route('upload') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Upload
                file</a>
        </div>
        {{-- show exception if no files --}}

        @if (count($files) == 0)
            <div class="mt-4">
                <h1 class="text-xl">No files</h1>
            </div>
        @else
            <table class="table-auto">
                <thead>
                    <tr>
                        <th class="px-4 py-2">download</th>
                        <th class="px-4 py-2">name</th>
                        <th class="px-4 py-2">size (MB)</th>
                        <th class="px-4 py-2">Delete</th>
                    </tr>
                </thead>
                <tbody>



                    @foreach ($files as $file)
                        {{-- @php
                            //retrive hash code from file meta json
                            $hash = json_decode($file->meta)->md5;
                        @endphp --}}
                        <tr>
                            <td class="border px-4 py-2">
                                <button onclick="handleStartDownload({{ $file->hashCode }})"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Download
                                </button>
                            </td>
                            <td class="border px-4 py-2">
                                <p>{{ $file->name }}</p>
                            </td>
                            <td class="border px-4 py-2">
                                <p>{{ round($file->size / 1024 / 1024, 2) }}</p>
                            </td>

                            <td class="border px-4 py-2">
                                <form action="{{ route('delete', $file->id) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <input type="submit" value="Delete"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                </form>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        @endif


        <script>
            let hashCode = null;
            let downloadChunk = new downloadFile();
            async function handleStartDownload(hash) {
                hashCode = hash;
                console.log('hash', hash)
                $("#uploadProgressDialog").css('display', 'flex')
                downloadChunk.setHashCode(hash);
                let res = await downloadChunk.download();
                console.log('res', res)
                //download res url
                downloadURI(res.url, res.name)

            }

            function downloadURI(uri, name) {
                var link = document.createElement("a");
                link.download = name;
                link.href = uri;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                delete link;
            }

            $(function() {
                $(document).ready(function() {
                    // Register progress listener
                    downloadChunk.setProgressListener(function(progress) {
                        // console.log('pro', progress)
                        percent = $('#middle-circle')
                        bar = $('#progress-spinner')
                        var percentVal = Math.ceil(progress) + '%';
                        bar.css("background",
                            `conic-gradient(rgb(3, 133, 255) ${percentVal}, rgb(242, 242, 242) ${percentVal})`
                        );
                        percent.html(percentVal);
                        if (progress == 100) {
                            setTimeout(() => {
                                // alert('upload successful!')
                                // window.location.href = "/"
                            }, 500);

                        }
                    });
                });
            });
        </script>
</body>


</html>
