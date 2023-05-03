<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTimeSlot extends Model
{
    use HasFactory;

    protected $fillable = ['room_id','date','price','notes'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
