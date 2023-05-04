<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name','theme','pax','description','price','added_by'];

    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->added_by = auth('admins')?->user()?->id;
        });
    }

    public function timeslots()
    {
        return $this->hasMany(RoomTimeSlot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

}   

