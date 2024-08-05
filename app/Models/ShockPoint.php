<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShockPoint extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function shocks(): HasMany
    {
        return $this->belongsTo(Shock::class)->with('repair');
    }
}
