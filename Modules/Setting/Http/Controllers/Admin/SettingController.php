<?php

namespace Modules\Setting\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Modules\Media\Entities\File;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Artisan;
use Modules\Admin\Ui\Facades\TabManager;
use Modules\Setting\Entities\Setting;
use Modules\Support\Services\PWAService;
use Illuminate\Contracts\Foundation\Application;
use Modules\Setting\Http\Requests\UpdateSettingRequest;

class SettingController
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function edit()
    {
        $settings = Setting::with(['translations' => function ($query) {
            $query->withoutGlobalScopes();
        }])
            ->get()
            ->pluck('value', 'key')
            ->toArray();

        $tabs = TabManager::get('settings');

        return view('setting::admin.settings.edit', compact('settings', 'tabs'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSettingRequest $request
     *
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     */
    public function update(UpdateSettingRequest $request)
    {
        $this->handleMaintenanceMode($request);

        setting($request->except('_token', '_method'));

        return redirect(non_localized_url())
            ->with('success', trans('setting::messages.settings_updated'));
    }


    private function handleMaintenanceMode($request)
    {
        if ($request->maintenance_mode) {
            Artisan::call('down');
        } else if (app()->isDownForMaintenance()) {
            Artisan::call('up');
        }
    }
}
