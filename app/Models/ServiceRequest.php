<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'latitude', 'longitude', 'contractor_id', 'client_id', 'status', 'notes', 'rejection_reason'
    ];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
