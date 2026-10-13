<?php

namespace Tests\Support\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CommentsMigration extends Migration
{
    protected $DBGroup = 'tests';

    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => ['type' => 'VARCHAR', 'constraint' => 254],
            'text' => ['type' => 'TEXT'],
            'date' => ['type' => 'VARCHAR', 'constraint' => 10],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('date');
        $this->forge->createTable('comments');
    }

    public function down(): void
    {
        $this->forge->dropTable('comments');
    }
}
