<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class SystemCommandController extends Controller
{
    public function __invoke(string $command, string $secret)
    {
        $expectedSecret = env('SYSTEM_SECRET_KEY');
        if (!$expectedSecret || $secret !== $expectedSecret) {
            abort(403, 'Unauthorized.');
        }

        $allowedCommands = [
            'migrate' => 'migrate --force',
            'seed' => 'db:seed --force',
            'optimize' => 'optimize',
            'clear-cache' => 'cache:clear',
            'config-cache' => 'config:cache',
            'route-cache' => 'route:cache',
            'view-clear' => 'view:clear',
            'storage-link' => 'storage:link',
        ];

        if (!app()->environment('production')) {
            $allowedCommands['migrate-fresh'] = 'migrate:fresh --force';
            $allowedCommands['migrate-seed'] = 'migrate:fresh --seed --force';
        }

        if (!array_key_exists($command, $allowedCommands)) {
            return "Command [{$command}] is not allowed or supported.";
        }

        try {
            $artisanCommand = $allowedCommands[$command];

            Artisan::call($artisanCommand);
            $output = Artisan::output() ?: 'Command executed successfully with empty output.';

            return response(
                "<h3>Executing: php artisan {$artisanCommand}</h3>" .
                "<pre style='background: #272822; color: #f8f8f2; padding: 15px; border-radius: 8px; font-family: monospace;'>" .
                e($output) .
                '</pre>' .
                "<p style='color: green; font-weight: bold;'>Execution complete!</p>"
            );
        } catch (\Throwable $e) {
            return response(
                "<p style='color: red; font-weight: bold;'>Error occurred:</p>" .
                "<pre style='background: #ffd6d6; color: #900; padding: 15px; border-radius: 8px;'>" .
                e($e->getMessage()) .
                '</pre>',
                500
            );
        }
    }
}
