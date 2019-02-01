<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

use Validator;
use Hash;
use Log;
use Auth;

class AccountController extends Controller
{
	protected $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }
    
    public function get() {
    	try {
    		$user = $this->user;

    		return response()->json(['result' => 'GOOD', 'data' => $user]);
    	}
    	catch (\Exception $e) {
    		Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

    public function resetPassword(Request $request)
    {
    	try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'password' => 'required|confirmed'
            ]);

            if ($validator->fails()) {
                if ($validator->messages()->get('password') == ['The password confirmation does not match.']) {
                    return response()->json(['result' => 'PASSWORDCONFIRMATION']);
                }else{
                    return response()->json(['result' => 'INCOMPLETEPARAMS']);
                }
            }

            $user = User::findOrFail($this->user->id);
            
            if (Hash::check($request->current_password, $user->password)) {
            	$user->update(['password' => Hash::make($request->password)]);

	    		return response()->json(['result' => 'GOOD']);
            }

            return response()->json(['result' => 'INCORRECTPASSWORD']);
    	}
    	catch (\Exception $e) {
	    	Log::error($e->getMessage());
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }
}
