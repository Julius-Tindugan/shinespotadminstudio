<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Package;

class TestFeaturedPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:featured-packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the featured packages limitation functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== Featured Package Limitation Test ===');
        $this->newLine();

        try {
            // Test 1: Check current featured count
            $this->info('1. Current featured package count: ' . Package::getFeaturedCount());
            $this->info('   Maximum allowed: ' . Package::getMaxFeaturedLimit());
            $this->info('   Can add more featured? ' . (Package::canBeFeatured() ? 'YES' : 'NO'));
            $this->newLine();

            // Test 2: Get all packages and their featured status
            $this->info('2. Current packages and their featured status:');
            $packages = Package::select('package_id', 'title', 'is_featured')->get();
            foreach ($packages as $package) {
                $status = $package->is_featured ? 'FEATURED' : 'Not Featured';
                $this->line("   - {$package->title} (ID: {$package->package_id}): {$status}");
            }
            $this->newLine();

            // Test 3: Test validation for making a package featured
            $this->info('3. Testing featured validation:');
            $firstNonFeatured = Package::where('is_featured', false)->first();
            
            if ($firstNonFeatured) {
                $validation = Package::validateFeaturedStatus(true, null);
                $this->info("   - Trying to feature '{$firstNonFeatured->title}':");
                $this->info('     Valid: ' . ($validation['valid'] ? 'YES' : 'NO'));
                if (!$validation['valid']) {
                    $this->warn("     Message: {$validation['message']}");
                }
            } else {
                $this->info('   - No non-featured packages found to test with');
            }
            $this->newLine();

            // Test 4: Simulate reaching the limit
            $this->info('4. Testing limit enforcement:');
            $featuredCount = Package::getFeaturedCount();
            
            if ($featuredCount < 4) {
                $this->info("   - Current featured count is {$featuredCount}, testing if we can add more...");
                
                // Try to set enough packages to reach the limit
                $nonFeaturedPackages = Package::where('is_featured', false)->limit(4 - $featuredCount)->get();
                
                foreach ($nonFeaturedPackages as $package) {
                    $validation = Package::validateFeaturedStatus(true, null);
                    if ($validation['valid']) {
                        $this->info("   - Can feature '{$package->title}': YES");
                    } else {
                        $this->warn("   - Can feature '{$package->title}': NO - {$validation['message']}");
                        break;
                    }
                }
            } else {
                $this->info("   - Already at or above limit ({$featuredCount} featured packages)");
                
                // Test trying to add one more
                $nonFeatured = Package::where('is_featured', false)->first();
                if ($nonFeatured) {
                    $validation = Package::validateFeaturedStatus(true, null);
                    $this->info("   - Trying to feature '{$nonFeatured->title}': " . 
                             ($validation['valid'] ? 'ALLOWED' : 'BLOCKED'));
                    if (!$validation['valid']) {
                        $this->warn("     Message: {$validation['message']}");
                    }
                }
            }
            $this->newLine();

            // Test 5: Test update scenario (excluding current package from count)
            $this->info('5. Testing update scenario (excluding current package):');
            $featuredPackage = Package::where('is_featured', true)->first();
            
            if ($featuredPackage) {
                $validation = Package::validateFeaturedStatus(true, $featuredPackage->package_id);
                $this->info("   - Updating '{$featuredPackage->title}' to remain featured: " . 
                         ($validation['valid'] ? 'ALLOWED' : 'BLOCKED'));
                if (!$validation['valid']) {
                    $this->warn("     Message: {$validation['message']}");
                }
            } else {
                $this->info('   - No featured packages found to test with');
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }

        $this->newLine();
        $this->info('=== Test Complete ===');
        
        return 0;
    }
}