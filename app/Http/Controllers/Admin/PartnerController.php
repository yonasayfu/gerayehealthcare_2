<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreatePartnerDTO;
use App\DTOs\UpdatePartnerDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\Partner;
use App\Models\Staff;
use App\Services\Partner\PartnerService;
use App\Services\Validation\Rules\PartnerRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerController extends BaseController
{
    public function __construct(PartnerService $partnerService)
    {
        parent::__construct(
            $partnerService,
            PartnerRules::class,
            'Admin/Partners',
            'partners',
            Partner::class,
            CreatePartnerDTO::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/Partners/Create', [
            'staff' => Staff::all()->map(function ($staff) {
                return [
                    'id' => $staff->id,
                    'name' => $staff->first_name.' '.$staff->last_name,
                ];
            }),
        ]);
    }

    public function edit($id)
    {
        $partner = $this->service->getById($id);

        return Inertia::render('Admin/Partners/Edit', [
            'partner' => $partner,
            'staff' => Staff::all()->map(function ($staff) {
                return [
                    'id' => $staff->id,
                    'name' => $staff->first_name.' '.$staff->last_name,
                ];
            }),
        ]);
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }

    public function printAll(Request $request)
    {
        return $this->service->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->service->printCurrent($request);
    }

    public function printSingle(Request $request, $id)
    {
        return $this->service->printSingle($request, $id);
    }

    public function update(Request $request, $id)
    {
        // Validate using same rules class
        $model = $this->service->getById($id);
        $validatedData = $this->validateRequest($request, 'update', $model);

        // Build Update DTO explicitly
        $dto = UpdatePartnerDTO::from($validatedData);
        $payload = $dto->toArray();

        // Do not overwrite existing DB values with nulls
        $payload = array_filter($payload, function ($value) {
            return !is_null($value);
        });

        $this->service->update($id, $payload);

        return redirect()->route('admin.partners.index')->with('success', 'Partners updated successfully.');
    }
}
