<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id','user_id','room_id','pax','checkin','checkout','sub_total','discount','grand_total','notes','status'];

    public $incrementing = false;

    protected $keyType = 'string';

    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $code = Str::random(8);
            while($model->checkIfCodeExists($code)) {
                $code = Str::random(8);
            }
            $model->id = strtoupper($code);
        });
    }

    private function checkIfCodeExists($code)
    {
        return self::where('id', $code)->exists();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getTotalDay()
    {
        $start = new DateTime($this->checkin);
        $end = new DateTime($this->checkout);

        return (int) $end->diff($start)->format("%a");
    }

}
