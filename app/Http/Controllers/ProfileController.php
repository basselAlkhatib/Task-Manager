<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Contracts\Support\ValidatedData;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->profile;
        $profile = Profile::all();
        return response()->json($profile, 200);
    }

    public function show($id)
    {
        $user_id = Auth::user()->id;
        $profile = Profile::where('user_id', $id)->firstorfail();
        if ($profile->user_id != $user_id)
            return response()->json(['message' => 'UnAuthraized', 403]);
        return response()->json($profile, 200);
    }
    public function store(StoreProfileRequest $request)
    {
        $user_id = Auth::user()->id;
        $ValidatedData = $request->validated();
        $ValidatedData['user_id'] = $user_id;
        $profile = Profile::create($ValidatedData);

        return response()->json(
            [
                'message' => 'Profile Created Successfuly',
                'profile' => $profile
            ],
            201
        );
    }
    public function update(UpdateProfileRequest $request, $id)
    {
        $user_id = Auth::user()->id;
        $profile = Profile::findOrFail($id);
        if ($profile->user_id != $user_id)
            return response()->json(['message' => 'UnAuthraized', 403]);
        $profile->update($request->validated());
        return response()->json($profile, 200);
    }
}
