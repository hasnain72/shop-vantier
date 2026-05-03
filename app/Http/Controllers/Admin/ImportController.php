<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function index(): View
    {
        return view('admin.import.index');
    }

    public function store(Request $request)
    {
        // No timeout or memory limits for imports
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $request->validate([
            'products_csv'  => ['nullable', 'file', 'max:102400'],
            'orders_csv'    => ['nullable', 'file', 'max:102400'],
            'inventory_csv' => ['nullable', 'file', 'max:102400'],
        ]);

        $hasFile = $request->hasFile('products_csv')
                || $request->hasFile('orders_csv')
                || $request->hasFile('inventory_csv');

        if (! $hasFile) {
            return back()->withErrors(['general' => 'Please upload at least one CSV file.']);
        }

        // Validate file extensions manually (more reliable than mimes on Windows)
        foreach (['products_csv', 'orders_csv', 'inventory_csv'] as $field) {
            if ($request->hasFile($field)) {
                $ext = strtolower($request->file($field)->getClientOriginalExtension());
                if (! in_array($ext, ['csv', 'txt'])) {
                    return back()->withErrors([$field => 'File must be a CSV (.csv or .txt).']);
                }
            }
        }

        // Build temp directory
        $tempPath = storage_path('app/temp/shopify_import_' . time());
        if (! is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        $artisanArgs = ['--path' => $tempPath, '--force' => true];

        if ($request->hasFile('products_csv')) {
            $request->file('products_csv')->move($tempPath, 'products_export.csv');
        } else {
            $artisanArgs['--skip-products'] = true;
        }

        if ($request->hasFile('orders_csv')) {
            $request->file('orders_csv')->move($tempPath, 'orders_export.csv');
        } else {
            $artisanArgs['--skip-orders'] = true;
        }

        if ($request->hasFile('inventory_csv')) {
            $request->file('inventory_csv')->move($tempPath, 'inventory_export.csv');
        } else {
            $artisanArgs['--skip-inventory'] = true;
        }

        if ($request->boolean('fresh')) {
            $artisanArgs['--fresh'] = true;
        }

        $error = null;
        $stats = [];

        try {
            $exitCode = Artisan::call('import:shopify', $artisanArgs);
            $output   = Artisan::output();

            // Parse stats from command's public property
            $command = app(\App\Console\Commands\ImportShopify::class);
            // Re-run to capture stats — use the output text to parse counts instead
            $stats = $this->parseStats($output);

            if ($exitCode !== 0) {
                $error = 'Import finished with errors. See details below.';
            }
        } catch (\Throwable $e) {
            $error  = $e->getMessage();
            $output = $error;
        } finally {
            // Clean up temp files
            foreach (['products_export.csv', 'orders_export.csv', 'inventory_export.csv'] as $f) {
                $fp = $tempPath . DIRECTORY_SEPARATOR . $f;
                if (file_exists($fp)) @unlink($fp);
            }
            @rmdir($tempPath);
        }

        return back()->with([
            'import_output' => $output ?? '',
            'import_stats'  => $stats,
            'import_error'  => $error,
            'import_done'   => true,
        ]);
    }

    /**
     * Extract counts from the command's text output lines like:
     *   "Products: 49 created, 1 skipped"
     *   "Inventory: 50 variants updated, 0 not matched, 1 location(s) ensured"
     *   "Orders: 5 created, 0 skipped"
     */
    private function parseStats(string $output): array
    {
        $stats = [];

        if (preg_match('/Products:\s+(\d+) created.*?(\d+) skipped/i', $output, $m)) {
            $stats['products'] = ['created' => (int)$m[1], 'skipped' => (int)$m[2]];
        }

        if (preg_match('/Inventory:\s+(\d+) variants updated.*?(\d+) not matched/i', $output, $m)) {
            $stats['inventory'] = ['updated' => (int)$m[1], 'missed' => (int)$m[2]];
            if (preg_match('/(\d+) location/i', $output, $lm)) {
                $stats['inventory']['locations'] = (int)$lm[1];
            }
        }

        if (preg_match('/Orders:\s+(\d+) created.*?(\d+) skipped/i', $output, $m)) {
            $stats['orders'] = ['created' => (int)$m[1], 'skipped' => (int)$m[2]];
        }

        // Detect file-not-found errors
        if (str_contains($output, 'Products file not found')) {
            $stats['products'] = ['error' => 'File not found'];
        }
        if (str_contains($output, 'Inventory file not found')) {
            $stats['inventory'] = ['error' => 'File not found'];
        }
        if (str_contains($output, 'Orders file not found')) {
            $stats['orders'] = ['error' => 'File not found'];
        }

        return $stats;
    }
}
