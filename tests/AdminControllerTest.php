<?php

class AdminControllerTest extends TestCase
{
    public function testGuestIsRedirectedToLogin()
    {
        $this->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@index'))
            ->assertRedirectedTo('/login');
    }

    public function testIndexRedirectsToDashboard()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@index'))
            ->assertRedirectedToAction('\Hamjoint\Mustard\Http\Controllers\AdminController@showDashboard');
    }

    public function testDashboardPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@showDashboard'))
            ->assertResponseOk();
    }

    public function testCategoriesPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@showCategoriesTable'))
            ->assertResponseOk();
    }

    public function testCreateCategoryUnknownParent()
    {
        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showCategoriesTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@createCategory'), [
                'parent_category_id' => 1,
                'name'               => 'Test',
                'slug'               => 'test',
                'sort'               => 1,
            ])
            ->assertRedirectedToAction($previous_url);

        $this->notSeeInDatabase('categories', ['name' => 'Test']);

        // Check the error was sent to the user
        $this->assertSessionHasErrors('parent_category_id');
    }

    public function testCreateCategoryValid()
    {
        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showCategoriesTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@createCategory'), [
                'parent_category_id' => null,
                'name'               => 'Test',
                'slug'               => 'test',
            ])
            ->assertRedirectedToAction($previous_url);

        $this->seeInDatabase('categories', [
            'parent_category_id' => null,
            'name'               => 'Test',
            'slug'               => 'test',
        ]);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.category_created');
    }

    public function testUpdateCategoryParentIsChild()
    {
        $category = factory(Hamjoint\Mustard\Category::class)->create();

        $child = factory(Hamjoint\Mustard\Category::class)->create();

        $category->children()->save($child);

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showCategoriesTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@updateCategory'), [
                'category_id'        => $category->categoryId,
                'parent_category_id' => $child->categoryId,
                'name'               => 'Test',
                'slug'               => 'test',
            ])
            ->assertRedirectedToAction($previous_url);

        $this->notSeeInDatabase('categories', [
            'category_id'        => $category->categoryId,
            'parent_category_id' => $child->categoryId,
        ]);

        // Check the error was sent to the user
        $this->assertSessionHasErrors('parent_category_id');
    }

    public function testUpdateCategoryValid()
    {
        $category = factory(Hamjoint\Mustard\Category::class)->create();

        $parent = factory(Hamjoint\Mustard\Category::class)->create();

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showCategoriesTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@updateCategory'), [
                'category_id'        => $category->categoryId,
                'parent_category_id' => $parent->categoryId,
                'name'               => 'Test',
                'slug'               => 'test',
            ])
            ->assertRedirectedToAction($previous_url);

        $this->seeInDatabase('categories', [
            'category_id'        => $category->categoryId,
            'parent_category_id' => $parent->categoryId,
            'name'               => 'Test',
            'slug'               => 'test',
        ]);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.category_updated');
    }

    public function testDeleteCategoryWithItems()
    {
        $category = factory(Hamjoint\Mustard\Category::class)->create();

        $item = factory(Hamjoint\Mustard\Item::class)->create();

        $item->categories()->attach($category);

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showCategoriesTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@deleteCategory'), [
                'category_id' => $category->categoryId,
            ])
            ->assertRedirectedToAction($previous_url);

        $this->seeInDatabase('categories', [
            'category_id' => $category->categoryId,
        ]);

        // Check the error was sent to the user
        $this->assertSessionHasErrors('category_id');
    }

    public function testDeleteCategoryValid()
    {
        $category = factory(Hamjoint\Mustard\Category::class)->create();

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showCategoriesTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@deleteCategory'), [
                'category_id' => $category->categoryId,
            ])
            ->assertRedirectedToAction($previous_url);

        $this->notSeeInDatabase('categories', ['category_id' => $category->categoryId]);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.category_deleted');
    }

    public function testSortCategories()
    {
        for ($i = 0; $i < 3; $i++) {
            $category = factory(Hamjoint\Mustard\Category::class)->create();

            $categories[$category->categoryId] = $i;
        }

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showCategoriesTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@sortCategories'), [
                'categories' => $categories
            ])
            ->assertRedirectedToAction($previous_url);

        foreach ($categories as $category_id => $sort) {
            $this->seeInDatabase('categories', [
                'category_id' => $category_id,
                'sort' => $sort,
            ]);
        }

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.categories_sorted');
    }

    public function testItemsPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@showItemsTable'))
            ->assertResponseOk();
    }

    public function testItemConditionsPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@showItemConditionsTable'))
            ->assertResponseOk();
    }

    public function testCreateItemConditionValid()
    {
        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showItemConditionsTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@createItemCondition'), [
                'name' => 'Test',
            ])
            ->assertRedirectedToAction($previous_url);

        $this->seeInDatabase('item_conditions', [
            'name' => 'Test',
        ]);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.item_condition_created');
    }

    public function testUpdateItemConditionValid()
    {
        $item_condition = factory(Hamjoint\Mustard\ItemCondition::class)->create();

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showItemConditionsTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@updateItemCondition'), [
                'item_condition_id' => $item_condition->itemConditionId,
                'name' => 'Test',
            ])
            ->assertRedirectedToAction($previous_url);

        $this->seeInDatabase('item_conditions', [
            'item_condition_id' => $item_condition->itemConditionId,
            'name' => 'Test',
        ]);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.item_condition_updated');
    }

    public function testDeleteItemConditionWithItems()
    {
        $item_condition = factory(Hamjoint\Mustard\ItemCondition::class)->create();

        $item = factory(Hamjoint\Mustard\Item::class)->create();

        $item_condition->items()->save($item);

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showItemConditionsTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@deleteItemCondition'), [
                'item_condition_id' => $item_condition->itemConditionId,
            ])
            ->assertRedirectedToAction($previous_url);

        $this->seeInDatabase('item_conditions', [
            'item_condition_id' => $item_condition->itemConditionId,
        ]);

        // Check the error was sent to the user
        $this->assertSessionHasErrors('item_condition_id');
    }

    public function testDeleteItemConditionValid()
    {
        $item_condition = factory(Hamjoint\Mustard\ItemCondition::class)->create();

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showItemConditionsTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@deleteItemCondition'), [
                'item_condition_id' => $item_condition->itemConditionId,
            ])
            ->assertRedirectedToAction($previous_url);

        $this->notSeeInDatabase('item_conditions', [
            'item_condition_id' => $item_condition->itemConditionId,
        ]);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.item_condition_deleted');
    }

    public function testSortItemConditions()
    {
        for ($i = 0; $i < 3; $i++) {
            $item_condition = factory(Hamjoint\Mustard\ItemCondition::class)->create();

            $item_conditions[$item_condition->itemConditionId] = $i;
        }

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showItemConditionsTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@sortItemConditions'), [
                'item_conditions' => $item_conditions
            ])
            ->assertRedirectedToAction($previous_url);

        foreach ($item_conditions as $item_condition_id => $sort) {
            $this->seeInDatabase('item_conditions', [
                'item_condition_id' => $item_condition_id,
                'sort' => $sort,
            ]);
        }

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.item_conditions_sorted');
    }

    public function testListingDurationsPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@showListingDurationsTable'))
            ->assertResponseOk();
    }

    public function testCreateListingDurationValid()
    {
        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showListingDurationsTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@createListingDuration'), [
                'duration' => 3600,
            ])
            ->assertRedirectedToAction($previous_url);

        $this->seeInDatabase('listing_durations', [
            'duration' => 3600,
        ]);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.listing_duration_created');
    }

    public function testUpdateListingDurationValid()
    {
        $listing_duration = factory(Hamjoint\Mustard\ListingDuration::class)->create();

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showListingDurationsTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@updateListingDuration'), [
                'listing_duration_id' => $listing_duration->listingDurationId,
                'duration' => 3600,
            ])
            ->assertRedirectedToAction($previous_url);

        $this->seeInDatabase('listing_durations', [
            'listing_duration_id' => $listing_duration->listingDurationId,
            'duration' => 3600,
        ]);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.listing_duration_updated');
    }

    public function testDeleteListingDurationValid()
    {
        $listing_duration = factory(Hamjoint\Mustard\ListingDuration::class)->create();

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showListingDurationsTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@deleteListingDuration'), [
                'listing_duration_id' => $listing_duration->listingDurationId,
            ])
            ->assertRedirectedToAction($previous_url);

        $this->notSeeInDatabase('listing_durations', [
            'listing_duration_id' => $listing_duration->listingDurationId,
        ]);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.listing_duration_deleted');
    }

    public function testSortListingDurations()
    {
        for ($i = 0; $i < 3; $i++) {
            $listing_duration = factory(Hamjoint\Mustard\ListingDuration::class)->create();

            $listing_durations[$listing_duration->listingDurationId] = $i;
        }

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showListingDurationsTable';

        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@sortListingDurations'), [
                'listing_durations' => $listing_durations
            ])
            ->assertRedirectedToAction($previous_url);

        foreach ($listing_durations as $listing_duration_id => $sort) {
            $this->seeInDatabase('listing_durations', [
                'listing_duration_id' => $listing_duration_id,
                'sort' => $sort,
            ]);
        }

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.listing_durations_sorted');
    }

    public function testUsersPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@showUsersTable'))
            ->assertResponseOk();
    }

    public function testUserResetPassword()
    {
        $user = factory(Hamjoint\Mustard\User::class)->create();

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showUsersTable';

        $this->expectMail(1);

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@resetUserPassword'), [
                'user_id' => $user->userId,
            ])
            ->assertRedirectedToAction($previous_url);

        $this->seeInDatabase($this->app['config']->get('auth.passwords.users.table'), [
            'email' => $user->email,
        ]);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.password_reset');
    }

    public function testSettingsPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@showSettingsTable'))
            ->assertResponseOk();
    }

    public function testMailoutPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@showMailoutForm'))
            ->assertResponseOk();
    }

    public function testSendMailout()
    {
        $user = factory(Hamjoint\Mustard\User::class)->create();

        for ($i = 0; $i < 3; $i++) {
            $recipient = factory(Hamjoint\Mustard\User::class)->create();

            $recipients[] = $recipient->userId;
        }

        $this->app->mailer->getSwiftMailer()->expects($this->exactly(3))->method('send');

        $previous_url = '\Hamjoint\Mustard\Http\Controllers\AdminController@showMailoutForm';

        $this->actingAs($user)
            ->withSession(['_previous.url' => action($previous_url)])
            ->post(action('\Hamjoint\Mustard\Http\Controllers\AdminController@sendMailout'), [
                'users'   => $recipients,
                'subject' => 'This is a test subject',
                'body'    => 'This is a test body',
            ])
            ->assertRedirectedToAction($previous_url);

        // Check the confirmation message was sent to the user
        $this->assertSessionHas('status', 'mustard::admin.mailout_sent');
    }
}
