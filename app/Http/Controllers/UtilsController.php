<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\SystemStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UtilsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function timesGet(Request $request, $date) {
        
        $times = [
            [
                "time" => '8:00',
                "type" => 'am'
            ],
            [
                "time" => '9:00',
                "type" => 'am'
            ],
            [
                "time" => '10:00',
                "type" => 'am'
            ],
            [
                "time" => '11:00',
                "type" => 'am'
            ],
            [
                "time" => '12:00',
                "type" => 'pm'
            ],
            [
                "time" => '13:00',
                "type" => 'pm'
            ],
            [
                "time" => '14:00',
                "type" => 'pm'
            ],
            [
                "time" => '15:00',
                "type" => 'pm'
            ],
            [
                "time" => '16:00',
                "type" => 'pm'
            ],
            [
                "time" => '17:00',
                "type" => 'pm'
            ],
            [
                "time" => '18:00',
                "type" => 'pm'
            ],
            [
                "time" => '19:00',
                "type" => 'pm'
            ],
            [
                "time" => '20:00',
                "type" => 'pm'
            ],
            [
                "time" => '21:00',
                "type" => 'pm'
            ],
            [
                "time" => '22:00',
                "type" => 'pm'
            ],
            [
                "time" => '23:00',
                "type" => 'pm'
            ],
        ];

        $bookings = Booking::where('date', $date)->get();
        foreach ($times as $t => $time) {
            foreach ($bookings as $booking) {
                if ($booking->time == $time['time'].' '.strtoupper($time['type'])) {
                    $times[$t]['booked'] = true;
                }
            }
        }
        
        return response()->json([
                'status' => 'OK',
                'data' => $times,
                'message'=> 'Success!',
            ]
        );
    }

    public function shopGet(Request $request) {
        $system_status = SystemStatus::find(1);
        if ($request->status !== null) {
            $system_status->status = $request->status == '1' ? 0 : 1;
            $system_status->save();

            return response()->json([
                'status'=> 'OK',
                'data'=> $system_status->status,
                'message'=> 'Shop '. ($system_status->status == 1 ? 'Opened!' : 'Closed!')
            ]);
        }

       
        return response()->json([
            'status'=> 'OK',
            'data'=> $system_status->status,
            'message'=> 'Shop '. ($system_status->status == 1 ? 'Opened!' : 'Closed!')
        ]);
    }
}


