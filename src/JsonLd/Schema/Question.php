<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

use DateTimeInterface;

final class Question extends AbstractType
{
    public function __construct()
    {
        parent::__construct('Question');
    }

    public function name(string $name): self
    {
        return $this->set('name', $name);
    }

    public function text(string $text): self
    {
        return $this->set('text', $text);
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

    /**
     * An explicit value wins over the automatic count of marked up answers.
     */
    public function answerCount(int $count): self
    {
        return $this->set('answerCount', $count);
    }

    public function acceptedAnswer(Answer $answer): self
    {
        return $this->set('acceptedAnswer', $answer);
    }

    public function suggestedAnswer(Answer $answer): self
    {
        return $this->push('suggestedAnswer', $answer);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        if (! array_key_exists('answerCount', $data)) {
            $suggested = $data['suggestedAnswer'] ?? [];

            $data['answerCount'] = (array_key_exists('acceptedAnswer', $data) ? 1 : 0)
                + (is_array($suggested) ? count($suggested) : 1);
        }

        return $data;
    }
}
