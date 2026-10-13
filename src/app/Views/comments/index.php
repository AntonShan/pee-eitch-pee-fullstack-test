<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= esc($csrfHash) ?>">
    <title>Комментарии</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/comments.css') ?>">
</head>
<body
    data-comments-url="<?= esc(site_url('comments')) ?>"
    data-create-url="<?= esc(site_url('comments')) ?>"
    data-csrf-name="<?= esc($csrfName) ?>"
>
<main class="page-shell">
    <div class="comments-container">
        <header class="hero-panel">
            <div class="hero-copy">
                <p class="eyebrow"><span class="eyebrow-dot" aria-hidden="true"></span>Открытое обсуждение</p>
                <h1>Комментарии,<br><span>которые двигают разговор.</span></h1>
                <p class="hero-description">Оставьте короткую мысль, идею или вопрос — всё важное останется в одном живом списке.</p>
            </div>
            <div class="hero-mark" aria-hidden="true">
                <svg viewBox="0 0 160 160" fill="none">
                    <circle cx="80" cy="80" r="64" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 8" />
                    <path d="M47 71.5C47 60.73 55.73 52 66.5 52h27C104.27 52 113 60.73 113 71.5v17C113 99.27 104.27 108 93.5 108H78l-17 12v-12h-1.5C48.73 108 47 99.27 47 88.5v-17Z" stroke="currentColor" stroke-width="2.5" />
                    <path d="M64 76h32M64 87h21" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </div>
        </header>

        <div id="notification" class="notification d-none" role="status" aria-live="polite"></div>

        <section aria-labelledby="comments-heading" class="comments-section">
            <div class="section-heading">
                <div>
                    <p class="section-kicker">Лента сообщества</p>
                    <h2 id="comments-heading">Все комментарии</h2>
                </div>
                <div class="sorting-controls" id="sorting-controls">
                    <div class="control-field">
                        <label for="sort-field">Сортировать по</label>
                        <select id="sort-field" class="control-input">
                            <option value="id" <?= $state['sort'] === 'id' ? 'selected' : '' ?>>ID записи</option>
                            <option value="date" <?= $state['sort'] === 'date' ? 'selected' : '' ?>>Дате</option>
                        </select>
                    </div>
                    <div class="control-field">
                        <label for="sort-direction">Направление</label>
                        <select id="sort-direction" class="control-input">
                            <option value="desc" <?= $state['direction'] === 'desc' ? 'selected' : '' ?>>Сначала новые</option>
                            <option value="asc" <?= $state['direction'] === 'asc' ? 'selected' : '' ?>>Сначала старые</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="comments-region" aria-busy="false">
                <?= view('comments/_list', ['state' => $state]) ?>
            </div>
        </section>

        <section aria-labelledby="add-comment-heading" class="composer-panel">
            <div class="composer-heading">
                <div class="composer-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M19 3H5a2 2 0 0 0-2 2v14l4-3h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                        <path d="M8 9h8M8 13h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                </div>
                <div>
                    <p class="section-kicker">Ваш голос важен</p>
                    <h2 id="add-comment-heading">Добавить комментарий</h2>
                </div>
            </div>

            <form id="comment-form" class="comment-form" novalidate>
                <input type="hidden" name="<?= esc($csrfName) ?>" value="<?= esc($csrfHash) ?>">

                <div class="form-fields">
                    <div class="field-group">
                        <label for="comment-name">Email</label>
                        <input type="email" class="form-input" id="comment-name" name="name"
                               maxlength="254" autocomplete="email" placeholder="you@example.com" required>
                        <div class="invalid-feedback" data-for="name"></div>
                    </div>

                    <div class="field-group">
                        <label for="comment-date">Дата публикации</label>
                        <input type="date" class="form-input" id="comment-date" name="date" required>
                        <div class="invalid-feedback" data-for="date"></div>
                    </div>

                    <div class="field-group field-group-wide">
                        <label for="comment-text">Ваш комментарий</label>
                        <textarea class="form-input form-textarea" id="comment-text" name="text" rows="5"
                                  maxlength="5000" placeholder="Поделитесь своей мыслью..." required></textarea>
                        <div class="invalid-feedback" data-for="text"></div>
                    </div>
                </div>

                <div class="form-actions">
                    <p class="form-hint">До 5000 символов. Пожалуйста, оставайтесь уважительными.</p>
                    <button type="submit" class="submit-button" id="comment-submit">
                        <span class="spinner-border form-spinner d-none" role="status" aria-hidden="true"></span>
                        <span>Опубликовать комментарий</span>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m5 12 14-7-4 14-3.5-5.5L5 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                            <path d="m11.5 13.5 7.5-8.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
            </form>
        </section>

        <footer class="page-footer">Комментарии обновляются мгновенно без перезагрузки страницы</footer>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= base_url('assets/js/comments.js') ?>"></script>
</body>
</html>
