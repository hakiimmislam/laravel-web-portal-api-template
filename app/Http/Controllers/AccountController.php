<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

use Validator;
use Hash;
use Log;
use Auth;
use Image;
use Storage;

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

    public function update(Request $request)
    {
    	try {
	    	$validator = Validator::make($request->all(), [
	            'name' => 'required',
	            'username' => 'required',
	            'email' => 'required|email'
	        ]);

	        if ($validator->fails()) {
	            return response()->json(['result' => 'ERROR', 'msg' => 'The given data was invalid.']);
	        }

	        $this->user->update([
	        	'name' => $request->name,
	        	'username' => $request->username,
	        	'email' => $request->email
	        ]);

	        return response()->json(['result' => 'GOOD']);
    	}
    	catch (\Exception $e) {
            return response()->json(['result' => 'ERROR', 'msg' => $e->getMessage()]);
    	}
    }

    public function uploadImage(Request $request)
    {
        $filename = str_random(10) . '.jpg';

        $img = Image::make($request->file('image')->getRealPath());
        $img = $img->encode('jpg', 50);

        Storage::disk('public')->put($this->user->id . '/' . $filename, $img->getEncoded());
        $this->user->update(['image' => $filename]);
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
                }
                else {
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
