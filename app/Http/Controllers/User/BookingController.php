<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public $bookingService;

    public function __construct()
    {
        $this->bookingService = new BookingService();
    }

    public function all(Request $request)
    {
        $booking = Booking::where('user_id',auth('users')->user()->id)->get();

        return response([
            'success'   => true,
            'data'      => BookingResource::collection($booking)
        ], 200);
    }

    public function create(BookingRequest $request)
    {
        $booking = $this->bookingService->book($request);

        return response([
            'success'   => $booking['success'],
            'message'   => $booking['message'],
            'data'      => isset($booking['data']) ? $booking['data'] : null
        ], $booking['code']);
    }

    public function show(Booking $booking)
    {
        return response([
            'success'   => true,
            'data'      => new BookingResource($booking)
        ], 200);
    }

    public function cancel(Booking $booking)
    {
        DB::transaction(function() use($booking) {
            $booking->status = 'canceled';
            $booking->save();
        });

        return response([
            'success' => true,
            'messag'  => 'Booking has been canceled'
        ], 200);
    }
}
