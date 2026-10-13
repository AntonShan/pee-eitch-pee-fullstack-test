<?php

use App\Models\CommentModel;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\CommentsSeeder;
use Tests\Support\DatabaseTestCase;

final class CommentTest extends DatabaseTestCase
{
    use FeatureTestTrait;

    protected $seed = CommentsSeeder::class;

    public function testPaginationAndStableDateOrdering(): void
    {
        $model = new CommentModel();
        $state = $model->getPage(1, 'date', 'asc');

        $this->assertSame(7, $state['total']);
        $this->assertSame(3, count($state['items']));
        $this->assertSame('2026-01-01', $state['items'][0]['date']);
        $this->assertLessThan($state['items'][1]['id'], $state['items'][0]['id']);
        $this->assertSame(3, $state['pages']);

        $descending = $model->getPage(1, 'date', 'desc');
        $this->assertSame('2026-01-06', $descending['items'][0]['date']);

        $ascendingIds = $model->getPage(1, 'id', 'asc');
        $this->assertSame(1, (int) $ascendingIds['items'][0]['id']);

        $lastPage = $model->getPage(99, 'id', 'desc');
        $this->assertSame(3, $lastPage['page']);
        $this->assertSame(1, count($lastPage['items']));
    }

    public function testInvalidCommentIsNotInserted(): void
    {
        $model = new CommentModel();

        $this->assertFalse($model->insert([
            'name' => 'not-an-email',
            'text' => '',
            'date' => 'invalid-date',
        ]));
        $this->assertSame(7, $model->countAllResults());
        $this->assertArrayHasKey('name', $model->errors());
        $this->assertArrayHasKey('date', $model->errors());
    }

    public function testCreateEndpointReturnsValidationErrors(): void
    {
        $result = $this->post('/comments', [
            'name' => 'bad-email',
            'text' => '',
            'date' => '2026-13-99',
        ]);

        $result->assertStatus(422);
        $result->assertJSONFragment(['message' => 'Проверьте заполнение формы.']);
    }

    public function testCreateEndpointCreatesComment(): void
    {
        $result = $this->post('/comments', [
            'name' => 'created@example.com',
            'text' => 'Новый комментарий',
            'date' => '2026-10-13',
        ]);

        $result->assertStatus(201);
        $this->assertSame(8, (new CommentModel())->countAllResults());
    }

    public function testDeleteEndpointRemovesComment(): void
    {
        $model = new CommentModel();
        $id = (int) $model->first()['id'];

        $result = $this->delete('/comments/' . $id);

        $result->assertStatus(200);
        $this->assertNull($model->find($id));
    }

    public function testUnknownCommentReturnsNotFound(): void
    {
        $result = $this->delete('/comments/99999');

        $result->assertStatus(404);
    }

    public function testCommentOutputIsEscaped(): void
    {
        $result = $this->get('/');

        $result->assertStatus(200);
        $body = $result->getBody();
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $body);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $body);
    }
}
