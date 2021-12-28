<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testFillable()
    {
        $category = new Category();
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $category->getFillable());
    }

    public function testIfUsesTraits()
    {
        $expectedTraits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($expectedTraits, $categoryTraits);
    }

    public function testModelUsesUuid()
    {
        $category = new Category();
        $this->assertEquals("string", $category->getKeyType());
        $this->assertFalse($category->getIncrementing());
    }
}
