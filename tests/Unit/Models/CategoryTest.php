<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;

use Tests\TestCase;

class CategoryTest extends TestCase
{
    private $category;

    // Will run only once before any tests begin
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    // Will run only once after all tests are done
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    // Will run before every test, rebuilding the app
    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    // Will run after every test, freeing allocated resources
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testFillable()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->category->getFillable());
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
        $this->assertEquals("string", $this->category->getKeyType());
        $this->assertFalse($this->category->getIncrementing());
    }
}
