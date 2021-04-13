<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Mail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EmailController extends BaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:20',
            'password' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        if($user = User::where('username',$request->username)->first()) {
            
        if($user->user_role == 1 || ($user->user_role == 2 && $user->email_verified_at )) {
            if(Auth::attempt(['username' => $request->username, 'password' => $request->password])){
        $user = Auth::user();
                $success['token'] =  $user->createToken('MyApp')-> accessToken; 
                $success['name'] =  $user->name;

                return $this->sendResponse($success, 'Login successfully.');
            } 
            else{
                return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            } 
        }
        return $this->sendError('Unauthorised.', ['error'=>'Please register first.']);
        }
        return $this->sendError('Unauthorised.', ['error'=>'No records found with this username.']);
        
    }
    
    public function invitation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $to_email = $request->email;
        if (Invitation::where('email', $to_email)->where('status', 1)->exists()) {
            return $this->sendResponse('error', 'User aready logined with this email.');
        } else {
            $code = encrypt(rand(10000, 100000));
            $invitation = new Invitation();
            $invitation->code = $code;
            $invitation->email = $to_email;
            if ($invitation->save()) {

                $data = array("link" => "http://65.1.52.251/dev/blog/public/api/registration/$code");
                Mail::send('email', $data, function ($message) use ($to_email) {
                            $message->to($to_email)->subject('Assessment');
                        });

                return $this->sendResponse('success', 'Invitation sent successfully.');
            }
        }
    }

    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username|min:4|max:20',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'code' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $invitation = Invitation::where('code',$request->code)->where('status',0)->latest()->first();
        
        if(User::where('email',$invitation->email)->exists()){
            return $this->sendResponse('Error', 'The Email has already been taken.');
        }

        if($invitation){
            
            $input = $request->all();
            $input['username'] = $input['username'];
            $input['email'] = $invitation->email;
            $input['user_role'] = 2;
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            
            $success['email'] =  $user->email;
            $code = rand(100000,999999);
            
            $to_email = $invitation->email;
            $data = array("link" => $code);
            $a = Mail::send('pin-email', $data, function($message) use ($to_email) {
                $message->to($to_email)->subject('Pin');
            });
            
            Invitation::whereId($invitation->id)->update(['status'=>1,'pin'=>$code]);

            return $this->sendResponse($success, 'Pin sent successfully.');
        } 
        return $this->sendResponse('Error', 'Invitation link is already used.');
    }
    public function enterPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required',
            'email' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        if(Invitation::where('email',$request->email)->where('status',1)->where('pin',$request->pin)->exists()){
            $user = User::where('email',$request->email)->first();
            $user->email_verified_at = \Carbon\Carbon::now()->toDateTimeString();
            $user->save();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['username'] =  $user->username;
            return $this->sendResponse($success, 'Registration done successfully.');
        }
        
        return $this->sendResponse('Error', 'Please enter correct pin.');
    }
    public function getCode($code)
    {
        dd($code);
    }
    public function profile()
    {
        $user = Auth::user();
        $data['name'] = $user->name;
        $data['username'] = $user->username;
        $data['avatar'] = $user->avatar;
        $data['email'] = $user->email;
        $data['role'] = $user->user_role == 1 ? "Admin":"User";
        $data['registered_at'] = $user->created_at;
        $data['created_at'] = $user->created_at;
        $data['updated_at'] = $user->updated_at;
         return $this->sendResponse('details',$data);
    }
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"  => "required",
            "avatar"  => "sometimes|image|mimes:jpeg,png,jpg|dimensions:max_width=256,max_height=256",
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $curruntDateTime = \Carbon\Carbon::now()->toDateTimeString();
        $user = User::where('id',Auth::id())->first();
        if($request->file('avatar')){
            $originalName = $request->file('avatar')->getClientOriginalName();
            $imageName = $curruntDateTime.'-'.$originalName;
            $request->file('avatar')->store( storage_path('/uploads/' . $imageName ) );
            
            $user->avatar = $imageName;
        }
        
        $user->name = $request->name;
        $user->save();
        
        return $this->sendResponse('success', 'Profile updated successfully.');
    }
    
}