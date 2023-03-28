<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LinksTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * test if all users can access shortcuts links.
     *
     * @return void
     */
    public function test_guests_can_access_shortcut_link()
    {
        $user = User::factory()->create();
        $link = $user->links()->create(
            Link::factory()->make()->toArray()
        );
        $response = $this->get(route('link.shortcut', ['shortcut' => $link->shortcut]));
        $response->assertStatus(200);
    }


    /**
     * test if user access a not found shortcut.
     *
     * @return void
     */
    public function test_guests_access_a_not_found_shortcut_link_returns_404()
    {
        $link = Link::factory()->make();
        $response = $this->get(route('link.shortcut', ['shortcut' => $link->shortcut]));
        $response->assertStatus(404);
    }

    /**
     * test if authenticated user can access create page.
     *
     * @return void
     */
    public function test_authenticated_users_can_access_links_create_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('link.create'));

        $response->assertStatus(200);
    }


    /**
     * test if guests can't access create page.
     *
     * @return void
     */
    public function test_guests_cant_access_links_create_page()
    {
        $response = $this->get(route('link.create'));

        $response->assertStatus(302);
    }


    /**
     * test if unauthenticated user cannot access create page.
     *
     * @return void
     */
    public function test_unauthenticated_users_canot_access_links_create_page()
    {

        $response = $this->get(route('link.create'));

        $response->assertStatus(302);
    }


    /**
     * test if authenticated user can add a link.
     *
     * @return void
     */
    public function test_authenticated_users_can_add_a_link()
    {
        $user = User::factory()->create();
        $link = Link::factory()->make();
        
        $response = $this->actingAs($user)->post(route('link.store'), $link->toArray());

        $this->assertDatabaseHas('links', [
            'link' => $link->link,
            'shortcut' => $link->shortcut,
            'user_id' => $user->id
        ]);
    }

    /**
     * test if authenticated user cant add more than 5 links.
     *
     * @return void
     */
    public function test_authenticated_users_cant_add_more_than_5_links()
    {
        $user = User::factory()->create();
        for($i = 0 ; $i<=5 ; $i++)
        {
            $user->links()->create(Link::factory()->make()->toArray());
        }

        $sixth_link = Link::factory()->make();
        
        $response = $this->actingAs($user)->post(route('link.store'), $sixth_link->toArray());

        $response->assertSessionHasErrors('link');
    }
}
