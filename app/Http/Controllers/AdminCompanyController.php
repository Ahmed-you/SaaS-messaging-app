<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCompanyController extends Controller
{
    public function updateStatus(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'trialing', 'suspended'])],
        ]);

        $company->update($validated);

        return redirect()
            ->route('messages.index', ['company_id' => $company->id])
            ->with('status', 'Super Admin updated the company status.');
    }

    public function updateModules(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'module_ids' => ['array'],
            'module_ids.*' => ['integer', 'exists:modules,id'],
        ]);

        $moduleIds = collect($validated['module_ids'] ?? [])
            ->intersect(Module::query()->pluck('id'))
            ->values()
            ->all();

        $company->modules()->syncWithPivotValues($moduleIds, ['enabled_at' => now()]);

        return redirect()
            ->route('messages.index', ['company_id' => $company->id])
            ->with('status', 'Super Admin updated the enabled modules.');
    }
}
