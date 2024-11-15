<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Classes\Helper;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenance = cache()->get('maintenance');

        return view('panel.admin.common.maintenance', [
            'maintenance' => $maintenance,
        ]);
    }

    public function update(Request $request)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $request->validate([
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $request->merge([
            'maintenance_mode' => $request->has('maintenance_mode'),
        ]);

        if ($image = $request->file('image')) {
            $data = $request->all();
            $data['image'] = $image->store('maintenance', 'public');
        } else {
            $maintenance = cache()->get('maintenance');

            $data = $request->except('image');

            if (isset($maintenance['image'])) {
                $data['image'] = $maintenance['image'];
            }
        }

        cache()->put('maintenance', $data);

        return back()->with([
            'type' => 'success',
            'message' => 'Maintenance settings updated successfully.',
        ]);
    }
}
