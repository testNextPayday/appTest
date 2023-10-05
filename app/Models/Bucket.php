<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Bucket extends Model
{
    use Sluggable;
    
    protected $guarded = [];
    
    protected $dates = ['last_sweep', 'last_peak_sweep'];
    
    public function employers()
    {
        return $this->hasMany(Employer::class);
    }
    
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('employerCount', function ($builder) {
           $builder->withCount('employers');
        });
    }
    
    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
