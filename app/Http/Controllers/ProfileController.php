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
    $user_id = Auth::id(); // shorter syntax

    $validatedData = $request->validated();
    $validatedData['user_id'] = $user_id;

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('my_photo', 'public');
        $validatedData['image'] = $path; // ✅ add image before insert
    } else {
        return response()->json(['error' => 'Image is required'], 422);
    }

    $profile = Profile::create($validatedData);

    return response()->json([
        'message' => 'Profile Created Successfully',
        'profile' => $profile
    ], 201);
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
