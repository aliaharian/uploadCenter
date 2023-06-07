<?php

namespace App\Http\Controllers;

use App\Models\FileMeta;
use App\Models\FilePart;
use Illuminate\Http\Request;

class UploadChunkController extends Controller
{
    //
    public function insertFileMeta(Request $request)
    {
        $request->validate([
            'size' => 'required',
            'mimeType' => 'required',
        ]);
        $now = new \DateTime();
        $hashCode = floor($now->getTimestamp() * (rand() / 100000000));

        //calculate how many parts based on 256 kb per part
        $partCount = floor((int) $request->size / (env('UPLOAD_CHUNK'))) + 1;
        $meta = new \stdClass();
        $meta = json_encode($request->meta);
        $fileMeta = FileMeta::create([
            'hashCode' => $hashCode,
            'meta' => $meta,
            'mimeType' => $request->mimeType,
            'partCount' => $partCount,
            'size' => $request->size,
        ]);
        $fileMeta->metaJson = $fileMeta->metas();
        return response()->json(
            $fileMeta,
        );

    }

    public function insertFilePart(Request $request)
    {
        $request->validate([
            'hashCode' => 'required',
            'mimeType' => 'required',
            'data' => 'required',
            'offset' => 'required',
        ]);

        $fileMeta = FileMeta::where('hashCode', $request->hashCode)->first();
        if (!$fileMeta) {
            return response()->json([
                'message' => 'File not found',
            ], 404);
        }
        $compressed = gzdeflate(json_encode($request->data), 9);
        // $compressed = gzdeflate($compressed, 9);
        // return $compressed;
        $filePart = $fileMeta->fileParts()->create([
            'data' => $compressed,
            'offset' => $request->offset,
        ]);

        return response()->json([
            'message' => 'File part created',
            'finished' => $fileMeta->fileParts()->count() == $fileMeta->partCount,
            'bytes_uploaded' => min($fileMeta->fileParts()->count() * env('UPLOAD_CHUNK'), $fileMeta->size),
            'total_bytes' => $fileMeta->size,
            'chunk' => (int) env('UPLOAD_CHUNK'),
            'next_offset' => $fileMeta->fileParts()->count() == $fileMeta->partCount ? null : $fileMeta->fileParts()->count() * env('UPLOAD_CHUNK'),
        ]);
    }
    public function viewFileMeta(Request $request)
    {
        $fileMeta = FileMeta::where('hashCode', $request->hashCode)->first();
        $fileMeta->name = $fileMeta->metas()->name;
        if (!$fileMeta) {
            return response()->json([
                'message' => 'File not found',
            ], 404);
        }

        return response()->json($fileMeta);



    }

    public function viewFilePart(Request $request)
    {
        $fileMeta = FileMeta::where('hashCode', $request->hashCode)->first();

        if (!$fileMeta) {
            return response()->json([
                'message' => 'File not found',
            ], 404);
        }

        $filePart = FilePart::where('file_meta_id', $fileMeta->id)->where('offset', $request->offset)->first();
        if (!$filePart) {
            return response()->json([
                'message' => 'File not found',
            ], 404);
        }


        // return $filePart->data;
        $dec = gzinflate($filePart->data);

        return response()->json(
            [
                'data' => $dec,
                'finished' => $filePart->offset + 1 == $fileMeta->partCount,
                'bytes_downloaded' => min(($filePart->offset + 1) * env('UPLOAD_CHUNK'), $fileMeta->size),
                // 'total_bytes' => $fileMeta->size,
                // 'chunk' => (int) env('UPLOAD_CHUNK'),
                // 'next_offset' => $filePart->offset + 1 == $fileMeta->partCount ? null : $filePart->offset + 1,
            ]
        );



    }
}