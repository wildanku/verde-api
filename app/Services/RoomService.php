<?php 

namespace App\Services;

use App\Http\Requests\Search\CheckinCheckoutRequest;
use App\Http\Requests\Search\FindRoomRequest;
use App\Http\Requests\Search\StartEndDateRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomTimeSlot;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

Class RoomService
{
    /**
     * @return App\Models\Room 
     */
    public function findForAdmin(FindRoomRequest $request)
    {
        $roomQuery = Room::query()
            ->when($request->name, fn ($q) => $q->where('name','like','%'.$request->name.'%'))
            ->when($request->theme, fn ($q) => $q->where('name',$request->theme))
            ->when($request->pax, fn ($q) => $q->where('pax','>=',$request->pax))
            ->when($request->checkin && $request->checkout, 
                    fn ($q) => 
                        $q->whereHas('timeslots', 
                            fn(Builder $table) => $table->whereBetween('date', [$request->checkin, $request->checkout])
                        )
            );
        
        return $roomQuery;
    }

    /**
     * @return App\Models\Room 
     */
    public function findForUser(FindRoomRequest $request)
    {
        $roomQuery = Room::query()
            ->when($request->name, fn ($q) => $q->where('name','like','%'.$request->name.'%'))
            ->when($request->theme, fn ($q) => $q->where('name',$request->theme))
            ->when($request->pax, fn ($q) => $q->where('pax','>=',$request->pax))
            ->when($request->checkin && $request->checkout, 
                    fn ($q) => 
                        $q->whereHas('timeslots', 
                            fn (Builder $table) => $table->whereBetween('date', [$request->checkin, $request->checkout])
                        )
                )
            ->whereDoesntHave('bookings', function (Builder $q) use($request) {
                    $q->whereBetween('checkin',[$request->checkin, $request->checkout]);
                    $q->whereBetween('checkout',[$request->checkin, $request->checkout]);
                }
            );
        
        return $roomQuery;
    }

    /**
     * @return App\Models\Room
     */
    public function findRoomTimeslots(Room $room, StartEndDateRequest $request)
    {
        $timeslots = $room->timeslots()
            ->when($request->start_at && $request->end_at, fn ($q) => $q->whereBetween('date', [$request->start_at, $request->end_at]));

        return $timeslots;
    }

    public function findRoomTimeslotsUser($room, CheckinCheckoutRequest $request)
    {
        $bookedDate = [];
        // parse all booking date to array
        // to do that, we retrieve the data from DB 
        $findBookingDate = Booking::select('room_id','checkin','checkout','status')
            ->where(['room_id' => $room->id, 'status' => 'confirmed'])
            ->whereBetween('checkin',[$request->checkin, $request->checkout])
            ->WhereBetween('checkout',[$request->checkin, $request->checkout])
            ->get();
            
        // push to booked date array
        foreach($findBookingDate as $date) {
            // loop into date range
            $begin = new DateTime($date->checkin);
            $end = new DateTime($date->checkout);

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, $end);

            // then loop from the period
            foreach ($period as $dt) {
                array_push($bookedDate, $dt->format('Y-m-d'));
            }
        }

        $timeslots = $room->timeslots()
            ->when($request->checkin && $request->checkout, fn ($q) => $q->whereBetween('date', [$request->checkin, $request->checkout]))
            ->whereNotIn('date', $bookedDate);

        return $timeslots;
    }

    public function addTimeSlotDaterange(Room $room, $request)
    {
        return DB::transaction(function () use($room, $request) {
            
            // define start and end date.
            $begin = new DateTime($request->start_at);
            $end = new DateTime($request->end_at);

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, $end);

            // then loop from the period
            foreach ($period as $dt) {

                // save with the proper configuration date
                if ($request->is_weekend_only) {
                    if($dt->format('D') == 'Sat' || $dt->format('D') == 'Sun') {
                        $room->timeslots()->updateOrCreate([
                            'date' => $dt->format('Y-m-d')
                        ],[
                            'date'  => $dt->format('Y-m-d'),
                            'price' => $request->price ?? null 
                        ]);
                    }

                } elseif ($request->is_weekday_only) {
                    if($dt->format('D') != 'Sat' && $dt->format('D') != 'Sun') {
                        $room->timeslots()->updateOrCreate([
                            'date' => $dt->format('Y-m-d')
                        ],[
                            'date'  => $dt->format('Y-m-d'),
                            'price' => $request->price ?? null 
                        ]);
                    }

                } elseif ($request->days) {
                    if(in_array(strtolower($dt->format('D')), $request->days)) {
                        $room->timeslots()->updateOrCreate([
                            'date' => $dt->format('Y-m-d')
                        ],[
                            'date'  => $dt->format('Y-m-d'),
                            'price' => $request->price ?? null 
                        ]);
                    }

                } else {
                    $room->timeslots()->updateOrCreate([
                        'date' => $dt->format('Y-m-d')
                    ],[
                        'date'  => $dt->format('Y-m-d'),
                        'price' => $request->price ?? null 
                    ]);
                }
            }
        });
    }

    public function addTimeSlotDate(Room $room, $request)
    {
        return DB::transaction(function() use($room, $request) {
            foreach($request->date as $date) {
                $room->timeslots()->updateOrCreate([
                    'date'  => $date
                ],[
                    'date'  => $date,
                    'price' => $request->price ?? null
                ]);
            }
        });
    }
}