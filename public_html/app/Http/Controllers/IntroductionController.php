<?php

namespace App\Http\Controllers;

use App\Models\Introduction;
use Illuminate\Http\Request;

class IntroductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $list = Introduction::all();

        return view('panel.admin.introduction.index', compact('list'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->except('_token', '_method');

        foreach ($data as $key => $value) {
            Introduction::query()->where('key', $key)->update(['intro' => $value]);
        }

        return response()->json([
            'message' => 'User Onboarding settings updated successfully',
        ]);
    }
}
