<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateNotificationSettingsRequest;
use App\Http\Resources\NotificationSettingResource;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;

class NotificationSettingsController extends Controller
{
    public function show(Request $request)
    {
        $settings = $this->getSettingsModel($request);

        return new NotificationSettingResource($settings);
    }

    public function update(UpdateNotificationSettingsRequest $request)
    {
        $settings = $this->getSettingsModel($request);

        $data = $request->validated();
        if (array_key_exists('enable_notifications', $data)) {
            $enable = (bool) $data['enable_notifications'];
            $data['enable_push'] = $data['enable_push'] ?? $enable;
            $data['enable_email'] = $data['enable_email'] ?? $enable;
            unset($data['enable_notifications']);
        }

        $settings->fill($data);
        $settings->save();

        return new NotificationSettingResource($settings);
    }

    protected function getSettingsModel(Request $request): NotificationSetting
    {
        return $request->user()->notificationSettings()->firstOrCreate([
            'user_id' => $request->user()->id,
        ]);
    }
}
