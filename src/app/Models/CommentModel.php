<?php

namespace App\Models;

use CodeIgniter\Model;

final class CommentModel extends Model
{
    public const PER_PAGE = 3;

    /** @var array<string, string> */
    private const SORT_FIELDS = [
        'id'   => 'id',
        'date' => 'date',
    ];

    /** @var list<string> */
    private const DIRECTIONS = ['asc', 'desc'];

    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['name', 'text', 'date'];

    protected $validationRules = [
        'name' => 'required|valid_email|max_length[254]',
        'text' => 'required|max_length[5000]',
        'date' => 'required|valid_date[Y-m-d]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'    => 'Укажите email.',
            'valid_email' => 'Укажите корректный email.',
            'max_length'  => 'Email слишком длинный.',
        ],
        'text' => [
            'required'   => 'Введите текст комментария.',
            'max_length' => 'Комментарий не должен превышать 5000 символов.',
        ],
        'date' => [
            'required'   => 'Выберите дату.',
            'valid_date' => 'Укажите корректную дату.',
        ],
    ];

    /**
     * @return array{items: list<array<string, mixed>>, total: int, pages: int, page: int, sort: string, direction: string}
     */
    public function getPage(int $page = 1, string $sort = 'id', string $direction = 'desc'): array
    {
        [$sort, $direction] = $this->normalizeOrdering($sort, $direction);

        $total = (int) $this->builder()->countAllResults();
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min(max(1, $page), $pages);

        $items = $this->orderedBuilder($sort, $direction)
            ->get(self::PER_PAGE, ($page - 1) * self::PER_PAGE)
            ->getResultArray();

        return [
            'items'     => $items,
            'total'     => $total,
            'pages'     => $pages,
            'page'      => $page,
            'sort'      => $sort,
            'direction' => $direction,
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    public function normalizeOrdering(string $sort, string $direction): array
    {
        $sort = array_key_exists($sort, self::SORT_FIELDS) ? $sort : 'id';
        $direction = in_array(strtolower($direction), self::DIRECTIONS, true)
            ? strtolower($direction)
            : 'desc';

        return [$sort, $direction];
    }

    private function orderedBuilder(string $sort, string $direction)
    {
        $builder = $this->builder();
        $column = self::SORT_FIELDS[$sort];

        return $builder
            ->orderBy($column, strtoupper($direction))
            ->orderBy('id', strtoupper($direction));
    }
}
