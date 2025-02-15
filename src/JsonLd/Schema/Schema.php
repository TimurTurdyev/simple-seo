<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

final class Schema
{
    public static function product(): Product
    {
        return new Product();
    }

    public static function offer(): Offer
    {
        return new Offer();
    }

    public static function aggregateOffer(): AggregateOffer
    {
        return new AggregateOffer();
    }

    public static function aggregateRating(): AggregateRating
    {
        return new AggregateRating();
    }

    public static function article(): Article
    {
        return new Article('Article');
    }

    public static function blogPosting(): Article
    {
        return new Article('BlogPosting');
    }

    public static function person(): Person
    {
        return new Person();
    }

    public static function qaPage(): QaPage
    {
        return new QaPage();
    }

    public static function question(): Question
    {
        return new Question();
    }

    public static function answer(): Answer
    {
        return new Answer();
    }

    public static function breadcrumbs(): Breadcrumbs
    {
        return new Breadcrumbs();
    }

    public static function organization(): Organization
    {
        return new Organization();
    }

    public static function webSite(): WebSite
    {
        return new WebSite();
    }

    public static function localBusiness(): LocalBusiness
    {
        return new LocalBusiness();
    }
}
