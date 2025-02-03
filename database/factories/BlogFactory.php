<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class BlogFactory extends Factory
{
    // The name of the model that this factory is for
    protected $model = Blog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence, // Random sentence for the title
            'content' => $this->faker->paragraph, // Random paragraph for the content
        ];
    }
}
