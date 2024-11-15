<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\Finance\AiChatModelPlan;
use App\Models\PaymentPlans;
use Illuminate\Http\Request;

class AiChatbotModelController extends Controller
{
    public function index()
    {
        $aiModels = AiModel::with('tokens')
            ->with('aiFinance')
            ->whereHas('tokens', function ($query) {
                $query->where('type', 'word');
            })
            ->whereIn('ai_engine', ['openai', 'anthropic', 'gemini'])
            ->whereNotIn('key', ['tts-1', 'tts-1-hd', 'whisper-1'])
            ->where('is_active', true)
            ->get();

        $groupedAiModels = $aiModels->groupBy('ai_engine');

        $plans = PaymentPlans::query()
            ->where('type', 'subscription')
            ->get();

        return view('panel.admin.chatbot.ai-models', compact('groupedAiModels', 'plans'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'selected_title.*' => 'required',
            'selected_plans.*' => 'sometimes',
            'no_plan_users.*'  => 'sometimes',
        ]);

        foreach ($data['selected_title'] as $key => $value) {
            AiModel::query()
                ->where('id', $key)
                ->update([
                    'selected_title' => $value,
                ]);
        }

        $selected_plans = $request->input('selected_plans');

        if ($selected_plans) {
            foreach ($selected_plans as $id => $value) {
                AiChatModelPlan::query()
                    ->where('ai_model_id', $id)
                    ->delete();
                foreach ($value as $item) {
                    AiChatModelPlan::query()
                        ->create([
                            'plan_id'     => $item,
                            'ai_model_id' => $id,
                        ]);
                }
            }
        }

        AiModel::query()->update([
            'is_selected' => false,
        ]);

        $no_plan_users = $request->input('no_plan_users');

        if ($no_plan_users) {
            foreach ($no_plan_users as $key => $value) {
                AiModel::query()
                    ->where('id', $key)
                    ->update([
                        'is_selected' => true,
                    ]);
            }
        }

        return redirect()->back()->with([
            'message' => 'AI Models updated successfully',
            'type'    => 'success',
        ]);
    }
}
