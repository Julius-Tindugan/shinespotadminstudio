<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Addon;

class BookingExpenditureService
{
    /**
     * Calculate the total expenditure for a booking
     *
     * @param mixed $booking The booking object or booking ID
     * @return array Returns an array with subtotals and total
     */
    public function calculateExpenditures($booking)
    {
        if (!is_object($booking)) {
            $booking = Booking::with(['package', 'addons', 'backdrop'])->findOrFail($booking);
        }

        // Initialize expenditure array
        $expenditures = [
            'package' => 0,
            'addons' => 0,
            'addon_details' => [],
            'backdrop' => null,
            'subtotal' => 0,
            'total' => 0
        ];

        // Calculate package cost if a package is selected
        if ($booking->package) {
            $expenditures['package'] = $booking->package->price;
        }

        // Calculate addons cost
        if ($booking->hasAddons()) {
            if ($booking->relationLoaded('addons') && $booking->addons instanceof \Illuminate\Database\Eloquent\Collection) {
                // Using relationship data
                foreach ($booking->addons as $addon) {
                    $quantity = $addon->pivot->quantity ?? 1;
                    $addonCost = $addon->addon_price * $quantity;
                    
                    $expenditures['addon_details'][] = [
                        'name' => $addon->addon_name,
                        'price' => $addon->addon_price,
                        'quantity' => $quantity,
                        'total' => $addonCost
                    ];
                    
                    $expenditures['addons'] += $addonCost;
                }
            } elseif (is_array($booking->addons)) {
                // Using array data from addons column
                foreach ($booking->addons as $addonData) {
                    $quantity = (int)($addonData['quantity'] ?? 1);
                    $price = (float)($addonData['price'] ?? 0);
                    $addonCost = $price * $quantity;
                    
                    $expenditures['addon_details'][] = [
                        'name' => $addonData['name'] ?? 'Unknown Addon',
                        'price' => $price,
                        'quantity' => $quantity,
                        'total' => $addonCost
                    ];
                    
                    $expenditures['addons'] += $addonCost;
                }
            }
        }
        
        // Services calculation removed

        // Add backdrop information
        if ($booking->backdrop_id && $booking->backdrop) {
            $expenditures['backdrop'] = [
                'name' => $booking->backdrop->name,
                'color_code' => $booking->backdrop->color_code
            ];
        } elseif ($booking->custom_backdrop) {
            $expenditures['backdrop'] = [
                'name' => $booking->custom_backdrop,
                'custom' => true
            ];
        }
        
        // Calculate subtotal (package + addons only - services removed)
        $expenditures['subtotal'] = $expenditures['package'] + $expenditures['addons'];
        
        // For now, total is same as subtotal, but could add taxes, discounts, etc. later
        $expenditures['total'] = $expenditures['subtotal'];

        return $expenditures;
    }
    
    /**
     * Calculate expenditures for a new booking based on form data
     *
     * @param array $formData Form data including package_id and addons
     * @return array Returns an array with subtotals and total
     */
    public function calculateExpendituresFromFormData($formData)
    {
        // Initialize expenditure array
        $expenditures = [
            'package' => 0,
            'addons' => 0,
            'addon_details' => [],
            'backdrop' => null,
            'subtotal' => 0,
            'total' => 0
        ];

        // Calculate package cost if a package is selected
        if (!empty($formData['package_id'])) {
            $package = Package::find($formData['package_id']);
            if ($package) {
                $expenditures['package'] = $package->price;
            }
        }

        // Calculate addons cost
        if (!empty($formData['addons']) && is_array($formData['addons'])) {
            foreach ($formData['addons'] as $addonId) {
                $addon = Addon::find($addonId);
                if ($addon) {
                    $quantity = $formData['addon_qty'][$addonId] ?? 1;
                    $addonCost = $addon->addon_price * $quantity;
                    
                    $expenditures['addon_details'][] = [
                        'name' => $addon->addon_name,
                        'price' => $addon->addon_price,
                        'quantity' => $quantity,
                        'total' => $addonCost
                    ];
                    
                    $expenditures['addons'] += $addonCost;
                }
            }
        }

        // Add backdrop information
        if (!empty($formData['backdrop_id']) && $formData['backdrop_id'] !== 'custom') {
            $backdrop = \App\Models\Backdrop::find($formData['backdrop_id']);
            if ($backdrop) {
                $expenditures['backdrop'] = [
                    'name' => $backdrop->name,
                    'color_code' => $backdrop->color_code
                ];
            }
        } elseif (!empty($formData['custom_backdrop'])) {
            $expenditures['backdrop'] = [
                'name' => $formData['custom_backdrop'],
                'custom' => true
            ];
        }
        
        // Services calculation removed
        
        // Calculate subtotal and total (without services)
        $expenditures['subtotal'] = $expenditures['package'] + $expenditures['addons'];
        $expenditures['total'] = $expenditures['subtotal'];

        return $expenditures;
    }
}
