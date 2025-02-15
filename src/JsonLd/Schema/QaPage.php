<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

final class QaPage extends AbstractType
{
    public function __construct()
    {
        parent::__construct('QAPage');
    }

    public function question(Question $question): self
    {
        return $this->set('mainEntity', $question);
    }
}
