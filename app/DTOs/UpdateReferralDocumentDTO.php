<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class UpdateReferralDocumentDTO
{
    public function __construct(
        public readonly ?int $referral_id,
        public readonly ?string $document_name,
        public readonly ?UploadedFile $document_file,
        public readonly ?string $document_type,
        public readonly ?string $status
    ) {}
}
