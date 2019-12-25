<?php

namespace App\Listeners;

use TightenCo\Jigsaw\Jigsaw;

class GenerateEstimateReadingTime {

    public function handle(Jigsaw $jigsaw)
    {
        $jigsaw->getCollection('posts')->map(function ($post) {
            $post->reading_time = calc_reading_time($post->getContent(), 200);
        });
    }
}
