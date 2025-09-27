<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class WebhookController extends Controller
{
    /**
     * Handle the incoming webhook request to deploy the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deploy(Request $request)
    {
        // 1. Verify the secret token (replace with your Git provider's method)
        // For GitHub, the secret is in the 'X-Hub-Signature-256' header.
        // For GitLab, it's in the 'X-Gitlab-Token' header.
        // For simplicity, we'll use a custom header or a query parameter for now,
        // but a proper implementation should verify the signature.
        $secret = env('WEBHOOK_SECRET');
        if (!$secret || $request->header('X-Webhook-Secret') !== $secret) {
            Log::warning('Webhook: Unauthorized access attempt.');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 2. Execute the update script
        $scriptPath = base_path('update.sh'); // Assuming update.sh is in the project root

        if (!file_exists($scriptPath)) {
            Log::error('Webhook: update.sh script not found at ' . $scriptPath);
            return response()->json(['message' => 'Update script not found'], 500);
        }

        // Ensure the script is executable
        if (!is_executable($scriptPath)) {
            Log::error('Webhook: update.sh is not executable. Attempting to make it executable.');
            try {
                Process::run("chmod +x {$scriptPath}");
            } catch (ProcessFailedException $e) {
                Log::error('Webhook: Failed to make update.sh executable: ' . $e->getMessage());
                return response()->json(['message' => 'Update script not executable'], 500);
            }
        }

        try {
            // Execute the script. Use 'sudo' if the script needs elevated privileges
            // and the web server user (www-data) has sudo rights for this script.
            // This is a security risk and should be configured carefully.
            // A better approach is to ensure the web server user has permissions
            // to run git pull, composer, npm, etc. without sudo.
            $process = Process::path(base_path())->run("bash {$scriptPath}");

            if ($process->successful()) {
                Log::info('Webhook: Deployment successful. Output: ' . $process->output());
                return response()->json(['message' => 'Deployment successful', 'output' => $process->output()]);
            } else {
                Log::error('Webhook: Deployment failed. Error: ' . $process->errorOutput() . ' Output: ' . $process->output());
                return response()->json(['message' => 'Deployment failed', 'error' => $process->errorOutput(), 'output' => $process->output()], 500);
            }
        } catch (ProcessFailedException $e) {
            Log::error('Webhook: Process execution failed: ' . $e->getMessage());
            return response()->json(['message' => 'Deployment process failed', 'error' => $e->getMessage()], 500);
        }
    }
}