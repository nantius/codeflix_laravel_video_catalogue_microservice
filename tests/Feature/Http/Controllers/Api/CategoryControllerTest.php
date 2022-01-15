<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));
        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testStore()
    {
        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test'
        ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test',
            'is_active' => false,
            'description' => 'test description'
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'is_active' => false,
                'description' => 'test description'
            ]);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false
        ]);
        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'is_active' => true,
                'description' => 'description updated'
            ]
        );

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'description' => 'description updated',
                'is_active' => true
            ]);

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                    'name' => 'test',
                    'is_active' => true,
                    'description' => ''
                ]
        );

        $response
            ->assertJsonFragment([
                'description' => null,
            ]);


        $category->description = 'test';

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => null
            ]
        );
    
        $response
                ->assertJsonFragment([
                    'description' => null,
                ]);
    }

    public function testInvalidData()
    {
        // !Alternative way to do the same thing!
        // $response = $this->post(route('categories.store'), [], [
        //     'Accept' => 'application/json',
        //     'Content-Type' => 'application/json'
        // ]);
        
        // POST
        $response = $this->json('POST', route('categories.store'), []);
        $this->assertInvalidRequired($response);

        $response = $this->json('POST', route('categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertInvalidMax($response);
        $this->assertInvalidBoolean($response);


        // PUT
        $category = factory(Category::class)->create();
        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            []
        );
        $this->assertInvalidRequired($response);

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        );
        $this->assertInvalidMax($response);
        $this->assertInvalidBoolean($response);
    }

    protected function assertInvalidRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                Lang::get('validation.required', ['attribute' => 'name'])
            ]);
    }

    protected function assertInvalidMax(TestResponse $response)
    {
        $response
                ->assertStatus(422)
                ->assertJsonValidationErrors(['name'])
                ->assertJsonFragment([
                    Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
                ]);
    }

    protected function assertInvalidBoolean(TestResponse $response)
    {
        $response
                ->assertStatus(422)
                ->assertJsonValidationErrors(['is_active'])
                ->assertJsonFragment([
                    Lang::get('validation.boolean', ['attribute' => 'is active'])
                ]);
    }
}
