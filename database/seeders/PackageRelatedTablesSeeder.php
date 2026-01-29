<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\PackageInclusion;
use App\Models\PackageFreeItem;
use App\Models\Addon;

class PackageRelatedTablesSeeder extends Seeder
{
    /**
     * Seed the package-related tables.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Clear any existing data
        Addon::truncate();
        Package::truncate();
        PackageInclusion::truncate();
        PackageFreeItem::truncate();
        
        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Seed addons
        $addons = [
            ['addon_id' => 1, 'addon_name' => 'Additional Person', 'addon_price' => 149.00],
            ['addon_id' => 2, 'addon_name' => '4R Photo Print', 'addon_price' => 49.00],
            ['addon_id' => 3, 'addon_name' => 'Plus 1 Backdrop', 'addon_price' => 49.00],
            ['addon_id' => 4, 'addon_name' => 'Lighting Effect', 'addon_price' => 99.00],
            ['addon_id' => 5, 'addon_name' => '32in number Balloon', 'addon_price' => 49.00],
            ['addon_id' => 6, 'addon_name' => '5mins Extension', 'addon_price' => 99.00],
            ['addon_id' => 7, 'addon_name' => 'Charge for Pets', 'addon_price' => 149.00],
            ['addon_id' => 8, 'addon_name' => 'A4 Photo Print', 'addon_price' => 100.00],
        ];
        
        foreach ($addons as $addon) {
            Addon::create($addon);
        }
        
        // Seed packages
        $packages = [
            [
                'package_id' => 1, 
                'package_type' => 'Self-Shoot', 
                'title' => 'Solo Package', 
                'price' => 399.00, 
                'image_asset' => 'assets/images/solo.png'
            ],
            [
                'package_id' => 2, 
                'package_type' => 'Self-Shoot', 
                'title' => 'Kids Package', 
                'price' => 499.00, 
                'image_asset' => 'assets/images/kid.png'
            ],
            [
                'package_id' => 3, 
                'package_type' => 'Self-Shoot', 
                'title' => 'Graduation Package', 
                'price' => 499.00, 
                'image_asset' => 'assets/images/grad.jpg'
            ],
            [
                'package_id' => 4, 
                'package_type' => 'Self-Shoot', 
                'title' => 'Duo Package', 
                'price' => 599.00, 
                'image_asset' => 'assets/images/duo.jpg'
            ],
            [
                'package_id' => 5, 
                'package_type' => 'Self-Shoot', 
                'title' => 'Barkada Package', 
                'price' => 799.00, 
                'image_asset' => 'assets/images/group.jpg'
            ],
            [
                'package_id' => 6, 
                'package_type' => 'Self-Shoot', 
                'title' => 'Fam One Package', 
                'price' => 799.00, 
                'image_asset' => 'assets/images/family.jpg'
            ],
            [
                'package_id' => 7, 
                'package_type' => 'Self-Shoot', 
                'title' => 'Fam Two Package', 
                'price' => 999.00, 
                'image_asset' => 'assets/images/famtwo.png'
            ],
            [
                'package_id' => 8, 
                'package_type' => 'Self-Shoot', 
                'title' => 'Fam Three Package', 
                'price' => 1199.00, 
                'image_asset' => 'assets/images/famthree.png'
            ],
            [
                'package_id' => 9, 
                'package_type' => 'Self-Shoot', 
                'title' => 'Fam Four Package', 
                'price' => 2199.00, 
                'image_asset' => 'assets/images/famfour.png'
            ],
            [
                'package_id' => 10, 
                'package_type' => 'Party', 
                'title' => 'Kids Party', 
                'price' => 9000.00, 
                'image_asset' => 'assets/images/kidsparty.png'
            ],
            [
                'package_id' => 11, 
                'package_type' => 'Party', 
                'title' => 'Birthday Party', 
                'price' => 9000.00, 
                'image_asset' => 'assets/images/birthday.png'
            ],
            [
                'package_id' => 12, 
                'package_type' => 'Party', 
                'title' => 'Debutant', 
                'price' => 12000.00, 
                'image_asset' => 'assets/images/debut.png'
            ],
            [
                'package_id' => 13, 
                'package_type' => 'Wedding', 
                'title' => 'Civil Wedding', 
                'price' => 5000.00, 
                'image_asset' => 'assets/images/civil.png'
            ],
            [
                'package_id' => 14, 
                'package_type' => 'Wedding', 
                'title' => 'Church Wedding', 
                'price' => 7500.00, 
                'image_asset' => 'assets/images/church.png'
            ],
            [
                'package_id' => 15, 
                'package_type' => 'Christening', 
                'title' => 'Christening', 
                'price' => 300.00, 
                'image_asset' => 'assets/images/christening_placeholder.png'
            ],
        ];
        
        foreach ($packages as $package) {
            Package::create($package);
        }
        
        // Seed package inclusions
        $inclusions = [
            ['inclusion_id' => 1, 'package_id' => 1, 'inclusion_text' => '20 mins shoot'],
            ['inclusion_id' => 2, 'package_id' => 1, 'inclusion_text' => 'Unlimited Shots'],
            ['inclusion_id' => 3, 'package_id' => 1, 'inclusion_text' => '1 Backdrop'],
            ['inclusion_id' => 4, 'package_id' => 1, 'inclusion_text' => '10 mins Photo Selection'],
            ['inclusion_id' => 5, 'package_id' => 2, 'inclusion_text' => 'Kids up to 7 years old'],
            ['inclusion_id' => 6, 'package_id' => 2, 'inclusion_text' => 'One Backdrop'],
            ['inclusion_id' => 7, 'package_id' => 2, 'inclusion_text' => '10 Mins Photo Selection'],
            ['inclusion_id' => 8, 'package_id' => 2, 'inclusion_text' => '2pc Single 4R or 2pc Collage 4R'],
            ['inclusion_id' => 9, 'package_id' => 3, 'inclusion_text' => 'Solo Package'],
            ['inclusion_id' => 10, 'package_id' => 3, 'inclusion_text' => '1 Backdrop'],
            ['inclusion_id' => 11, 'package_id' => 3, 'inclusion_text' => '1 A4 Size Print'],
            ['inclusion_id' => 12, 'package_id' => 3, 'inclusion_text' => '1 4R Size Print'],
            ['inclusion_id' => 13, 'package_id' => 3, 'inclusion_text' => '4 Wallet Size Print'],
            ['inclusion_id' => 14, 'package_id' => 4, 'inclusion_text' => '2 Persons'],
            ['inclusion_id' => 15, 'package_id' => 4, 'inclusion_text' => '20 Minutes'],
            ['inclusion_id' => 16, 'package_id' => 4, 'inclusion_text' => 'Unlimited shots'],
            ['inclusion_id' => 17, 'package_id' => 4, 'inclusion_text' => 'One Backdrop'],
            ['inclusion_id' => 18, 'package_id' => 4, 'inclusion_text' => '10 Mins Photo Selection'],
            ['inclusion_id' => 19, 'package_id' => 5, 'inclusion_text' => '3-4 Persons'],
            ['inclusion_id' => 20, 'package_id' => 5, 'inclusion_text' => '20 Minutes'],
            ['inclusion_id' => 21, 'package_id' => 5, 'inclusion_text' => 'Unlimited Slots'],
            ['inclusion_id' => 22, 'package_id' => 5, 'inclusion_text' => 'One Backdrop'],
            ['inclusion_id' => 23, 'package_id' => 5, 'inclusion_text' => '10 Mins Photo Selection'],
            ['inclusion_id' => 24, 'package_id' => 6, 'inclusion_text' => '3-4 Persons'],
            ['inclusion_id' => 25, 'package_id' => 6, 'inclusion_text' => '20 Minutes'],
            ['inclusion_id' => 26, 'package_id' => 6, 'inclusion_text' => 'Unlimited Slots'],
            ['inclusion_id' => 27, 'package_id' => 6, 'inclusion_text' => 'One Backdrop'],
            ['inclusion_id' => 28, 'package_id' => 6, 'inclusion_text' => '10 Mins Photo Selection'],
            ['inclusion_id' => 29, 'package_id' => 7, 'inclusion_text' => '5-6 Persons'],
            ['inclusion_id' => 30, 'package_id' => 7, 'inclusion_text' => '20 Minutes'],
            ['inclusion_id' => 31, 'package_id' => 7, 'inclusion_text' => 'Unlimited Slots'],
            ['inclusion_id' => 32, 'package_id' => 7, 'inclusion_text' => 'Two Backdrop'],
            ['inclusion_id' => 33, 'package_id' => 7, 'inclusion_text' => '10 Mins Photo Selection'],
            ['inclusion_id' => 34, 'package_id' => 8, 'inclusion_text' => '7-9 Persons'],
            ['inclusion_id' => 35, 'package_id' => 8, 'inclusion_text' => '20 Minutes'],
            ['inclusion_id' => 36, 'package_id' => 8, 'inclusion_text' => 'Unlimited Slots'],
            ['inclusion_id' => 37, 'package_id' => 8, 'inclusion_text' => 'Two Backdrop'],
            ['inclusion_id' => 38, 'package_id' => 8, 'inclusion_text' => '10 Mins Photo Selection'],
            ['inclusion_id' => 39, 'package_id' => 9, 'inclusion_text' => '10-15 Persons'],
            ['inclusion_id' => 40, 'package_id' => 9, 'inclusion_text' => '40 Minutes'],
            ['inclusion_id' => 41, 'package_id' => 9, 'inclusion_text' => 'Unlimited Slots'],
            ['inclusion_id' => 42, 'package_id' => 9, 'inclusion_text' => 'Two Backdrop'],
            ['inclusion_id' => 43, 'package_id' => 9, 'inclusion_text' => '10 Mins Photo Selection'],
            ['inclusion_id' => 44, 'package_id' => 10, 'inclusion_text' => '2-3 Hours Photo Coverage'],
            ['inclusion_id' => 45, 'package_id' => 10, 'inclusion_text' => 'Pre-Event Photoshoot'],
            ['inclusion_id' => 46, 'package_id' => 10, 'inclusion_text' => '200 - 300+ Soft Copies'],
            ['inclusion_id' => 47, 'package_id' => 10, 'inclusion_text' => 'All Copies Enhanced Sent'],
            ['inclusion_id' => 48, 'package_id' => 10, 'inclusion_text' => 'Sent Via Google Drive'],
            ['inclusion_id' => 49, 'package_id' => 10, 'inclusion_text' => 'Online Gallery Posted'],
            ['inclusion_id' => 50, 'package_id' => 11, 'inclusion_text' => '2-3 Hours Photo Coverage'],
            ['inclusion_id' => 51, 'package_id' => 11, 'inclusion_text' => 'Pre-Event Photoshoot'],
            ['inclusion_id' => 52, 'package_id' => 11, 'inclusion_text' => '200 - 300+ Soft Copies'],
            ['inclusion_id' => 53, 'package_id' => 11, 'inclusion_text' => 'All Copies Enhanced Sent'],
            ['inclusion_id' => 54, 'package_id' => 11, 'inclusion_text' => 'Sent Via Google Drive'],
            ['inclusion_id' => 55, 'package_id' => 11, 'inclusion_text' => 'Online Gallery Posted'],
            ['inclusion_id' => 56, 'package_id' => 12, 'inclusion_text' => 'Full Event Coverage'],
            ['inclusion_id' => 57, 'package_id' => 12, 'inclusion_text' => 'Pre-Event Photoshoot'],
            ['inclusion_id' => 58, 'package_id' => 12, 'inclusion_text' => '200 - 300+ Soft Copies'],
            ['inclusion_id' => 59, 'package_id' => 12, 'inclusion_text' => 'All Copies Enhanced Sent'],
            ['inclusion_id' => 60, 'package_id' => 12, 'inclusion_text' => 'Sent Via Google Drive'],
            ['inclusion_id' => 61, 'package_id' => 12, 'inclusion_text' => 'Online Gallery Posted'],
            ['inclusion_id' => 62, 'package_id' => 13, 'inclusion_text' => 'Full Event Coverage'],
            ['inclusion_id' => 63, 'package_id' => 13, 'inclusion_text' => 'Pre-Event Photoshoot'],
            ['inclusion_id' => 64, 'package_id' => 13, 'inclusion_text' => '200 - 300+ Soft Copies'],
            ['inclusion_id' => 65, 'package_id' => 13, 'inclusion_text' => 'All Copies Enhanced Sent'],
            ['inclusion_id' => 66, 'package_id' => 13, 'inclusion_text' => 'Sent Via Google Drive'],
            ['inclusion_id' => 67, 'package_id' => 13, 'inclusion_text' => 'Online Gallery Posted'],
            ['inclusion_id' => 68, 'package_id' => 14, 'inclusion_text' => 'Full Event Coverage'],
            ['inclusion_id' => 69, 'package_id' => 14, 'inclusion_text' => 'Pre-Event Photoshoot'],
            ['inclusion_id' => 70, 'package_id' => 14, 'inclusion_text' => '200 - 300+ Soft Copies'],
            ['inclusion_id' => 71, 'package_id' => 14, 'inclusion_text' => 'All Copies Enhanced Sent'],
            ['inclusion_id' => 72, 'package_id' => 14, 'inclusion_text' => 'Sent Via Google Drive'],
            ['inclusion_id' => 73, 'package_id' => 14, 'inclusion_text' => 'Online Gallery Posted'],
            ['inclusion_id' => 74, 'package_id' => 15, 'inclusion_text' => '2-3 Hours Photo Coverage'],
            ['inclusion_id' => 75, 'package_id' => 15, 'inclusion_text' => 'Pre-Event Photoshoot'],
            ['inclusion_id' => 76, 'package_id' => 15, 'inclusion_text' => '200 - 300+ Soft Copies'],
            ['inclusion_id' => 77, 'package_id' => 15, 'inclusion_text' => 'All Copies Enhanced Sent'],
            ['inclusion_id' => 78, 'package_id' => 15, 'inclusion_text' => 'Sent Via Google Drive'],
            ['inclusion_id' => 79, 'package_id' => 15, 'inclusion_text' => 'Online Gallery Posting'],
        ];
        
        foreach ($inclusions as $inclusion) {
            PackageInclusion::create($inclusion);
        }
        
        // Seed package free items
        $freeItems = [
            ['free_item_id' => 1, 'package_id' => 1, 'free_item_text' => 'ALL soft copies'],
            ['free_item_id' => 2, 'package_id' => 1, 'free_item_text' => '1 4r size print'],
            ['free_item_id' => 3, 'package_id' => 2, 'free_item_text' => 'ALL Soft Copies'],
            ['free_item_id' => 4, 'package_id' => 2, 'free_item_text' => 'Two 4R Size Print'],
            ['free_item_id' => 5, 'package_id' => 2, 'free_item_text' => 'Number Balloon'],
            ['free_item_id' => 6, 'package_id' => 3, 'free_item_text' => 'ALL Soft Copies'],
            ['free_item_id' => 7, 'package_id' => 3, 'free_item_text' => 'Hard Copies'],
            ['free_item_id' => 8, 'package_id' => 4, 'free_item_text' => 'ALL Soft Copies'],
            ['free_item_id' => 9, 'package_id' => 4, 'free_item_text' => 'Two 4R Size Print'],
            ['free_item_id' => 10, 'package_id' => 5, 'free_item_text' => 'ALL Soft Copies'],
            ['free_item_id' => 11, 'package_id' => 5, 'free_item_text' => 'Four 4R Size Print'],
            ['free_item_id' => 12, 'package_id' => 6, 'free_item_text' => 'ALL Soft Copies'],
            ['free_item_id' => 13, 'package_id' => 6, 'free_item_text' => 'Two 4R Size Print'],
            ['free_item_id' => 14, 'package_id' => 6, 'free_item_text' => 'One A4 Size Print'],
            ['free_item_id' => 15, 'package_id' => 7, 'free_item_text' => 'ALL Soft Copies'],
            ['free_item_id' => 16, 'package_id' => 7, 'free_item_text' => 'Two 4R Size Print'],
            ['free_item_id' => 17, 'package_id' => 7, 'free_item_text' => 'One A4 Size Print'],
            ['free_item_id' => 18, 'package_id' => 8, 'free_item_text' => 'ALL Soft Copies'],
            ['free_item_id' => 19, 'package_id' => 8, 'free_item_text' => 'Four 4R Size Print'],
            ['free_item_id' => 20, 'package_id' => 8, 'free_item_text' => 'Two A4 Size Print'],
            ['free_item_id' => 21, 'package_id' => 9, 'free_item_text' => 'ALL Soft Copies'],
            ['free_item_id' => 22, 'package_id' => 9, 'free_item_text' => 'Four 4R Size Print'],
            ['free_item_id' => 23, 'package_id' => 9, 'free_item_text' => 'Two A4 Size Print'],
        ];
        
        foreach ($freeItems as $freeItem) {
            PackageFreeItem::create($freeItem);
        }
    }
}
