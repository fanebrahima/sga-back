<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repair extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function repairer()
    {
        return $this->belongsTo(Repairer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class)->with('brand','color');
    }

    public function insurer()
    {
        return $this->belongsTo(Insurer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function shocks(): HasMany
    {
        return $this->hasMany(Shock::class,'repair_id')->with('shock_point','repair_works');
    }
}
