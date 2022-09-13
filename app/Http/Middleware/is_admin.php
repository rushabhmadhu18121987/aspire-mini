<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class is_admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   
        //Request Validates for Admin only to Approve Loan + View All Loans
        if ($request->user()->isAdmin()) {
            return $next($request);
        } else {
            return response()->json([
                "status"    => false,
                "message"   => "You are not allowed to access this routes",
                "error"     => "Admin can only access this request"
            ], 403);
        }
    }
}
