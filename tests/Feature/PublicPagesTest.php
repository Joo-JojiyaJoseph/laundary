<?php

use Database\Seeders\DatabaseSeeder;

it('renders the home page', function () {
    $this->seed(DatabaseSeeder::class);
    $this->get('/')->assertOk();
});

it('redirects old pages to their single-page sections', function () {
    $this->get('/about')->assertRedirect('/#about');
    $this->get('/services')->assertRedirect('/#services');
    $this->get('/contact')->assertRedirect('/#contact');
});

it('blocks guests from the admin dashboard', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
});
