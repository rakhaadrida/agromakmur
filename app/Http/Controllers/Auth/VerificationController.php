<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserVerifyRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Utilities\Constant;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verifyEmail(UserVerifyRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::query()
                ->where('verifyCode', $request->get('token'))
                ->firstOrFail();

            $status = Constant::USER_STATUS_ACTIVE;
            if($user->role !== Constant::USER_TYPE_USER) {
                $status = Constant::USER_STATUS_PENDING;
            }

            $user->emailVerifiedAt = Carbon::now()->toDateTimeString();
            $user->status = $status;
            $user->verifyCode = null;
            $user->save();

            DB::commit();

            $message = $user->role === Constant::USER_TYPE_USER
                ? 'Email verification successful! Please log in.'
                : 'Email verification successful! Your account is pending approval by our team.';

            return redirect()->route('login')->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('login')->withErrors([
                'message' => 'Verification failed! Please contact our support'
            ]);
        }
    }
}
