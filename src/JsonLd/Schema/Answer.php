<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

use DateTimeInterface;

final class Answer extends AbstractType
{
    public function __construct()
    {
        parent::__construct('Answer');
    }

    public function text(string $text): self
    {
        return $this->set('text', $text);
    }

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }

    /**
     * A plain string is wrapped into a Person: Google reads the Q&A author
     * as an object with a name, not as text.
     */
    public function author(string|Person|Organization $author): self
    {
        if (is_string($author)) {
            $author = (new Person())->name($author);
        }

        return $this->set('author', $author);
    }

    public function dateCreated(DateTimeInterface|string $date): self
    {
        return $this->set('dateCreated', $date);
    }

    public function upvoteCount(int $count): self
    {
        return $this->set('upvoteCount', $count);
    }
}
