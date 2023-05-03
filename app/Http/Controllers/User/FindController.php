<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\CheckinCheckoutRequest;
use App\Http\Requests\Search\FindRoomRequest;
use App\Http\Requests\Search\StartEndDateRequest;
use App\Http\Resources\RoomResource;
use App\Http\Resources\RoomTimeSlotResource;
use App\Models\Room;
use App\Models\RoomTimeSlot;
use App\Services\RoomService;
use Illuminate\Http\Request;

class FindController extends Controller
{
    public $roomService;

    public function __construct()
    {
        $this->roomService = new RoomService();
    }

    public function rooms(FindRoomRequest $request)
    {
        $rooms = $this->roomService->findForUser($request)->paginate(perPage: $request->offset ?? 20);

        return response([
            'success'   => true,
            'data'      => [
                'per_page'      => $rooms->perPage(),
                'current_page'  => $rooms->currentPage(),
                'total_page'    => $rooms->lastPage(),
                'total_items'   => $rooms->count(), 
                'items'         => RoomResource::collection($rooms)
            ]
        ], 200);
    }

    public function showRoom(Room $room)
    {
        return response([
            'success'   => true,
            'data'      => new RoomResource($room)
        ], 200);
    }

    public function roomTimeslots(Room $room, CheckinCheckoutRequest $request)
    {
        $timeslots = $this->roomService->findRoomTimeslotsUser($room, $request)->paginate(perPage: $request->offset ?? 20);
        return response([
            'success'   => true,
            'data'      => RoomTimeSlotResource::collection($timeslots)
        ], 200);
    }
}
