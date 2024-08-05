<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shock extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    public function shock_point()
    {
        return $this->belongsTo(ShockPoint::class,);
    }

    public function repair_works(): HasMany
    {
        return $this->hasMany(RepairWork::class,'shock_id')->with('designation');
    }
}
