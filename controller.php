<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{

    public function index()
    {
        return view('x');
    }
    public function upload(Request $request)
    {
        if ($request->hasFile('files')) {
            $images = $request->file('files');
            $order = $request->input('file_order');

            foreach ($images as $key => $image) {
                //  $imageName = $order[$key] . '_' . $image->getClientOriginalName();
                // $image->move(public_path('images'), $imageName);
                $imageName = $order[$key] . "." . "jpg";
                $image->storeAs('public/images', $imageName, 'local');

                $processedImage = Image::make($image->getRealPath())->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('jpg');


                $processedImagePath = 'processed/' . 'processed_' . $order[$key] . '.jpg';
                Storage::disk('public')->put($processedImagePath, $processedImage);
            }

            return response()->json(['message' => 'Images uploaded successfully']);
        } else {
            return response()->json(['message' => 'No images or order provided'], 400);
        }
    }
    public function readFiles()
    {
        $directory = 'images';
        $files_info = [];
        $file_ext = array('png', 'jpg', 'jpeg', 'pdf');

        // Read files
        foreach (Storage::disk('public')->files($directory) as $file) {

            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (in_array($extension, $file_ext)) { // Check file extension 
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $size = Storage::disk('public')->size($file); // Bytes 
                $sizeinMB = round($size / (1024 * 1024), 2); // MB 


                if ($sizeinMB <= 2) { // Check file size is <= 2 MB 
                    $files_info[] = array(
                        "name" => $filename,
                        "extension" => $extension,
                        "size" => $size,
                        "path" => url("storage/" . $directory . '/' . $filename . "." . $extension)
                    );
                }
            }
        }
        return response()->json($files_info);
    }

    public function deleteFile(Request $request)
    {
        $filename = $request->input('filename');

        Storage::disk('public')->delete('images/' . $filename . "." . "jpg");
        Storage::disk('public')->delete('processed/processed_' . $filename . "." . "jpg");
        return response()->json(['message' => 'File deleted successfully']);
    }
}
