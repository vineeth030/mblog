<?php

namespace App\Support;

/**
 * Fluent builder for breadcrumb trails.
 *
 * One definition feeds both the visible trail and the Schema.org JSON-LD on
 * the front end, so the two can never drift apart. URLs are absolute (Laravel's
 * route() default) because JSON-LD `item` values must be absolute.
 *
 * Usage:
 *   Breadcrumbs::make()
 *       ->push('Home', route('blog.index'))
 *       ->push($category, route('blog.index', ['category' => $category]))
 *       ->push($title)            // no URL → current page
 *       ->toArray();
 */
class Breadcrumbs
{
    /** @var array<int, array{title: string, url: string|null}> */
    private array $items = [];

    public static function make(): self
    {
        return new self();
    }

    /**
     * Append a crumb. Omit $url for the current page (rendered as plain text).
     */
    public function push(string $title, ?string $url = null): self
    {
        $this->items[] = ['title' => $title, 'url' => $url];

        return $this;
    }

    /**
     * @return array<int, array{title: string, url: string|null}>
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
