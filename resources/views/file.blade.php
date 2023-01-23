<!DOCTYPE html>
<html>

<head>
    {{-- mobile viewport --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    {{-- link resources/css/app.css --}}
    <link rel="stylesheet" href="/css/app.css">


    <title>File</title>
    {{-- bootstrap --}}

</head>

<body>
    <div class="p-4">

        {{-- input tag link id --}}
        <input type="text" name="link" id="link" value="{{ $path2 }}" class="w-full">
        {{-- click to copy button --}}
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4"
            onclick="copyTo()">Copy</button>
    </div>

    <script>
        //copy to clipboard
        function copyTo() {
            var copyText = document.getElementById("link");
            console.log(copyText)
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Copied the text: " + copyText.value);
        }
    </script>


</body>
</html>
