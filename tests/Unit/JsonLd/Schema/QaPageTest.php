<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;
use TimurTurdyev\SimpleSeo\JsonLd\Schema\Schema;

it('builds a full qa page with accepted and suggested answers', function (): void {
    $page = Schema::qaPage()->question(
        Schema::question()
            ->name('How to choose an office chair?')
            ->text('Full question text')
            ->upvoteCount(5)
            ->acceptedAnswer(
                Schema::answer()
                    ->text('Check the tilt mechanism.')
                    ->url('https://example.com/q/1#answer-1')
                    ->upvoteCount(12)
            )
            ->suggestedAnswer(Schema::answer()->text('Look at the upholstery.'))
    );

    expect(json_encode($page))->toBe(json_encode([
        '@type' => 'QAPage',
        'mainEntity' => [
            '@type' => 'Question',
            'name' => 'How to choose an office chair?',
            'text' => 'Full question text',
            'upvoteCount' => 5,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Check the tilt mechanism.',
                'url' => 'https://example.com/q/1#answer-1',
                'upvoteCount' => 12,
            ],
            'suggestedAnswer' => [
                ['@type' => 'Answer', 'text' => 'Look at the upholstery.'],
            ],
            'answerCount' => 2,
        ],
    ]))->and(json_encode($page->jsonSerialize()['mainEntity']))->toBe(json_encode([
        '@type' => 'Question',
        'name' => 'How to choose an office chair?',
        'text' => 'Full question text',
        'upvoteCount' => 5,
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => 'Check the tilt mechanism.',
            'url' => 'https://example.com/q/1#answer-1',
            'upvoteCount' => 12,
        ],
        'suggestedAnswer' => [
            ['@type' => 'Answer', 'text' => 'Look at the upholstery.'],
        ],
        'answerCount' => 2,
    ]));
});

it('derives answer count from marked up answers', function (): void {
    $accepted = Schema::question()->acceptedAnswer(Schema::answer()->text('a'));
    $mixed = Schema::question()
        ->acceptedAnswer(Schema::answer()->text('a'))
        ->suggestedAnswer(Schema::answer()->text('b'))
        ->suggestedAnswer(Schema::answer()->text('c'));
    $suggestedOnly = Schema::question()->suggestedAnswer(Schema::answer()->text('b'));

    expect($accepted->jsonSerialize()['answerCount'])->toBe(1)
        ->and($mixed->jsonSerialize()['answerCount'])->toBe(3)
        ->and($suggestedOnly->jsonSerialize()['answerCount'])->toBe(1);
});

it('prefers an explicit answer count over the derived one', function (): void {
    $question = Schema::question()
        ->answerCount(31)
        ->acceptedAnswer(Schema::answer()->text('a'));

    expect($question->jsonSerialize()['answerCount'])->toBe(31);
});

it('wraps a plain string author into a person', function (): void {
    $question = Schema::question()->author('Ivan');
    $answer = Schema::answer()->author('Maria');

    expect(json_encode($question->jsonSerialize()['author']))
        ->toBe('{"@type":"Person","name":"Ivan"}')
        ->and(json_encode($answer->jsonSerialize()['author']))
        ->toBe('{"@type":"Person","name":"Maria"}');
});

it('keeps a person or organization author as passed', function (): void {
    $person = Schema::person()->name('Ivan')->url('https://example.com/ivan');
    $org = Schema::organization()->name('Example LLC');

    expect(Schema::question()->author($person)->jsonSerialize()['author'])->toBe($person)
        ->and(Schema::answer()->author($org)->jsonSerialize()['author'])->toBe($org);
});

it('formats date created from a datetime instance', function (): void {
    $question = Schema::question()->dateCreated(new DateTimeImmutable('2025-02-15 12:00:00', new DateTimeZone('UTC')));
    $answer = Schema::answer()->dateCreated('2025-02-15');

    expect($question->jsonSerialize()['dateCreated'])->toBe('2025-02-15T12:00:00+00:00')
        ->and($answer->jsonSerialize()['dateCreated'])->toBe('2025-02-15');
});

it('replaces the accepted answer and accumulates suggested answers', function (): void {
    $question = Schema::question()
        ->acceptedAnswer(Schema::answer()->text('first'))
        ->acceptedAnswer(Schema::answer()->text('second'))
        ->suggestedAnswer(Schema::answer()->text('one'))
        ->suggestedAnswer(Schema::answer()->text('two'));

    $data = $question->jsonSerialize();

    expect(json_encode($data['acceptedAnswer']))->toContain('second')
        ->and($data['suggestedAnswer'])->toHaveCount(2)
        ->and($data['answerCount'])->toBe(3);
});

it('accepts raw properties through the escape hatch', function (): void {
    $page = Schema::qaPage()->property('name', 'Delivery questions');

    expect($page->jsonSerialize())->toBe([
        '@type' => 'QAPage',
        'name' => 'Delivery questions',
    ]);
});

it('joins the graph next to other entities through the json-ld builder', function (): void {
    $html = (new JsonLdBuilder())
        ->add(Schema::webSite()->name('Example'))
        ->add(Schema::qaPage()->question(Schema::question()->name('Question?')))
        ->render();

    expect($html)->toContain('"@graph"')
        ->and($html)->toContain('"@type":"WebSite"')
        ->and($html)->toContain('"@type":"QAPage"')
        ->and($html)->toContain('"answerCount":0');
});

it('feeds a question builder into a from page override', function (): void {
    $builder = new JsonLdBuilder();
    $builder->fromPage('QAPage', [
        'mainEntity' => Schema::question()
            ->name('Question?')
            ->acceptedAnswer(Schema::answer()->text('Answer.')),
    ]);

    expect($builder->render())
        ->toContain('"@type":"QAPage"')
        ->and($builder->render())->toContain('"@type":"Question","name":"Question?"')
        ->and($builder->render())->toContain('"answerCount":1');
});
