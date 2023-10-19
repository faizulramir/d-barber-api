<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\SystemStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function bookingPost(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'phone'=> 'required',
            'b_date'=> 'required',
            'b_time'=> 'required'
        ]); 

        if ($validator->fails()) {
            return response()->json([
                    'status'=> 'ERROR',
                    'data' => null,
                    'message'=> $validator->errors()->first(),
                ]
            );
        }

        $system_status = SystemStatus::find(1);

        if (!$system_status->status) {
            return response()->json([
                    'status'=> 'ERROR',
                    'data' => null,
                    'message'=> 'Oops! Shop Closed!',
                ]
            );
        }
        
        $bookHistory = Booking::where('date', $request->b_date)->get();
        if ($bookHistory) {
            foreach ($bookHistory as $key => $b) {
                if ($b->time == $request->b_time) {
                    return response()->json([
                            'status'=> 'ERROR',
                            'data' => null,
                            'message'=> 'Oops! Someone "Cop" '.$b->time.'!',
                        ]
                    );
                }
            }
        }

        $booking = new Booking();
        $booking->name = $request->name;
        $booking->phone = $request->phone;
        $booking->date = $request->b_date;
        $booking->time = $request->b_time;
        $booking->save();

        return response()->json([
                'status' => 'OK',
                'data' => $booking,
                'message'=> 'Booking Success!',
            ]
        );
    }

    public function bookingGet($phone) {
        $booking = Booking::where([['phone', '=', $phone], ['status', '=', 0]])->first();
        if (!$booking) {
            return response()->json([
                    'status'=> 'ERROR',
                    'data' => null,
                    'message'=> 'Booking not found',
                ]
            );
        }

        return response()->json([
                'status' => 'OK',
                'data' => $booking,
                'message'=> 'Success!',
            ]
        );
    }
}


