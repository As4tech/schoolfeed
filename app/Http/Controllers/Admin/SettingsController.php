<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $schoolId = SettingsHelper::getCurrentSchoolId();
        
        if (!$schoolId) {
            abort(403, 'Unable to determine school context');
        }
        
        // Get settings by category
        $generalSettings = SettingsHelper::getSettingsByCategory($schoolId, 'general');
        $paymentSettings = SettingsHelper::getSettingsByCategory($schoolId, 'payment');
        $feedingSettings = SettingsHelper::getSettingsByCategory($schoolId, 'feeding');
        $notificationSettings = SettingsHelper::getSettingsByCategory($schoolId, 'notification');
        
        return view('admin.settings.index', compact(
            'generalSettings',
            'paymentSettings',
            'feedingSettings',
            'notificationSettings'
        ));
    }
    
    public function update(Request $request)
    {
        // Log the request for debugging
        \Log::info('Settings update attempt', [
            'request_data' => $request->all(),
            'school_id' => SettingsHelper::getCurrentSchoolId()
        ]);
        
        $schoolId = SettingsHelper::getCurrentSchoolId();
        
        if (!$schoolId) {
            \Log::error('No school ID found in settings update');
            abort(403, 'Unable to determine school context');
        }
        
        try {
            $validated = $request->validate([
                'general' => 'sometimes|array',
                'general.school_name' => 'sometimes|string|max:255',
                'general.contact_email' => 'sometimes|email|max:255',
                'general.phone' => 'sometimes|string|max:20',
                'general.address' => 'sometimes|string|max:500',
                'general.logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                
                'payment' => 'sometimes|array',
                'payment.paystack_subaccount_code' => 'sometimes|string|nullable|max:100',
                'payment.platform_fee_percentage' => 'sometimes|numeric|min:0|max:100',
                'payment.payment_methods' => 'sometimes|array',
                'payment.payment_methods.*' => 'string|in:momo,card,bank',
                
                'feeding' => 'sometimes|array',
                'feeding.default_feeding_fee' => 'sometimes|integer|min:0',
                'feeding.feeding_type' => ['sometimes', Rule::in(['daily', 'weekly', 'termly'])],
                'feeding.allow_unpaid_feeding' => 'sometimes|boolean',
                
                'notification' => 'sometimes|array',
                'notification.enable_sms' => 'sometimes|boolean',
                'notification.enable_email' => 'sometimes|boolean',
                'notification.reminder_frequency' => ['sometimes', Rule::in(['daily', 'weekly', 'monthly'])],
            ]);
            
            \Log::info('Validation passed', ['validated' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }
        
        // Handle logo upload
        if ($request->hasFile('general.logo')) {
            $logo = $request->file('general.logo');
            $path = $logo->store('school-logos', 'public');
            SettingsHelper::setSetting($schoolId, 'logo', $path);
        }
        
        // Get the submitted tab
        $tab = $request->input('tab', 'general');
        
        // Update only the settings from the submitted tab
        switch ($tab) {
            case 'general':
                if (isset($validated['general'])) {
                    foreach ($validated['general'] as $key => $value) {
                        if ($key !== 'logo') {
                            SettingsHelper::setSetting($schoolId, $key, $value);
                        }
                    }
                }
                break;
                
            case 'payment':
                if (isset($validated['payment'])) {
                    foreach ($validated['payment'] as $key => $value) {
                        SettingsHelper::setSetting($schoolId, $key, $value);
                    }
                }
                break;
                
            case 'feeding':
                if (isset($validated['feeding'])) {
                    foreach ($validated['feeding'] as $key => $value) {
                        // Convert string "0"/"1" to boolean for boolean fields
                        if ($key === 'allow_unpaid_feeding') {
                            $value = (bool) $value;
                        }
                        SettingsHelper::setSetting($schoolId, $key, $value);
                    }
                }
                break;
                
            case 'notification':
                if (isset($validated['notification'])) {
                    foreach ($validated['notification'] as $key => $value) {
                        // Convert string "0"/"1" to boolean for boolean fields
                        if (in_array($key, ['enable_sms', 'enable_email'])) {
                            $value = (bool) $value;
                        }
                        SettingsHelper::setSetting($schoolId, $key, $value);
                    }
                }
                break;
        }
        
        return back()->with('success', 'Settings updated successfully!');
    }
    
    /**
     * Remove school logo
     */
    public function removeLogo()
    {
        $schoolId = SettingsHelper::getCurrentSchoolId();
        
        if (!$schoolId) {
            abort(403, 'Unable to determine school context');
        }
        
        $logoPath = SettingsHelper::getSetting($schoolId, 'logo');
        
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            Storage::disk('public')->delete($logoPath);
        }
        
        SettingsHelper::setSetting($schoolId, 'logo', null);
        
        return back()->with('success', 'Logo removed successfully!');
    }
}
