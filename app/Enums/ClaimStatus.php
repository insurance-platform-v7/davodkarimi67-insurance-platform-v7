<?php

namespace App\Enums;

enum ClaimStatus: string
{
    case SUBMITTED = 'submitted';

    case UNDER_REVIEW = 'under_review';

    case APPROVED = 'approved';

    case REJECTED = 'rejected';

    case PAID = 'paid';
}
