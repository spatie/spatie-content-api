<?php

namespace Spatie\ContentApi\Data;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

final readonly class Post
{
    public function __construct(
        public string $title,
        public string $slug,
        public ?string $header_image,
        public ?string $og_image,
        public ?string $summary,

        /** @property Collection<Author> */
        public Collection $authors,

        /** @property string[] */
        public array $tags,

        public string $content,

        public bool $published,
        public CarbonInterface $date,
        public CarbonInterface $updated_at,
    ) {}

    public static function fromResponse(array $post): self
    {
        return new self(
            title: $post['title'],
            slug: $post['slug'],
            header_image: $post['header_image'],
            og_image: $post['og_image'] ?? null,
            summary: $post['summary'],
            authors: collect($post['authors'])
                ->map(fn (array $author) => Author::fromResponse($author)),
            tags: $post['tags'] ?? [],
            content: $post['content'],
            published: $post['published'],
            date: Date::parse($post['date']),
            updated_at: Date::parse($post['updated_at']),
        );
    }
}
