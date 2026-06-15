<?php

use App\Models\T02User as User;
use Database\Factories\T01RoleFactory;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    T01RoleFactory::new()->create(['id_role' => 1, 'nama_role' => 'Nazhir']);
    $user = User::factory()->create(['id_role' => 1]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});