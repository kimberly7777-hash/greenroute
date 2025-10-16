<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'location',
        'collection_fee',
        'frequency',
        'description',
        'is_active'
    ];

    protected $casts = [
        'collection_fee' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get billing rate by category and location
     */
    public static function getRateByLocation($category, $location, $frequency = null)
    {
        $query = self::where('category', $category)
            ->where('location', $location)
            ->where('is_active', true);
        
        if ($frequency) {
            $query->where('frequency', $frequency);
        }
        
        return $query->first();
    }

    /**
     * Get all active rates grouped by category
     */
    public static function getActiveRatesGrouped()
    {
        return self::where('is_active', true)
            ->orderBy('category')
            ->orderBy('location')
            ->get()
            ->groupBy('category');
    }
}
