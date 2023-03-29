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
        //create user
        $user = User::factory()->create();
        
        //create a link
        $link = $user->links()->create(
            Link::factory()->make()->toArray()
        );

        //user accesses link.shortcut with an undentified shortcut
        $response = $this->get(route('link.shortcut', ['shortcut' => $link->shortcut]));

        //app will redirect him to the original link
        $response->assertRedirect($link->link);
    }


    /**
     * test if user access a not found shortcut.
     *
     * @return void
     */
    public function test_guests_access_a_not_found_shortcut_link_returns_404()
    {
        //prepare link data
        $link = Link::factory()->make();

        //unauthenticated user accesses link.shortcut
        $response = $this->get(route('link.shortcut', ['shortcut' => $link->shortcut]));

        //app will return 404 not found
        $response->assertStatus(404);
    }

    /**
     * test if guests can't access create page.
     *
     * @return void
     */
    public function test_guests_cant_access_links_create_page()
    {

        //unauthenticated user accesses link.create
        $response = $this->get(route('link.create'));
        
        //app will redirect him to login
        $response->assertStatus(302)
                ->assertRedirect('dashboard/login');
        
    }

    
    /**
     * test if authenticated user can access create page.
     *
     * @return void
     */
    public function test_authenticated_user_can_access_links_create_page()
    {
        //create user
        $user = User::factory()->create();

        //authenticated user accesses link.create
        $response = $this->actingAs($user)->get(route('link.create'));

        //app will return create page
        $response->assertStatus(200);
    }


    /**
     * test if authenticated user can add a link.
     *
     * @return void
     */
    public function test_authenticated_user_can_add_a_link()
    {
        //create user
        $user = User::factory()->create();

        //prepare link data
        $link = Link::factory()->make();
        
        //authenticated user submits data to link.store
        $response = $this->actingAs($user)->post(route('link.store'), $link->toArray());

        //database has the posted link
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
        //create user
        $user = User::factory()->create();

        //create 5 links for this user
        for($i = 0 ; $i<=5 ; $i++)
        {
            $user->links()->create(Link::factory()->make()->toArray());
        }

        //prepare new link
        $sixth_link = Link::factory()->make();
        
        //authenticated user submits data to link.store
        $response = $this->actingAs($user)->post(route('link.store'), $sixth_link->toArray());

        //app will return error message You can't create more than 5 links
        $response->assertSessionHasErrors( ['link' => "You can't create more than 5 links."] );
    }

    /**
     * test if authenticated user cant add more than 5 links.
     *
     * @return void
     */
    public function test_links_add_will_not_go_higher_than_20()
    {
        //create user
        

        //create 5 links for this user
        for($i = Link::count() ; $i<20 ; $i++)
        {
            $user = User::factory()->create();
            $user->links()->create(Link::factory()->make()->toArray());
        }

        $this->assertEquals(20, Link::count());
        
        //prepare new link
        $new_link = Link::factory()->make();
        
        //authenticated user submits data to link.store
        $response = $this->actingAs($user)->post(route('link.store'), $new_link->toArray());

        
        $response->assertSessionHas('created', trans('trans.link_created'));
        
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

        $response->assertSessionHas('deleted', trans('trans.link_deleted'));

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

    public function test_app_custom_command_will_delete_expired_links()
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
