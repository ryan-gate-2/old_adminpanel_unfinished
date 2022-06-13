<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class PhoneLoginController extends Controller
{

        public function sendEmail(Request $request)
    {
        $name = [
            'name' => 'John',
        ];

        try {
            Mail::to('example@gmail.com')->send(new WelcomeEmail($name));
            return back()->with('msg','Email Send Successfully');
        } catch (\Exception $e) {
            echo 'Error - '.$e;
        }
    }


    public function index()
    {

        /*
        if(auth()->guest()) {
            return redirect('/login');
        }
        $user = auth()->user()->id;
        $selectUser = \App\Models\User::where('id', '=', $user)->first();

        if($selectUser->phonenumber_state === 0) {
            return redirect('/login');
        }
        */

        return view('verify-phone');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'phonenumber' => 'required|min:2|max:10',
        ]);

        if($validated) {

        if(auth()->guest()) {
            return redirect('/login');
        }

        $user = auth()->user()->id;
        $selectUser = \App\Models\User::where('id', '=', $user)->first();

        if($selectUser) {
            $phoneCode = $selectUser->phonenumber_code;

            if($selectUser->phonenumber_wrong_tries > 15) { 
                abort(403, 'You have tried too many invalid attempts.');                 
            }

            if($phoneCode === '0') {
                return redirect('/verify')->with('status', 'First press button to send verification code to your phone.');
            }

            $phoneCodeExplode = explode('-', $phoneCode);

            if($phoneCodeExplode[0] === $request->phonenumber) {

                $updateUser = \App\Models\User::where('id', '=', $user)->update([
                    'phonenumber_state' => 0,
                    'phonenumber_code' => '0',
                    'phonenumber_last_verification' => now(),
                    'phonenumber_wrong_tries' => 0,
                ]);

                return redirect('/')->with('status', 'Completed');
            } else {

                $updateUser = \App\Models\User::where('id', '=', $user)->update([
                    'phonenumber_wrong_tries' => $selectUser->phonenumber_wrong_tries + 1,
                ]);

                return redirect('/verify')->with('status', 'Invalid Try');
            }
            } else {

                abort(403, 'Can not verify your authentication, please clear cookies and re-login.');                 
            }
        }
    }

    public function sendcode(Request $request)
    {

        if(auth()->guest()) {
            return redirect('/login');
        }

        $user = auth()->user()->id;
        $selectUser = \App\Models\User::where('id', '=', $user)->first();

        if($selectUser) {
                $phonenumber = $selectUser->phonenumber;

                if($phonenumber !== '0') {

                if($selectUser->phonenumber_code !== '0') {
                    $explodeCode = explode('-', $selectUser->phonenumber_code);
                    $currentTime = time(); 
                    if($explodeCode[1] > ($currentTime - (60*3))) {
                       return redirect('/verify')->with('status', 'You have already sent verification code in last 180 seconds - please wait before sending request again.');
                    }

                }

                    $newphoneCode = rand('100000', '99999999');
                    $updateUser = \App\Models\User::where('id', '=', $user)->update([
                        'phonenumber_code' => $newphoneCode.'-'.time(),
                    ]);
                    /*
                  $MessageBird = new \MessageBird\Client('rpQsWY1DoTibPgMGImv6Ttod5');
                  $Message = new \MessageBird\Objects\Message();
                  $Message->originator = 'TollgateIO';
                  $Message->recipients = array('+'.$phonenumber);
                  $Message->body = 'Your TollgateIO verification code: '.$newphoneCode;

                  $MessageBird->messages->create($Message);
                  */





                Http::get('https://gateway.sms77.io/api/sms?p=p5K07YVHe29IZaqNOWz7empL8KR4SJJCcDOsRJN4Ton8x444C17LfB5b8kDuRARt&to=31645575196&text=Your%20Tollgate.io%20verification%20code%20:%20%20'.$newphoneCode.'&from=TollgateIO&return_msg_id=1');

                return redirect('/verify')->with('status', 'Verification code sent.');

                }


    }

}

}