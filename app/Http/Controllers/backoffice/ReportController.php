<?php

namespace App\Http\Controllers\backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Database\Eloquent\Builder;

class ReportController extends Controller
{
    public function loginHistory(Request $request)
    {

        $pageTitle = 'User Login History';
        $loginLogs = UserLogin::orderBy('id', 'desc')->with('user')->when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('user_name', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login by IP - ' . $ip;
        $loginLogs = UserLogin::where('user_ip', $ip)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));
    }
}
