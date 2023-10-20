<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\SystemStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendNotification;

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

        $bookDouble = Booking::where([['date', '=', $request->b_date], ['phone', '=', $request->phone], ['status', '=', 0]])->get();
        if (count($bookDouble) > 0) {
            return response()->json([
                    'status'=> 'ERROR',
                    'data' => null,
                    'message'=> 'Oops! You have pending booking for today!',
                ]
            );
        }

        $booking = new Booking();
        $booking->name = $request->name;
        $booking->phone = $request->phone;
        $booking->date = $request->b_date;
        $booking->time = $request->b_time;
        $booking->save();

        try{
            Notification::send($booking, new SendNotification());
        }catch(\Exception $e){
            
        }

        return response()->json([
                'status' => 'OK',
                'data' => $booking,
                'message'=> 'Booking Success!',
            ]
        );
    }

    public function bookingGet($phone) {
        if ($phone == 'all') {
            $booking = Booking::all();
        } else {
            $booking = Booking::where([['phone', '=', $phone], ['status', '=', 0]])->first();
        }
        
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


