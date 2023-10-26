<?php

namespace Spatie\ContentApi;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\ContentApi\Data\Post;

class ContentApi
{
    const BASE_URL = 'https://content.spatie.be/api';

    public static function getPosts(string $product, int $page = 1, int $perPage = 20, string $sort = '-date', string $theme = 'github-light', array $filters = []): Paginator
    {
        $response = Http::get(static::BASE_URL.'/collections/posts/entries', [
            'filter' => array_merge($filters, [
                'product' => $product,
            ]),
            'limit' => $perPage,
            'page' => $page,
            'sort' => $sort,
            'theme' => $theme,
        ]);

        if (! $response->successful()) {
            report($response->toException());

            if ($posts = Cache::get("posts-{$product}-{$page}-{$perPage}-{$sort}")) {
                return $posts;
            }

            return new Paginator([], $perPage, $page);
        }

        $data = collect($response->json('data'))->filter()->map(function (array $post) {
            return Post::fromResponse($post);
        });

        /**
         * When the API has more data, fake an extra item so the "next page" link appears
         */
        $paddedData = $data->when($data->count() === $perPage && $response->json('meta.last_page') > $page, fn ($data) => $data->add([]));

        $posts = new Paginator(
            items: $paddedData,
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => Paginator::resolveCurrentPath(),
            ]
        );

        Cache::put("posts-{$product}-{$page}-{$perPage}-{$sort}", $posts);

        return $posts;
    }

    public static function getPost(string $product, string $slug, string $theme = 'github-light'): ?Post
    {
        $response = Http::get(self::BASE_URL.'/collections/posts/entries', [
            'filter' => [
                'product' => $product,
                'slug' => $slug,
            ],
            'theme' => $theme,
        ]);

        if ($response->serverError()) {
            report($response->toException());

            return Cache::get("post-{$product}-{$slug}");
        }

        if (! $postData = $response->json('data.0')) {
            return null;
        }

        $post = Post::fromResponse($postData);

        Cache::put("post-{$product}-{$slug}", $post);

        return $post;
    }
}
