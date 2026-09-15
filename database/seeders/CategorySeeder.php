<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Salary', 'slug' => 'salary', 'type' => 'income', 'color' => '#10B981', 'icon' => 'banknotes'],
            ['name' => 'Freelance', 'slug' => 'freelance', 'type' => 'income', 'color' => '#06B6D4', 'icon' => 'briefcase'],
            ['name' => 'Investments', 'slug' => 'investments', 'type' => 'income', 'color' => '#8B5CF6', 'icon' => 'chart-bar'],
            ['name' => 'Gifts Received', 'slug' => 'gifts-received', 'type' => 'income', 'color' => '#EC4899', 'icon' => 'gift'],
            ['name' => 'Food & Dining', 'slug' => 'food-dining', 'type' => 'expense', 'color' => '#F59E0B', 'icon' => 'utensils'],
            ['name' => 'Transportation', 'slug' => 'transportation', 'type' => 'expense', 'color' => '#3B82F6', 'icon' => 'car'],
            ['name' => 'Housing', 'slug' => 'housing', 'type' => 'expense', 'color' => '#EF4444', 'icon' => 'home'],
            ['name' => 'Utilities', 'slug' => 'utilities', 'type' => 'expense', 'color' => '#F97316', 'icon' => 'bolt'],
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'type' => 'expense', 'color' => '#A855F7', 'icon' => 'film'],
            ['name' => 'Shopping', 'slug' => 'shopping', 'type' => 'expense', 'color' => '#EC4899', 'icon' => 'shopping-bag'],
            ['name' => 'Healthcare', 'slug' => 'healthcare', 'type' => 'expense', 'color' => '#14B8A6', 'icon' => 'heart'],
            ['name' => 'Education', 'slug' => 'education', 'type' => 'expense', 'color' => '#6366F1', 'icon' => 'book'],
            ['name' => 'Travel', 'slug' => 'travel', 'type' => 'expense', 'color' => '#0EA5E9', 'icon' => 'plane'],
            ['name' => 'Insurance', 'slug' => 'insurance', 'type' => 'expense', 'color' => '#64748B', 'icon' => 'shield'],
            ['name' => 'Savings', 'slug' => 'savings', 'type' => 'both', 'color' => '#22C55E', 'icon' => 'piggy-bank'],
            ['name' => 'Other', 'slug' => 'other', 'type' => 'both', 'color' => '#6B7280', 'icon' => 'ellipsis'],
        ];

        foreach ($categories as $category) {
            Category::create(array_merge($category, ['is_system' => true]));
        }
    }
}
