<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Finance\PaymentProcessController;
use App\Models\PaymentPlans;
use App\Models\Setting;

class PlanController extends Controller
{
    public function index()
    {
        return view('panel.admin.finance.plan.index', [
            'gatewayError' => false,
            'setting'      => Setting::query()->first(),
            'plans'        => PaymentPlans::query()->get(),
        ]);
    }

    public function create()
    {
        return view('panel.admin.finance.plan.form', [
            'method'       => 'POST',
            'title'        => trans('Create Plan'),
            'item'         => new PaymentPlans,
            'subscription' => true,
        ]);
    }

    public function edit(PaymentPlans $plan)
    {

        return view('panel.admin.finance.plan.form', [
            'method'       => 'POST',
            'title'        => trans('Edit Plan'),
            'item'         => $plan,
            'subscription' => true,
        ]);
    }

    public function destroy(PaymentPlans $plan)
    {
        PaymentProcessController::deletePaymentPlan($plan->id);

        return redirect()->route('dashboard.admin.finance.plan.index');
    }
}
