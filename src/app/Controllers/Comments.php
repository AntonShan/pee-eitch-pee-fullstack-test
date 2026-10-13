<?php

namespace App\Controllers;

use App\Models\CommentModel;
use CodeIgniter\HTTP\ResponseInterface;

final class Comments extends BaseController
{
    private CommentModel $comments;

    public function __construct()
    {
        $this->comments = new CommentModel();
    }

    public function index(): string
    {
        $state = $this->pageState();

        return view('comments/index', [
            'state'    => $state,
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ]);
    }

    public function fragment(): ResponseInterface
    {
        return $this->response->setBody(view('comments/_list', [
            'state' => $this->pageState(),
        ]));
    }

    public function create(): ResponseInterface
    {
        $data = [
            'name' => $this->postString('name'),
            'text' => $this->postString('text'),
            'date' => $this->postString('date'),
        ];

        $id = $this->comments->insert($data);

        if ($id === false) {
            return $this->json([
                'message'   => 'Проверьте заполнение формы.',
                'errors'    => $this->comments->errors(),
                'csrfHash'  => csrf_hash(),
            ], 422);
        }

        return $this->json([
            'message'  => 'Комментарий добавлен.',
            'id'       => $id,
            'csrfHash' => csrf_hash(),
        ], 201);
    }

    public function delete(int $id): ResponseInterface
    {
        if ($this->comments->find($id) === null) {
            return $this->json([
                'message'  => 'Комментарий не найден.',
                'csrfHash' => csrf_hash(),
            ], 404);
        }

        if (! $this->comments->delete($id)) {
            return $this->json([
                'message'  => 'Не удалось удалить комментарий.',
                'csrfHash' => csrf_hash(),
            ], 500);
        }

        return $this->json([
            'message'  => 'Комментарий удалён.',
            'csrfHash' => csrf_hash(),
        ]);
    }

    /** @return array{items: list<array<string, mixed>>, total: int, pages: int, page: int, sort: string, direction: string} */
    private function pageState(): array
    {
        $pageParam = $this->request->getGet('page');
        $sortParam = $this->request->getGet('sort');
        $directionParam = $this->request->getGet('direction');

        $page = is_numeric($pageParam) ? max(1, (int) $pageParam) : 1;
        $sort = is_string($sortParam) ? $sortParam : '';
        $direction = is_string($directionParam) ? $directionParam : '';

        return $this->comments->getPage($page, $sort, $direction);
    }

    private function postString(string $key): string
    {
        $value = $this->request->getPost($key);

        return is_string($value) ? trim($value) : '';
    }

    /** @param array<string, mixed> $data */
    private function json(array $data, int $status = 200): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON($data);
    }
}
