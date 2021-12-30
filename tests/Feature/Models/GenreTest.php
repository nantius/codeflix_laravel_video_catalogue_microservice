<?php

namespace Tests\Feature;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 1)->create();
        $genres = Genre::all();
        $genreKeys = array_keys($genres->first()->getAttributes());
        $this->assertCount(1, $genres);
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $genreKeys);
    }

    public function testCreate()
    {
        $genre = Genre::create([
            'name' => 'testing'
        ]);
        $genre->refresh();

        $this->assertEquals('testing', $genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Genre::create([
            'name' => 'testing',
            'is_active' => false
        ]);
        $genre->refresh();

        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'testing',
            'is_active' => true
        ]);
        $genre->refresh();

        $this->assertTrue($genre->is_active);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => true
        ]);

        $data = [
            'name' => 'name_updated',
            'is_active' => false
        ];
        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        factory(Genre::class, 1)->create();

        $genre = Genre::first();
        $genre->delete();

        $genres = Genre::all();
        $this->assertCount(0, $genres);
    }

    public function testEntryHasUuid()
    {
        factory(Genre::class, 1)->create();

        $genre = Genre::first();
        $this->assertTrue(Str::isUuid($genre->id));
    }
}
