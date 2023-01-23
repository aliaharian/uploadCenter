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

</head>

<body>
    {{-- list of links in a table with tailwind --}}
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
                        <th class="px-4 py-2">Link</th>
                        <th class="px-4 py-2">Delete</th>
                    </tr>
                </thead>
                <tbody>



                    @foreach ($files as $file)
                        @php
                            //retrive hash code from file meta json
                            $hash = json_decode($file->meta)->md5;
                        @endphp
                        <tr>
                            <td class="border px-4 py-2">
                                <a href="{{ route('file', $hash) }}" target="_blank">{{ route('file', $hash) }}</a>
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

</body>


</html>
