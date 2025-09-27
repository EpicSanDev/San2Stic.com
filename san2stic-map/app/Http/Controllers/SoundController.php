<?php

namespace App\Http\Controllers;

use App\Models\Sound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SoundController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sound  $sound
     * @return \Illuminate\View\View
     */
    public function edit(Sound $sound)
    {
        return view('sounds.edit', compact('sound'));
    }

    /**
     * Trim the specified sound file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sound  $sound
     * @return \Illuminate\Http\JsonResponse
     */
    public function trim(Request $request, Sound $sound)
    {
        // IMPORTANT: This method requires FFMPEG to be installed on the server.
        // You can install it on Ubuntu with: sudo apt-get install ffmpeg
        // Ensure the path to ffmpeg is in the system's PATH or provide the full path.

        $validated = $request->validate([
            'start' => 'required|numeric|min:0',
            'end' => 'required|numeric|gt:start',
        ]);

        $start = $validated['start'];
        $end = $validated['end'];
        $duration = $end - $start;

        try {
            // 1. Download the file from S3 to a temporary local path.
            $originalContents = Storage::disk('s3')->get($sound->path);
            $tempDisk = Storage::disk('local');
            $tempInputPath = 'temp/' . basename($sound->path);
            $tempOutputPath = 'temp/trimmed_' . basename($sound->path);
            
            $tempDisk->put($tempInputPath, $originalContents);

            // 2. Build and run the FFMPEG process.
            $inputFullPath = $tempDisk->path($tempInputPath);
            $outputFullPath = $tempDisk->path($tempOutputPath);

            $result = Process::run("ffmpeg -i {$inputFullPath} -ss {$start} -t {$duration} -c copy {$outputFullPath}");

            if (!$result->successful()) {
                // If the process fails, throw an exception with the error output.
                throw new \Exception('FFMPEG Error: ' . $result->errorOutput());
            }

            // 3. Upload the trimmed file back to S3, overwriting the original.
            Storage::disk('s3')->put($sound->path, $tempDisk->get($tempOutputPath));

            // 4. Clean up temporary local files.
            $tempDisk->delete([$tempInputPath, $tempOutputPath]);

            return response()->json(['success' => true, 'message' => 'Sound trimmed successfully.']);

        } catch (\Exception $e) {
            // Log the error and return a generic error response.
            report($e);
            return response()->json(['success' => false, 'message' => 'An error occurred while trimming the sound.'], 500);
        }
    }
}
