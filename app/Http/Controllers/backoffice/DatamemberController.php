<?php

namespace App\Http\Controllers\backoffice;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bank;
use App\Models\Saldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;


class DatamemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $pageTitle = 'Member List';
        $data_member = User::where('level',null)->when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());
        return view('admin.member.list',compact(['data_member','pageTitle'] ));
    }

    public function admins(Request $request)
    {
        $pageTitle = 'Admins List';
        $data_member = User::where('level', 1)->when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());
        return view('admin.manage.list',compact(['data_member','pageTitle'] ));
    }

    public function createacc()
    {
        $pageTitle = 'Create Admin / Master';
        $data_bank = Bank::get();
        return view('admin.manage.create',compact(['pageTitle','data_bank'] ));
    }

    public function masters(Request $request)
    {
        $pageTitle = 'Master List';
        $data_member = User::where('level', 2)->when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());
        return view('admin.manage.master',compact(['data_member','pageTitle'] ));
    }

    public function details(string $id)
    {
        

        $data_member = User::where('level',null)->find($id);
        $data_bank = Bank::get();
        $pageTitle = 'Member Details';
        return view('admin.member.detail',compact(['data_member', 'data_bank','pageTitle'], ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $admins = new User();
        $admins->name = $request->nama;
        $admins->telp = $request->telp;
        $admins->email = $request->email;
        $admins->password = Hash::make($request->password);
        $admins->level = $request->level;
        $admins->save();
        return redirect()->back()->with('success', 'Admins Create Successfuly');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //check
        $check = User::where('name', $request->name)->get();
        if(count($check) > 0){
            return redirect()->back()->with('error', 'Member data already exists');
        }

        if(!empty($request->id)){
            $check = User::where('email', $request->email)->where('id', '!=', $request->id)->get();

            $data_user = User::where('id', $request->id)->first();
        }else{    
            $check = User::where('email', $request->email)->get();
            
            $data_user = new User();
            $data_user->name = $request->nama;
        }
        
        if(count($check) > 0){
            return redirect()->back()->with('error', 'Email already exists');
        }
        
        $data_user->email = $request->email;
        $data_user->level = null;
        $data_user->ref_code = $request->ref_code;
        $data_user->bank = $request->bank;
        $data_user->no_rek = $request->no_rek;
        $data_user->game_mode = isset($request->game_mode) ? $request->game_mode : 0;

        if(!empty($request->id)){
            if(!empty($request->password)){
                $data_user->password = Hash::make($request->password);
            }
        }else{
            $data_user->password = Hash::make($request->password);
        }

        $data_user->save();

        $msg = "Member updated successfully";
        if(empty($request->id)){
            $saldo = new Saldo();
            $saldo->user_id = $data_user->id;
            $saldo->saldo = 0;
            $saldo->bonus = 0;
    
            $saldo->save();
    
            $msg = "Member updated successfully";
        }
        return redirect()->back()->with('success', $msg );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('backoffice.admin.list')->with('success', 'User Remove Successfuly');
    }
}
