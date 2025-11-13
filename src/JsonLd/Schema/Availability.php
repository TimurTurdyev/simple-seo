<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

enum Availability: string
{
    case BackOrder = 'https://schema.org/BackOrder';
    case Discontinued = 'https://schema.org/Discontinued';
    case InStock = 'https://schema.org/InStock';
    case InStoreOnly = 'https://schema.org/InStoreOnly';
    case LimitedAvailability = 'https://schema.org/LimitedAvailability';
    case MadeToOrder = 'https://schema.org/MadeToOrder';
    case OnlineOnly = 'https://schema.org/OnlineOnly';
    case OutOfStock = 'https://schema.org/OutOfStock';
    case PreOrder = 'https://schema.org/PreOrder';
    case PreSale = 'https://schema.org/PreSale';
    case Reserved = 'https://schema.org/Reserved';
    case SoldOut = 'https://schema.org/SoldOut';
}
