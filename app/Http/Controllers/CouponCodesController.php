<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use Carbon\Carbon;

class CouponCodesController extends Controller
{
    public function show($code)
    {
        // check if coupon code exists
        if (!$record = CouponCode::where('code', $code)->first()) {
            abort(404);
        }

        // if code is not enabled
        if (!$record->enabled) {
            abort(404);
        }

        // if code is used out
        if ($record->total - $record->used <= 0) {
            return response()->json(['msg' => 'This code is used out'], 403);
        }

        if ($record->not_before && $record->not_before->gt(Carbon::now())) {
            return response()->json(['msg' => 'This code is not available yet'], 403);
        }

        if ($record->not_after && $record->not_after->lt(Carbon::now())) {
            return response()->json(['msg' => 'This code has expired'], 403);
        }

        return $record;
    }
}