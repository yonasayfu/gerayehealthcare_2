<?php

namespace App\DTOs;

class CreateEthiopianCalendarDayDTO
{
    public function __construct(
        public string $gregorian_date,
        public ?string $ethiopian_date,
        public ?string $description,
        public ?bool $is_holiday,
        public ?string $region
    ) {}
}
