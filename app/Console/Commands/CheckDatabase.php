<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check package-related database tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking packages table...');
        $packages = DB::table('packages')->get();
        $this->table(['package_id', 'category_id', 'title', 'price'],
            $packages->map(function($package) {
                return [
                    'package_id' => $package->package_id,
                    'category_id' => $package->category_id ?? 'NULL',
                    'title' => $package->title,
                    'price' => $package->price
                ];
            })
        );        $this->info('Checking addons table...');
        $addons = DB::table('addons')->get();
        $this->table(['addon_id', 'addon_name', 'addon_price'], 
            $addons->map(function($addon) {
                return [
                    'addon_id' => $addon->addon_id,
                    'addon_name' => $addon->addon_name,
                    'addon_price' => $addon->addon_price
                ];
            })
        );

        $this->info('Checking package inclusions table...');
        $inclusions = DB::table('package_inclusions')
                    ->join('packages', 'packages.package_id', '=', 'package_inclusions.package_id')
                    ->select('package_inclusions.inclusion_id', 'packages.title', 'package_inclusions.inclusion_text')
                    ->limit(10)
                    ->get();
        $this->table(['inclusion_id', 'package', 'inclusion_text'], 
            $inclusions->map(function($inclusion) {
                return [
                    'inclusion_id' => $inclusion->inclusion_id,
                    'package' => $inclusion->title,
                    'inclusion_text' => $inclusion->inclusion_text
                ];
            })
        );

        $this->info('Checking package free items table...');
        $freeItems = DB::table('package_free_items')
                    ->join('packages', 'packages.package_id', '=', 'package_free_items.package_id')
                    ->select('package_free_items.free_item_id', 'packages.title', 'package_free_items.free_item_text')
                    ->limit(10)
                    ->get();
        $this->table(['free_item_id', 'package', 'free_item_text'], 
            $freeItems->map(function($item) {
                return [
                    'free_item_id' => $item->free_item_id,
                    'package' => $item->title,
                    'free_item_text' => $item->free_item_text
                ];
            })
        );
    }
}
