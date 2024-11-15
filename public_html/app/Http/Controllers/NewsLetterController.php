<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Models\EmailTemplates;
use Illuminate\Http\Request;

class NewsLetterController extends Controller
{
    public function new()
    {
        return view('panel.email.form', [
            'action' => route('dashboard.newsletter.store'),
            'method' => 'POST',
            'template' => new EmailTemplates(),
            'title' => 'Create Email Template'
        ]);
    }

    public function store(Request $request)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.')
            ]);
        }

        $request['content'] = $request->get('content_ace');

        $data = $request->validate([
            'title' => 'required',
            'subject' => 'required',
            'content' => 'required'
        ]);

        $data['system'] = false;

        EmailTemplates::create($data);

        return redirect()
            ->route('dashboard.email-templates.index')
            ->with([
                'message' => 'Created Successfully', 'type' => 'success'
            ]);
    }
}
