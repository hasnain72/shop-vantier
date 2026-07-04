<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function index(): View
    {
        return view('admin.queue.index', [
            'pending' => $this->pendingCount(),
            'failed'  => $this->failedCount(),
            'driver'  => config('queue.default'),
        ]);
    }

    /**
     * Process pending jobs synchronously until the queue is empty or the timeout hits.
     * Returns JSON so the admin page can show a "processed N jobs" message without a full reload.
     */
    public function run(): JsonResponse
    {
        $before = $this->pendingCount();

        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--max-time'        => 45,
            '--tries'           => 1,
        ]);

        $after     = $this->pendingCount();
        $processed = max(0, $before - $after);

        return response()->json([
            'success'   => true,
            'processed' => $processed,
            'pending'   => $after,
            'failed'    => $this->failedCount(),
            'output'    => trim(Artisan::output()),
        ]);
    }

    public function retryFailed(): RedirectResponse
    {
        Artisan::call('queue:retry', ['id' => ['all']]);

        return back()->with('success', 'All failed jobs re-queued.');
    }

    public function flushFailed(): RedirectResponse
    {
        Artisan::call('queue:flush');

        return back()->with('success', 'Failed jobs table cleared.');
    }

    private function pendingCount(): int
    {
        if (config('queue.default') !== 'database') {
            return 0;
        }
        return (int) DB::table(config('queue.connections.database.table', 'jobs'))->count();
    }

    private function failedCount(): int
    {
        return (int) DB::table('failed_jobs')->count();
    }
}
