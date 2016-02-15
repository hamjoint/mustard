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
        $this->assertSessionHasErrors('parent_category_id');
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

    public function testListingDurationsPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@showListingDurationsTable'))
            ->assertResponseOk();
    }

    public function testUsersPage()
    {
        $this->actingAs(factory(Hamjoint\Mustard\User::class)->make())
            ->get(action('\Hamjoint\Mustard\Http\Controllers\AdminController@showUsersTable'))
            ->assertResponseOk();
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
}
