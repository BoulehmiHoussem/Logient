<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

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
        $response->assertRedirect($link->link);
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
     * test if authenticated user can access create page.
     *
     * @return void
     */
    public function test_authenticated_user_can_access_links_create_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('link.create'));

        $response->assertStatus(200);
    }



    /**
     * test if unauthenticated user cannot access create page.
     *
     * @return void
     */
    public function test_unauthenticated_user_canot_access_links_create_page()
    {

        $response = $this->get(route('link.create'));

        $response->assertStatus(302);
    }


    /**
     * test if authenticated user can add a link.
     *
     * @return void
     */
    public function test_authenticated_user_can_add_a_link()
    {
        $user = User::factory()->create();
        $link = Link::factory()->make();
        
        $response = $this->actingAs($user)->post(route('link.store'), $link->toArray());

        $this->assertDatabaseHas('links', [
            'link' => $link->link,
            'user_id' => $user->id
        ]);
    }

    /**
     * test if authenticated user cant add more than 5 links.
     *
     * @return void
     */
    public function test_authenticated_user_cant_add_more_than_5_links()
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

    /**
     * test if authenticated user can delete one of his links.
     *
     * @return void
     */
    public function test_authenticated_user_can_delete_one_of_his_links()
    {
        $user = User::factory()->create();

        $link = $user->links()->create(Link::factory()->make()->toArray());

        $this->assertDatabaseHas('links', [
            'id' => $link->id
        ]);
        
        $response = $this->actingAs($user)->delete(route('link.destroy', ['link' => $link->id]));

        $this->assertDatabaseMissing('links', [
            'id' => $link->id
        ]);
    }

    /**
     * test if authenticated user cant delete other users links.
     *
     * @return void
     */
    public function test_authenticated_user_cant_delete_other_users_links()
    {
        $user_1 = User::factory()->create();
        $user_2 = User::factory()->create();

        $link_user_1 = $user_1->links()->create(Link::factory()->make()->toArray());
        $link_user_2 = $user_2->links()->create(Link::factory()->make()->toArray());

        $this->assertDatabaseHas('links', [
            'id' => $link_user_1->id,
            'user_id' => $user_1->id,
        ]);

        $this->assertDatabaseHas('links', [
            'id' => $link_user_2->id,
            'user_id' => $user_2->id,
        ]);
        
        $response = $this->actingAs($user_1)->delete(route('link.destroy', ['link' => $link_user_2->id]));

        $this->assertDatabaseHas('links', [
            'id' => $link_user_2->id
        ]); 

        $response->assertStatus(404);
    }


    /**
     * test if all users can access shortcuts links.
     *
     * @return void
     */
    public function test_app_will_log_access_informations()
    {
        $user = User::factory()->create();
    
        $link = $user->links()->create(
            Link::factory()->make()->toArray()
        );
        $response = $this->actingAs($user)->get(route('link.shortcut', ['shortcut' => $link->shortcut]));
        
        $logPath = storage_path('logs/access.log');

        $this->assertFileExists($logPath);
        
        $logLines = file($logPath);
        
        $lastLogLine = end($logLines);
    
        
        
        $logContent = file_get_contents($logPath);

        $url = url($link->shortcut);
        
        $this->assertStringContainsString("Lien accédé: {$url}", $lastLogLine);
    }

    public function test_expired_links_are_deleted()
    {
        // Create a link that is exactly 24 hours old

        $user = User::factory()->create();

        $OldLink = Link::factory()->make();
        $OldLink->created_at = Carbon::now()->subHours(24);
        $OldLink->user_id = $user->id;
        
        $OldLink->save();
        

        //Assert link created 
        $this->assertDatabaseHas('links', [
            'id' => $OldLink->id
        ]);

        // Create a link that is less than 24 hours old
        $recentLink = $user->links()->create(Link::factory()->make()->toArray());
        

        // Run the command that deletes expired links
        Artisan::call('links:delete-expired');

        // Assert that the old link was deleted
        $this->assertDatabaseMissing('links', ['id' => $OldLink->id]);

        // Assert that the recent link still exists
        $this->assertDatabaseHas('links', ['id' => $recentLink->id]);
    }
}
