<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

final class CommentsSeeder extends Seeder
{
    public function run(): void
    {
        $this->db->table('comments')->insertBatch([
            ['name' => 'one@example.com', 'text' => 'Первый комментарий', 'date' => '2026-01-03'],
            ['name' => 'two@example.com', 'text' => 'Второй комментарий', 'date' => '2026-01-01'],
            ['name' => 'three@example.com', 'text' => 'Третий комментарий', 'date' => '2026-01-02'],
            ['name' => 'four@example.com', 'text' => 'Четвёртый комментарий', 'date' => '2026-01-01'],
            ['name' => 'five@example.com', 'text' => 'Пятый комментарий', 'date' => '2026-01-05'],
            ['name' => 'six@example.com', 'text' => 'Шестой комментарий', 'date' => '2026-01-04'],
            ['name' => 'seven@example.com', 'text' => '<script>alert(1)</script>', 'date' => '2026-01-06'],
        ]);
    }
}
