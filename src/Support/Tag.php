<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\Support;

/**
 * One head tag as data: the element name, its attributes in output order
 * and, for title and script, the text content.
 *
 * Values are kept raw. html() escapes them for direct output; toArray()
 * hands them to a client renderer (React, Vue) that escapes on its own.
 * Both outputs come from the same tag, so the markup and the data can
 * never disagree.
 */
final readonly class Tag
{
    /**
     * @param  array<string, string>  $attrs
     */
    public function __construct(
        public string $name,
        public array $attrs = [],
        public ?string $text = null,
    ) {
    }

    public function html(): string
    {
        $attrs = '';

        foreach ($this->attrs as $key => $value) {
            $attrs .= ' ' . $key . '="' . Escaper::html($value) . '"';
        }

        return match ($this->name) {
            'title' => '<title>' . Escaper::html((string) $this->text) . '</title>',
            // JSON-LD is already safe for a script element (JSON_HEX_TAG).
            'script' => '<script' . $attrs . '>' . $this->text . '</script>',
            default => '<' . $this->name . $attrs . '>',
        };
    }

    /**
     * @return array{tag: string, attrs: array<string, string>, text?: string}
     */
    public function toArray(): array
    {
        $tag = ['tag' => $this->name, 'attrs' => $this->attrs];

        if ($this->text !== null) {
            $tag['text'] = $this->text;
        }

        return $tag;
    }

    /**
     * @param  list<self>  $tags
     */
    public static function join(array $tags): string
    {
        return implode("\n", array_map(static fn (self $tag): string => $tag->html(), $tags));
    }
}
