<?php 

namespace App\Services;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomTimeSlot;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

Class BookingService 
{
    public function book(BookingRequest $request)
    {
        $room = Room::find($request->room_id);

        // validate booking date
        $hasBooking = $this->validateBookDate($request);

        if ($hasBooking) {
            return [
                'success'   => false,
                'message'   => 'Booking date is taken, please make booking for another date',
                'code'      => 422
            ];
        }

        if ($request->pax > $room->pax) {
            return [
                'success'   => false,
                'message'   => 'The number of people exceeds the capacity of the room',
                'code'      => 422
            ];
        }
        
        $subTotal = 0;
        $discount = 0;

        // get total price (each date might have a different price) and checking is the date available to that room time slot
        $begin = new DateTime($request->checkin);
        $end = new DateTime($request->checkout);
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $dateyear = $dt->format('Y-m-d');
            $date = $dt->format('m-d');

            // checking date availability to each room
            if(!in_array($dateyear, $room->timeslots->pluck('date')->toArray())) {
                return [
                    'success'   => false,
                    'message'   => $dateyear.' is not available to this room time slot',
                    'code'      => 422
                ];
            }

            $getPrice = RoomTimeSlot::where(['room_id' => $room->id, 'date' => $dateyear])->first()->price ?? $room->price;
            $subTotal += $getPrice;

            // applying discount when user book on user's birthdate
            $birthDate = Carbon::parse(auth('users')->user()->birth_date)->format('m-d');
            if($birthDate == $date){
                $discount = $getPrice * 0.1;
            }
        }

        $booking = DB::transaction(function () use($room, $request, $discount, $subTotal) {
            return 
                Booking::create([
                    'user_id'       => auth('users')->user()->id,
                    'room_id'       => $room->id,
                    'pax'           => $request->pax,
                    'checkin'       => $request->checkin,
                    'checkout'      => $request->checkout,
                    'sub_total'     => $subTotal,
                    'discount'      => $discount,
                    'grand_total'   => $subTotal - $discount,
                    'notes'         => $request->notes,
                    'status'        => 'confirmed'
                ]);
        });

        return [
            'success'   => true,
            'message'   => 'Booking is confirmed',
            'data'      => $booking,
            'code'      => 200
        ];
    }

    public function validateBookDate(BookingRequest $request)
    {
        return 
            Booking::where('room_id',$request->room_id)
                ->where('status','confirmed')
                ->whereBetween('checkin',[$request->checkin, $request->checkout])
                ->whereBetween('checkout',[$request->checkin, $request->checkout])
                ->exists();
    }
}