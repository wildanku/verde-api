<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateRoomRequest;
use App\Http\Requests\Admin\CreateRoomTimeSlotDateRequest;
use App\Http\Requests\Admin\CreateRoomTimeSlotRequest;
use App\Http\Requests\Search\FindRoomRequest;
use App\Http\Requests\Search\StartEndDateRequest;
use App\Http\Resources\RoomResource;
use App\Http\Resources\RoomTimeSlotResource;
use App\Models\Room;
use App\Models\RoomTimeSlot;
use App\Services\RoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomManagementController extends Controller
{
    public $roomService;

    public function __construct()
    {
        $this->roomService = new RoomService();
    }

    public function index(FindRoomRequest $request)
    {
        $rooms = $this->roomService->findForAdmin($request)->paginate(perPage: $request->offset ?? 20);

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

    public function create(CreateRoomRequest $request)
    {
        DB::transaction(function() use($request) {
            Room::create($request->all());
        });

        return response([
            'success' => true,
            'message' => 'The new room has been added successfuly'
        ],200);
    }

    public function addTimeSlot(Room $room, CreateRoomTimeSlotRequest $request)
    {
        $this->roomService->addTimeSlotDaterange($room, $request);

        return response([
            'success'   => true,
            'message'   => 'Time slots has been added'
        ],200);
    }

    public function addTimeSlotDate(Room $room, CreateRoomTimeSlotDateRequest $request)
    {
        $this->roomService->addTimeSlotDate($room, $request);
        
        return response([
            'success'   => true,
            'message'   => 'Time slots has been added'
        ],200);
    }

    public function show(Room $room)
    {
        return response([
            'success'   => true,
            'data'      => new RoomResource($room)
        ], 200);
    }

    public function timeslots(Room $room, StartEndDateRequest $request)
    {
        $timeslots = $this->roomService->findRoomTimeslots($room, $request)->paginate(perPage: $request->offset ?? 20);

        return response([
            'success'   => true,
            'data'      => [
                'per_page'      => $timeslots->perPage(),
                'current_page'  => $timeslots->currentPage(),
                'total_page'    => $timeslots->lastPage(),
                'total_items'   => $timeslots->count(), 
                'items'         => RoomTimeSlotResource::collection($timeslots)
            ]
        ]);
    }

    public function deleteTimeSlot(Room $room, Request $request) 
    {   
        $request->validate([
            'date'   => 'required|array|min:1',
            'date.*' => 'required|date'
        ]);
        
        DB::transaction(function() use($room, $request) {
            $room->timeslots()->whereIn('date', $request->date)->delete();
        });

        return response([
            'success'   => true,
            'message'   => 'Time slot for this room has been deleted'
        ], 200);
    }

    public function deleteAllTimeSlot(Room $room) 
    {   
        DB::transaction(function() use($room) {
            $room->timeslots()->delete();
        });

        return response([
            'success'   => true,
            'message'   => 'All time slot for this room has been deleted'
        ], 200);

    }
}
