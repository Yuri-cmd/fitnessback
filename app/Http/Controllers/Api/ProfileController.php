<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->profile ?? UserProfile::create(['user_id' => $request->user()->id]);
        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $request->validate([
            'height'         => 'nullable|numeric|min:50|max:250',
            'current_weight' => 'nullable|numeric|min:20|max:300',
            'goal_weight'    => 'nullable|numeric|min:20|max:300',
            'birth_date'     => 'nullable|date|before:today',
            'gender'         => 'nullable|in:male,female',
            'activity_level' => 'nullable|in:sedentary,lightly_active,moderately_active,very_active,extra_active',
        ]);

        $profile = $request->user()->profile;
        if (!$profile) {
            $profile = new UserProfile();
            $profile->user_id = $request->user()->id;
        }

        $profile->fill($request->only([
            'height', 'current_weight', 'goal_weight',
            'birth_date', 'gender', 'activity_level',
        ]));
        $profile->save();

        return response()->json($profile);
    }
}
