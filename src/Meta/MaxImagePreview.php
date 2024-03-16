<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\Meta;

enum MaxImagePreview: string
{
    case None = 'none';
    case Standard = 'standard';
    case Large = 'large';
}
