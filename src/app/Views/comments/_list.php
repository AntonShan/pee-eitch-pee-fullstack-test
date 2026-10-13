<div id="comments-list"
     data-page="<?= esc((string) $state['page']) ?>"
     data-sort="<?= esc($state['sort']) ?>"
     data-direction="<?= esc($state['direction']) ?>">
    <?php if ($state['total'] === 0): ?>
        <div class="empty-state">
            <div class="empty-state-icon" aria-hidden="true">
                <svg viewBox="0 0 48 48" fill="none">
                    <path d="M10 11.5A4.5 4.5 0 0 1 14.5 7h19a4.5 4.5 0 0 1 4.5 4.5v17a4.5 4.5 0 0 1-4.5 4.5H24l-8.5 7v-7h-1A4.5 4.5 0 0 1 10 28.5v-17Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                    <path d="M18 18h12M18 24h7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
            <p class="empty-state-kicker">Пока тихо</p>
            <h3>Здесь появится первый комментарий</h3>
            <p>Поделитесь своей мыслью — она сразу появится в общей ленте.</p>
        </div>
    <?php else: ?>
        <div class="list-meta" aria-live="polite">
            <span class="list-count-dot" aria-hidden="true"></span>
            Всего записей: <?= esc((string) $state['total']) ?>
        </div>

        <div class="comments-grid">
            <?php foreach ($state['items'] as $comment): ?>
                <article class="comment-card">
                    <div class="comment-card-head">
                        <div class="comment-author">
                            <span class="author-avatar" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M20 21a8 8 0 0 0-16 0M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                            </span>
                            <span class="author-details">
                                <strong class="author-email"><?= esc($comment['name']) ?></strong>
                                <time datetime="<?= esc($comment['date']) ?>"><?= esc($comment['date']) ?></time>
                            </span>
                        </div>
                        <button type="button" class="comment-delete" data-id="<?= esc((string) $comment['id']) ?>"
                                aria-label="Удалить комментарий #<?= esc((string) $comment['id']) ?>" title="Удалить комментарий">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7l1-3h4l1 3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <p class="comment-text"><?= nl2br(esc($comment['text'])) ?></p>
                    <span class="comment-id">#<?= esc((string) $comment['id']) ?></span>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($state['pages'] > 1): ?>
            <nav class="pagination-wrap" aria-label="Страницы комментариев">
                <span class="pagination-label">Страница <?= esc((string) $state['page']) ?> из <?= esc((string) $state['pages']) ?></span>
                <ul class="pagination mb-0">
                    <?php for ($page = 1; $page <= $state['pages']; $page++): ?>
                        <li class="page-item <?= $page === $state['page'] ? 'active' : '' ?>">
                            <a class="page-link comments-page" href="#comments-heading" data-page="<?= esc((string) $page) ?>">
                                <?= esc((string) $page) ?>
                                <?php if ($page === $state['page']): ?><span class="sr-only"> (текущая)</span><?php endif; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
