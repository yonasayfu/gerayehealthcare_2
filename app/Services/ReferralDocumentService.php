<?php

namespace App\Services;

use App\DTOs\CreateReferralDocumentDTO;
use App\DTOs\UpdateReferralDocumentDTO;
use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\ReferralDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReferralDocumentService extends BaseService
{
    use ExportableTrait;

    public function __construct(ReferralDocument $referralDocument)
    {
        parent::__construct($referralDocument);
    }

    public function createReferralDocument(CreateReferralDocumentDTO $dto): ReferralDocument
    {
        $uploaded = $dto->document_file;
        $path = $uploaded->store('referral_documents', 'public');
        $originalName = $uploaded->getClientOriginalName();
        $mimeType = $uploaded->getMimeType();
        $fileSize = $uploaded->getSize();
        $checksum = hash_file('sha256', $uploaded->getRealPath());

        return ReferralDocument::create([
            'referral_id' => $dto->referral_id,
            'uploaded_by_staff_id' => $dto->uploaded_by_staff_id,
            'document_name' => $dto->document_name,
            'document_path' => $path,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'checksum' => $checksum,
            'document_type' => $dto->document_type,
            'status' => $dto->status,
        ]);
    }

    public function updateReferralDocument(ReferralDocument $referralDocument, UpdateReferralDocumentDTO $dto): ReferralDocument
    {
        $data = [];
        if (! is_null($dto->referral_id)) {
            $data['referral_id'] = $dto->referral_id;
        }
        if (! is_null($dto->document_name)) {
            $data['document_name'] = $dto->document_name;
        }
        if (! is_null($dto->document_file)) {
            // Delete old file
            if ($referralDocument->document_path) {
                Storage::disk('public')->delete($referralDocument->document_path);
            }
            $uploaded = $dto->document_file;
            $data['document_path'] = $uploaded->store('referral_documents', 'public');
            $data['original_name'] = $uploaded->getClientOriginalName();
            $data['mime_type'] = $uploaded->getMimeType();
            $data['file_size'] = $uploaded->getSize();
            $data['checksum'] = hash_file('sha256', $uploaded->getRealPath());
        }
        if (! is_null($dto->document_type)) {
            $data['document_type'] = $dto->document_type;
        }
        if (! is_null($dto->status)) {
            $data['status'] = $dto->status;
        }

        $referralDocument->update($data);

        return $referralDocument;
    }

    public function deleteReferralDocument(ReferralDocument $referralDocument): void
    {
        if ($referralDocument->document_path) {
            Storage::disk('public')->delete($referralDocument->document_path);
        }
        $referralDocument->delete();
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, ReferralDocument::class, ExportConfig::getReferralDocumentConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, ReferralDocument::class, ExportConfig::getReferralDocumentConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, ReferralDocument::class, ExportConfig::getReferralDocumentConfig());
    }

    public function printSingle(ReferralDocument $referralDocument, Request $request)
    {
        return $this->handlePrintSingle($request, $referralDocument, ExportConfig::getReferralDocumentConfig());
    }

    protected function applySearch($query, $search)
    {
        $query->where('document_name', 'ilike', "%{$search}%")
            ->orWhere('document_type', 'ilike', "%{$search}%");
    }
}
