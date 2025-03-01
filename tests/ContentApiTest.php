<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\ContentApi\ContentApi;

beforeEach(function () {
    Http::preventStrayRequests();

});

it('can get posts', function () {
    Http::fake([
        'https://content.spatie.be/api/collections/posts/entries?filter%5Bproduct%5D=mailcoach&limit=20&page=1&sort=-date&theme=github-light' => Http::response([
            'data' => [
                ['title' => 'A post', 'slug' => 'a-post', 'header_image' => null, 'header_image_presets' => ['w-320' => ['url' => 'test', 'width' => 320, 'height' => 320]], 'summary' => 'summary', 'authors' => [], 'content' => '', 'published' => true, 'date' => now()->format('Y-m-d H:i:s'), 'updated_at' => now()->format('Y-m-d H:i:s')],
            ],
        ]),
    ]);

    $posts = ContentApi::getPosts('mailcoach');

    expect($posts->count())->toBe(1);
    expect($posts->first()->header_image_presets->count())->toBe(1);
    expect(Cache::has('posts-mailcoach-1-20--date'))->toBeTrue();
});

it('returns a cached version when the request fails', function () {
    Http::fake([
        'https://content.spatie.be/api/collections/posts/entries?filter%5Bproduct%5D=mailcoach&limit=20&page=1&sort=-date&theme=github-light' => Http::sequence([
            Http::response([
                'data' => [
                    ['title' => 'A post', 'slug' => 'a-post', 'header_image' => null, 'summary' => 'summary', 'authors' => [], 'content' => '', 'published' => true, 'date' => now()->format('Y-m-d H:i:s'), 'updated_at' => now()->format('Y-m-d H:i:s')],
                ],
            ]),
            Http::response([], 500),
        ]),
    ]);

    ContentApi::getPosts('mailcoach');
    $posts = ContentApi::getPosts('mailcoach');

    expect($posts->count())->toBe(1);
});

it('can get a post', function () {
    Http::fake([
        'https://content.spatie.be/api/collections/posts/entries?filter%5Bproduct%5D=mailcoach&filter%5Bslug%5D=a-post&theme=github-light' => Http::response([
            'data' => [
                ['title' => 'A post', 'slug' => 'a-post', 'header_image' => null, 'summary' => 'summary', 'authors' => [], 'content' => '', 'published' => true, 'date' => now()->format('Y-m-d H:i:s'), 'updated_at' => now()->format('Y-m-d H:i:s')],
            ],
        ]),
    ]);

    $post = ContentApi::getPost('mailcoach', 'a-post');

    expect($post->title)->toBe('A post');
    expect(Cache::has('post-mailcoach-a-post'))->toBeTrue();
});

it('returns a cached version of a post when the request fails', function () {
    Http::fake([
        'https://content.spatie.be/api/collections/posts/entries?filter%5Bproduct%5D=mailcoach&filter%5Bslug%5D=a-post&theme=github-light' => Http::sequence([
            Http::response([
                'data' => [
                    ['title' => 'A post', 'slug' => 'a-post', 'header_image' => null, 'summary' => 'summary', 'authors' => [], 'content' => '', 'published' => true, 'date' => now()->format('Y-m-d H:i:s'), 'updated_at' => now()->format('Y-m-d H:i:s')],
                ],
            ]),
            Http::response([], 500),
        ]),
    ]);

    ContentApi::getPost('mailcoach', 'a-post');
    $post = ContentApi::getPost('mailcoach', 'a-post');

    expect($post->title)->toBe('A post');
    expect(Cache::has('post-mailcoach-a-post'))->toBeTrue();
});
