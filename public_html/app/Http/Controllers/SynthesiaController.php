<?php

namespace App\Http\Controllers;

use App\Enums\AIEngine;
use App\Models\AiModel;
use App\Models\UserSynthesia;
use App\Services\Synthesia\SynthesiaService;
use Illuminate\Http\Request;
use App\Helpers\Classes\Helper;

class SynthesiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service = new SynthesiaService();
        $list = $service->listVideos();

        $model = UserSynthesia::query()->where("user_id", auth()->user()->id)->get();

        $list = collect($list)->filter(function ($item) use ($model) {
            $avatarIds = $model->pluck('avatar_id')->toArray();
            return isset($item['id']) && in_array($item['id'], $avatarIds);
        })->toArray();

        $inProgress = collect($list)->filter(function ($entry) {
            return $entry['status'] === 'in_progress';
        })->pluck('id')->toArray();

        return view('panel.user.synthesia.index', compact('list', 'inProgress'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $service = new SynthesiaService();
        $avatars = $service->listAvatars();
        $backgrounds = $service->listBackgrounds();

        return view('panel.user.synthesia.create', compact(['avatars', 'backgrounds']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $request->validate([
            'avatar' => 'required',
            'title' => 'required',
            'description' => 'required',
            'style' => 'required',
            'scriptText' => 'required',
        ]);

        $avatarSettings = [
            'style' => $request->get('style'),
            'backgroundColor' => $request->get('backgroundColor'),
        ];

        if ($request->get('style') === "rectangular") {
            $avatarSettings['horizontalAlign'] = $request->get('horizontalAlign');
        }

        $body = [
            [
                'avatarSettings' => $avatarSettings,
                'scriptText' => $request->get('scriptText'),
                'avatar' => $request->get('avatar'),
                'background' => $request->get('background'),
            ],
        ];


        $aiModel = AiModel::query()->where('ai_engine', AIEngine::SYNTHESIA)->firstOrFail();

        if (auth()->user()->remaining_images < $aiModel->imageToken()->cost_per_token && auth()->user()->remaining_images != -1) {
            return redirect()->back()->with([
                'message' => __('You have no credits left. Please consider upgrading your plan.'),
                'type' => 'error',
            ]);
        }

        $service = new SynthesiaService();
        $response = $service->createVideo($body, $request->get('visibility'),
            $request->get('title'), $request->get('description'), $request->get('test'));

        if (!empty($response['status']) && $response["id"]) {

            UserSynthesia::query()->create([
                "user_id" => auth()->user()->id,
                "avatar_id" => $response["id"],
                'status' => $response['status']
            ]);

            userCreditDecreaseForImage(auth()->user(), 1, AIEngine::SYNTHESIA);

            return redirect()->route('dashboard.user.ai-avatar.index')->with([
                'message' => __('Video Created Successfully'),
                'type' => 'success',
            ]);
        } else {
            return redirect()->back()->with([
                'message' => $response["message"] ?? __('Synthesia API Key Error'),
                'type' => 'error',
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $service = new SynthesiaService();
        $model = $service->deleteVideo($id);

        if ($model->getStatusCode() === 200 || $model->getStatusCode() === 204) {

            $builder = UserSynthesia::query()->where("avatar_id", $id)->first();
            $builder->delete();

            return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
        } else {
            return back()->with(['message' => __('Delete Failed'), 'type' => 'danger']);
        }
    }

    public function checkVideoStatus(Request $request)
    {
        $ids = UserSynthesia::query()->where('status', 'in_progress')->pluck('avatar_id')->toArray();

        if (!count($ids)) {
            return response()->json(['data' => []]);

        }

        $service = new SynthesiaService();

        $list = $service->listVideos();

        $data = [];

        foreach ($list as $entry) {
            if (in_array($entry['id'], $ids) && $entry['status'] == 'complete') {
                $data[] = [
                    'divId' => 'video-' . $entry['id'],
                    'html' => view('panel.user.synthesia.video-item', ['entry' => $entry])->render(),
                ];

                UserSynthesia::query()->update([
                    'status' => 'complete'
                ]);
            }
        }

        return response()->json(['data' => $data]);
    }
}