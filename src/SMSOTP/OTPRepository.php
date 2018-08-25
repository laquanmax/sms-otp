<?php

namespace SMSOTP;

use SMSOTP\Contract\Repository;
use SMSOTP\Models\UserOTP;
use SMSOTP\OTP;

use Carbon\Carbon;

class OTPRepository implements Repository
{
    public function save_otp($number, $code, $token, $action = OTP::ACTION_DEFAULT)
    {
        $expired_at = Carbon::now()->addSeconds(config('smsotp.ttl'));

        $otp = UserOTP::updateOrCreate(
            ['number' => $number, 'action' => $action],
            ['code' => $code, 'expired_at' => $expired_at, 'token' => $token]
        );

        $otp->save();
    }

    public function otp_details($number, $code, $action = OTP::ACTION_DEFAULT)
    {
        $details = UserOTP::whereNumber($number)
            ->whereCode($code)
            ->whereAction($action)
            ->orderBy('created_at', 'desc')
            ->first();

        $otp = new OTP;

        if ($details)
        {
            $otp->setNumber($details->number);
            $otp->setCode($details->code);
            $otp->setToken($details->token);
            $otp->setIsVerified($details->is_verified);
            $otp->setExpiredAt($details->expired_at);
        }

        return $otp;
    }

    public function mark_otp_verified($code)
    {
        return (bool) UserOTP::where('code', '=', $code)
            ->update(['is_verified' => true]);
    }
}
