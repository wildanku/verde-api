<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingTest extends TestCase
{
    public function test_user_can_book(): void
    {
        $loginRes = $this->json('POST','/user/auth/login', [
            'email'     => 'doe@mail.com',
            'password'  => 'asdf1234'
        ])->getOriginalContent();

        $token = $loginRes['data']['token'];

        $roomId = 3;
        $checkin = Carbon::today()->addDay()->format('Y-m-d');
        $checkout = Carbon::today()->addDays(5)->format('Y-m-d');

        Booking::where(['room_id' => $roomId])->delete();

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])->post('/user/booking/create', [
            'room_id'   => $roomId,
            'pax'       => 2,
            'checkin'   => $checkin,
            'checkout'  => $checkout,
            'notes'     => 'This is a notes for booking'
        ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_book_room_with_date_booked(): void
    {
        $loginRes = $this->json('POST','/user/auth/login', [
            'email'     => 'doe@mail.com',
            'password'  => 'asdf1234'
        ])->getOriginalContent();

        $token = $loginRes['data']['token'];

        $roomId = 3;
        $checkin = Carbon::today()->addDay()->format('Y-m-d');
        $checkout = Carbon::today()->addDays(5)->format('Y-m-d');

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])->post('/user/booking/create', [
            'room_id'   => $roomId,
            'pax'       => 2,
            'checkin'   => $checkin,
            'checkout'  => $checkout,
            'notes'     => 'This is a notes for booking'
        ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_book_with_exceed_pax()
    {
        $loginRes = $this->json('POST','/user/auth/login', [
            'email'     => 'doe@mail.com',
            'password'  => 'asdf1234'
        ])->getOriginalContent();

        $token = $loginRes['data']['token'];

        $roomId = 1;
        $checkin = Carbon::today()->addDay()->format('Y-m-d');
        $checkout = Carbon::today()->addDays(5)->format('Y-m-d');

        Booking::where(['room_id' => $roomId])->delete();

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])->post('/user/booking/create', [
            'room_id'   => $roomId,
            'pax'       => 8,
            'checkin'   => $checkin,
            'checkout'  => $checkout,
            'notes'     => 'This is a notes for booking'
        ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_book_room_with_date_not_available_to_that_room(): void
    {
        $loginRes = $this->json('POST','/user/auth/login', [
            'email'     => 'doe@mail.com',
            'password'  => 'asdf1234'
        ])->getOriginalContent();

        $token = $loginRes['data']['token'];
        $userId = $loginRes['data']['user']['id'];

        $roomId = 1;
        $checkin = Carbon::today()->addDay()->format('Y-m-d');
        $checkout = Carbon::today()->addDays(5)->format('Y-m-d');

        $room = Room::find($roomId);
        $room->timeslots()->where('date',$checkin)->delete();

        Booking::where(['user_id' => $userId, 'room_id' => $roomId, 'checkin' => $checkin, 'checkout' => $checkout])->delete();

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])->post('/user/booking/create', [
            'room_id'   => $roomId,
            'pax'       => 8,
            'checkin'   => $checkin,
            'checkout'  => $checkout,
            'notes'     => 'This is a notes for booking'
        ]);

        $response->assertStatus(422);
    }

    public function test_user_birthday_discount(): void
    {
        // create user with spesicif birth day
        User::where('email','mahabi@mail.test')->delete();
        $registerRes = $this->json('POST','/user/auth/register', [
            'name'                  => 'Mahabi',
            'email'                 => 'mahabi@mail.test',
            'phone'                 => '0505123984',
            'birth_date'            => '1997-11-30',
            'password'              => 'password',
            'password_confirmation' => 'password'
        ])->getOriginalContent();

        $token = $registerRes['data']['token'];
        $userId = $registerRes['data']['user']['id'];
        $price = 40;

        // create room
        $room = Room::updateOrCreate([
            'name'          => 'Testing Escape Room'
        ],[
            'name'          => 'Testing Escape Room',
            'theme'         => 'testing-theme',
            'pax'           => 2,
            'price'         => $price,
            'description'   => 'Testing Description'
        ]);

        // add timeslot
        $year = Carbon::today()->addYear()->format('Y');
        $dateSimiliarWithBirthday = $year.'-11-30';
        $room->timeslots()->create(['date' => $dateSimiliarWithBirthday, 'price' => null]);
        $room->timeslots()->create(['date' => $year.'-11-28', 'price' => null]);
        $room->timeslots()->create(['date' => $year.'-11-29', 'price' => null]);
        $room->timeslots()->create(['date' => $year.'-12-01', 'price' => null]);
        $room->timeslots()->create(['date' => $year.'-12-02', 'price' => null]);

        $checkin = $year.'-11-29';
        $checkout = $year.'-12-01';

        Booking::where(['room_id' => $room->id])->delete();

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])->json('POST','/user/booking/create', [
            'room_id'   => $room->id,
            'pax'       => 2,
            'checkin'   => $checkin,
            'checkout'  => $checkout,
            'notes'     => 'This is a notes for booking'
        ])->getOriginalContent();
        
        $discountShouldBe = $price * 0.1;
        $bookingDiscount = $response['data']['discount'];

        $this->assertTrue($bookingDiscount == $discountShouldBe);
    }
}
