<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // index
    public function index(Request $request)
    {
        //get users with pagination
        $users = DB::table('users')
        ->when($request->input('search'), function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere ('email', 'like', '%' . $search . '%')
                      ->orWhere('phone', 'like', '%' . $search . '%')
                      ->orWhere('roles', 'like', '%' . $search . '%');
            });
        })
        ->paginate(10);

        return view('pages.user.index', compact('users'));
    }


    // create
    public function create()
    {
        return view('pages.user.create');
    }

    // store
    public function store(Request $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($request->input('password'));
        User::create($data);
        return redirect()->route('user.index')->with('success', 'User added successfully');
    }

    // show
    public function show($id)
    {
        return view('pages.dashboard');
    }

    // edit
    public function edit($id)
    {
       $user = User::findOrFail($id);
       return view('pages.user.edit', compact('user'));
    }

    // update
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $user = User::findOrFail($id);
        // check if password is not empty
        if ($request -> input('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }else {
            // if password is empty, then use the old password
            $data['password'] = $user->password;
        }
        $user->update($data);
        return redirect()->route('user.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('user.index')->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('user.index')->with('error', 'Failed to delete the user');
        }
    }
}
