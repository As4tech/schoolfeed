<?php

namespace App\Helpers;

use App\Helpers\SettingsHelper;

class CurrencyHelper
{
    /**
     * Get the currency symbol for the current school
     * 
     * @param int|string|null $schoolId
     * @return string
     */
    public static function getSymbol($schoolId = null)
    {
        if (!$schoolId) {
            $schoolId = SettingsHelper::getCurrentSchoolId();
        }
        
        $currency = SettingsHelper::getSetting($schoolId, 'currency', 'GHS');
        
        return match($currency) {
            'GHS' => 'GH₵',
            'USD' => '$',
            'NGN' => '₦',
            'EUR' => '€',
            'GBP' => '£',
            default => $currency,
        };
    }
    
    /**
     * Format a price with the currency symbol
     * 
     * @param float $amount
     * @param int|string|null $schoolId
     * @return string
     */
    public static function format($amount, $schoolId = null)
    {
        $symbol = self::getSymbol($schoolId);
        return $symbol . number_format($amount, 2);
    }
}
