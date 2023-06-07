<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class uploadController extends Controller
{
    //

    public function index()
    {
        return view('upload');
    }
    public function store(Request $request)
    {

        // check if file not grater than 5 mb

        // return $request->file('file')->getSize();
        if ($request->file('file')->getSize() > 15000000) {
            return response()->json(['error' => 'File size is greater than 5MB'], 400);
        }

        $md5Name = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 20);
        $guessExtension = $request->file('file')->guessExtension();

        //save file using Storage::

        $logoFile = $request->file('file')->storeAs('/stored_files', $md5Name . '.' . $guessExtension, 'public');
        //create hash to save in meta as json

        //create random hash contains numbers and letters
        $hashCode = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 15);

        $hash = [
            'md5' => $hashCode,
            'extension' => $guessExtension,
        ];

        $logo = File::create([
            'name' => $md5Name,
            'path' => '/storage/stored_files',
            'mimeType' => $guessExtension,
            'meta' => json_encode($hash),
        ]);
        $path2 = route('file', $hashCode);

        //remove files that older than 15 minutes from storage and db
        $files = File::where('created_at', '<', now()->subMinutes(15))->get();
        $path = "public/stored_files";
        foreach ($files as $file) {
            Storage::delete($path . '/' . $file->name . '.' . $file->mimeType);
            $file->delete();
        }
        // return view('file', compact("path2"));

        return response()->json([
            'success' => 'File uploaded successfully',
            "url" => $path2
        ], 200);
        // return $logo;
    }

    public function view($hash)
    {
        //remove files that older than 15 minutes from storage and db
        $files = File::where('created_at', '<', now()->subMinutes(15))->get();
        $path = "public/stored_files";
        foreach ($files as $file) {
            Storage::delete($path . '/' . $file->name . '.' . $file->mimeType);
            $file->delete();
        }


        $file = File::where('meta', 'like', '%' . $hash . '%')->first();
        //return 404 if not found
        if (!$file) {
            return response()->json(['error' => 'File not found'], 404);
        }
        // $path = public_path() . $file->path . '/' . $file->name . '.' . $file->mimeType;
        $path = "public/stored_files";

        //get file path
        // $path = $file->path . '/' . $file->name . '.' . $file->mimeType;
        //headers of download file
        $headers = [
            'Content-Type' => 'application/octet-stream',
        ];
        $a = Storage::download($path . '/' . $file->name . '.' . $file->mimeType, "download." . $file->mimeType, $headers);

        //Storage::download delete after download

        // return response()->download($path, "download." . $file->mimeType, $headers);


        return $a;
    }

    public function list()
    {
        //delete files older than 15 minutes
        $filesList = FileMeta::where('created_at', '<', now()->subMinutes(120))->get();
        foreach ($filesList as $file) {
            $file->delete();
        }


        $filesList = FileMeta::orderBy('id', 'desc')->get();
        $files = array();
        foreach ($filesList as $file) {
            if ($file->fileParts->count() == $file->partCount) {
                $obj = new \stdClass();
                $obj->id = $file->id;
                $obj->hashCode = $file->hashCode;
                $obj->name = $file->metas()->name;
                $obj->size = $file->size;

                array_push($files, $obj);
            } else {
                //delete meta
                $file->delete();
            }
        }


        return view('list', compact("files"));
    }

    //delete file
    public function delete($id)
    {
        // $file = File::find($id);
        // $path = "public/stored_files";
        // Storage::delete($path . '/' . $file->name . '.' . $file->mimeType);
        // $file->delete();
        // return redirect()->route('list');


        $file = FileMeta::where('hashCode', $id)->first();
        if (!$file) {
            return response()->json([
                'message' => 'File not found',
            ], 404);
        }
        $file->delete();
        return redirect()->route('list');

    }



}