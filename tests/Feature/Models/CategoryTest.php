<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertCount(1, $categories);
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $categoryKeys);
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'testing'
        ]);
        $category->refresh();

        $this->assertEquals('testing', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'testing',
            'description' => null
        ]);
        $category->refresh();

        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'testing',
            'description' => 'test_description'
        ]);
        $category->refresh();

        $this->assertEquals('test_description', $category->description);

        $category = Category::create([
            'name' => 'testing',
            'is_active' => false
        ]);
        $category->refresh();

        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'testing',
            'is_active' => true
        ]);
        $category->refresh();

        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'test_description',
            'is_active' => true
        ]);

        $data = [
            'name' => 'name_updated',
            'description' => 'description_updated',
            'is_active' => false
        ];
        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        factory(Category::class, 1)->create();

        $category = Category::first();
        $category->delete();

        $categories = Category::all();
        $this->assertCount(0, $categories);
    }

    public function testEntryHasUuid()
    {
        factory(Category::class, 1)->create();

        $category = Category::first();
        $this->assertTrue(Str::isUuid($category->id));
    }
}
