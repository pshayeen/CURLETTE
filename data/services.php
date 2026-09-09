<?php

// Single source of truth for the service catalog.
function getServices(): array
{
    return [
        [
            'name' => 'Curl Consultation',
            'price' => 'PHP 350',
            'duration' => '30–45 min',
            'image' => 'assets/service.jpg',
            'description' => 'A personalized consultation to understand your curl pattern, concerns, routine, and hair goals.',
            'includes' => 'Curl assessment, routine recommendations, and personalized care advice.'
        ],
        [
            'name' => 'Curl Styling',
            'price' => 'PHP 650',
            'duration' => '60–90 min',
            'image' => 'assets/service1.jpg',
            'description' => 'Professional styling that brings out the natural shape, definition, and movement of your curls.',
            'includes' => 'Wash, styling products, curl definition, and finishing.'
        ],
        [
            'name' => 'Curl Hair Cut',
            'price' => 'PHP 750',
            'duration' => '60–90 min',
            'image' => 'assets/service3.jpg',
            'description' => 'A curl-focused haircut shaped around your natural texture, density, and desired silhouette.',
            'includes' => 'Curl consultation, tailored haircut, and basic styling.'
        ],
        [
            'name' => 'Curl Hair Color',
            'price' => 'PHP 2,500',
            'duration' => '2–3 hrs',
            'image' => 'assets/service2.jpg',
            'description' => 'A customized color service designed to complement your look while keeping your curls cared for.',
            'includes' => 'Color consultation, customized color application, and curl-friendly finishing.'
        ],
        [
            'name' => 'Deep Conditioning Treatment',
            'price' => 'PHP 550',
            'duration' => '45–60 min',
            'image' => 'assets/service1.jpg',
            'description' => 'A moisture-focused treatment for curls that feel dry, rough, or lacking softness.',
            'includes' => 'Deep conditioning treatment, gentle cleansing, and curl styling.'
        ],
        [
            'name' => 'Curl Repair Treatment',
            'price' => 'PHP 650',
            'duration' => '45–60 min',
            'image' => 'assets/service.jpg',
            'description' => 'A restorative treatment for curls affected by dryness, heat, coloring, or everyday damage.',
            'includes' => 'Repair treatment, conditioning, and a curl-friendly finishing routine.'
        ],
        [
            'name' => 'Wash & Define',
            'price' => 'PHP 450',
            'duration' => '60–75 min',
            'image' => 'assets/service2.jpg',
            'description' => 'A complete wash and definition service for fresh, bouncy, and well-defined curls.',
            'includes' => 'Cleanse, condition, curl definition, and drying.'
        ],
        [
            'name' => 'Special Occasion Styling',
            'price' => 'PHP 850',
            'duration' => '60–90 min',
            'image' => 'assets/service3.jpg',
            'description' => 'A polished curl style created for celebrations, events, photos, and special moments.',
            'includes' => 'Consultation, customized styling, finishing, and event-ready details.'
        ],
    ];
}
