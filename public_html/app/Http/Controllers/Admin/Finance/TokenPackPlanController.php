<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlans;

class TokenPackPlanController extends Controller
{
    public function create()
    {
        return view('panel.admin.finance.plan.form', [
            'method'       => 'POST',
            'title'        => trans('Create Token Pack Plan'),
            'item'         => new PaymentPlans,
            'subscription' => false,
        ]);
    }

    public function edit(PaymentPlans $tokenPackPlan)
    {
        return view('panel.admin.finance.plan.form', [
            'method'       => 'POST',
            'title'        => trans('Edit Token Pack Plan'),
            'item'         => $tokenPackPlan,
            'subscription' => false,
        ]);
    }
}
