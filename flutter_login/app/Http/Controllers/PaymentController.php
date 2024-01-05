<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\User;

class PaymentController extends Controller
{
    public function create_payment(Request $request)
    {

        $request->validate([
            'user_id'=>'required|exists:users,id',
            'payment_amount'=>'required|numeric',
            'due_date'=>'required|date',
        ]);

        $existuserid=Payment::
        //where('user_id',$request->user_id)
        where('due_date', $request->due_date)
        ->first();
        if($existuserid)
        {
            return response()->json(['Error'=>'This user Due date already present in Payment table for this month'],422);
        }

       $user= User::find($request->user_id);
       if(!$user)
       {
        return response()->json(['error'=>'User Not Found'],404);
       }

       $payment=Payment::create([
        'user_id'=>$request->user_id,
        'payment_amount'=>$request->payment_amount,
        'due_date'=>$request->due_date,
       ]);

       return response()->json(['Payment Due date alert send successfully'=>true,'payment'=>$payment]);

    }
}
