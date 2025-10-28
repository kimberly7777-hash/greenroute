<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    /**
     * Cache duration in seconds (24 hours)
     */
    protected const CACHE_DURATION = 86400;

    /**
     * Get all regions with caching
     */
    public function getRegions()
    {
        return Cache::remember('locations:regions', self::CACHE_DURATION, function () {
            return Location::distinctRegions()->pluck('region');
        });
    }

    /**
     * Get districts for a region with caching
     */
    public function getDistricts(string $region)
    {
        $cacheKey = "locations:districts:{$region}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($region) {
            return Location::byRegion($region)
                ->select('district')
                ->distinct()
                ->orderBy('district')
                ->pluck('district');
        });
    }

    /**
     * Get wards for a district with caching
     */
    public function getWards(string $region, string $district)
    {
        $cacheKey = "locations:wards:{$region}:{$district}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($region, $district) {
            return Location::byRegion($region)
                ->byDistrict($district)
                ->select('ward')
                ->distinct()
                ->orderBy('ward')
                ->pluck('ward');
        });
    }

    /**
     * Get streets for a ward with caching
     */
    public function getStreets(string $region, string $district, string $ward)
    {
        $cacheKey = "locations:streets:{$region}:{$district}:{$ward}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($region, $district, $ward) {
            return Location::byRegion($region)
                ->byDistrict($district)
                ->byWard($ward)
                ->whereNotNull('street')
                ->where('street', '!=', '')
                ->select('street')
                ->distinct()
                ->orderBy('street')
                ->pluck('street');
        });
    }

    /**
     * Search locations by keyword
     */
    public function searchLocations(string $keyword, int $limit = 50)
    {
        return Location::search($keyword)
            ->select('region', 'district', 'ward', 'street')
            ->distinct()
            ->limit($limit)
            ->get();
    }

    /**
     * Get location statistics
     */
    public function getStatistics()
    {
        return Cache::remember('locations:statistics', self::CACHE_DURATION, function () {
            return [
                'total_records' => Location::count(),
                'unique_regions' => Location::distinct('region')->count('region'),
                'unique_districts' => Location::distinct('district')->count('district'),
                'unique_wards' => Location::distinct('ward')->count('ward'),
                'locations_with_streets' => Location::whereNotNull('street')
                    ->where('street', '!=', '')
                    ->count(),
            ];
        });
    }

    /**
     * Find locations by exact match
     */
    public function findExact(string $region, ?string $district = null, ?string $ward = null, ?string $street = null)
    {
        $query = Location::byRegion($region);

        if ($district) {
            $query->byDistrict($district);
        }

        if ($ward) {
            $query->byWard($ward);
        }

        if ($street) {
            $query->where('street', $street);
        }

        return $query->get();
    }

    /**
     * Clear all location caches
     */
    public function clearCache()
    {
        Cache::forget('locations:regions');
        Cache::forget('locations:statistics');
        
        // Clear pattern-based caches
        if (method_exists(Cache::getStore(), 'flush')) {
            // For stores that support flushing
            $keys = Cache::getStore()->getRedis()->keys('locations:*');
            foreach ($keys as $key) {
                Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
            }
        }
        
        return true;
    }

    /**
     * Validate if location exists
     */
    public function validateLocation(string $region, string $district, string $ward, ?string $street = null): bool
    {
        $query = Location::byRegion($region)
            ->byDistrict($district)
            ->byWard($ward);

        if ($street) {
            $query->where('street', $street);
        }

        return $query->exists();
    }
}
