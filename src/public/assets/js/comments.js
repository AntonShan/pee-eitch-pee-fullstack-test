(function ($) {
    'use strict';

    var $body = $('body');
    var $region = $('#comments-region');
    var $form = $('#comment-form');
    var $notification = $('#notification');
    var csrfName = $body.attr('data-csrf-name');
    var commentsUrl = $body.attr('data-comments-url');
    var createUrl = $body.attr('data-create-url');

    function currentState() {
        var $list = $('#comments-list');

        return {
            page: Number($list.attr('data-page')) || 1,
            sort: $list.attr('data-sort') || 'id',
            direction: $list.attr('data-direction') || 'desc'
        };
    }

    function showNotification(message, type) {
        $notification
            .removeClass('d-none alert-success alert-danger alert-info')
            .addClass('alert-' + type)
            .text(message);
    }

    function updateCsrf(hash) {
        if (!hash) {
            return;
        }

        $('meta[name="csrf-token"]').attr('content', hash);
        $form.find('input[name="' + csrfName + '"]')
            .val(hash)
            .prop('defaultValue', hash);
    }

    function loadComments(state, updateUrl) {
        $region.addClass('is-loading').attr('aria-busy', 'true');

        return $.get(commentsUrl, state)
            .done(function (html) {
                $region.html(html);

                if (updateUrl) {
                    var url = new URL(window.location.href);
                    url.searchParams.set('page', state.page);
                    url.searchParams.set('sort', state.sort);
                    url.searchParams.set('direction', state.direction);
                    window.history.replaceState({}, '', url.toString());
                }
            })
            .fail(function () {
                showNotification('Не удалось загрузить комментарии. Попробуйте ещё раз.', 'danger');
            })
            .always(function () {
                $region.removeClass('is-loading').attr('aria-busy', 'false');
            });
    }

    function clearValidation() {
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');
    }

    function renderValidation(errors) {
        $.each(errors || {}, function (field, message) {
            var $field = $form.find('[name="' + field + '"]');
            $field.addClass('is-invalid');
            $form.find('.invalid-feedback[data-for="' + field + '"]').text(message);
        });
    }

    function setFormBusy(isBusy) {
        $('#comment-submit').prop('disabled', isBusy);
        $('#comment-submit .spinner-border').toggleClass('d-none', !isBusy);
    }

    function validateEmail() {
        var $email = $('#comment-name');
        var value = $.trim($email.val());
        var valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

        if (!valid) {
            $email.addClass('is-invalid');
            $form.find('[data-for="name"]').text('Укажите корректный email.');
        } else {
            $email.removeClass('is-invalid');
            $form.find('[data-for="name"]').text('');
        }

        return valid;
    }

    $('#sort-field, #sort-direction').on('change', function () {
        var state = currentState();
        state.page = 1;
        state.sort = $('#sort-field').val();
        state.direction = $('#sort-direction').val();
        loadComments(state, true);
    });

    $(document).on('click', '.comments-page', function (event) {
        event.preventDefault();
        var state = currentState();
        state.page = Number($(this).data('page')) || 1;
        loadComments(state, true);
    });

    $(document).on('click', '.comment-delete', function () {
        var $button = $(this);
        var id = $button.data('id');

        if (!window.confirm('Удалить этот комментарий?')) {
            return;
        }

        $button.prop('disabled', true);
        $.ajax({
            url: createUrl + '/' + id,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        }).done(function (response) {
            updateCsrf(response.csrfHash);
            showNotification(response.message, 'success');
            loadComments(currentState(), false);
        }).fail(function (xhr) {
            var response = xhr.responseJSON || {};
            updateCsrf(response.csrfHash);
            showNotification(response.message || 'Не удалось удалить комментарий.', 'danger');
            $button.prop('disabled', false);
        });
    });

    $('#comment-name').on('input blur', validateEmail);

    $('#comment-text').on('keydown', function (event) {
        var isEnter = event.key === 'Enter' || event.which === 13;

        if (isEnter && (event.ctrlKey || event.metaKey)) {
            event.preventDefault();
            $form.trigger('submit');
        }
    });

    $form.on('submit', function (event) {
        event.preventDefault();

        if ($('#comment-submit').prop('disabled')) {
            return;
        }

        clearValidation();

        var emailValid = validateEmail();
        if (!this.checkValidity() || !emailValid) {
            $(this).find(':invalid').first().trigger('focus');
            return;
        }

        setFormBusy(true);
        $.ajax({
            url: createUrl,
            type: 'POST',
            data: $form.serialize()
        }).done(function (response) {
            updateCsrf(response.csrfHash);
            $form[0].reset();
            clearValidation();
            showNotification(response.message, 'success');
            $('#sort-field').val('id');
            $('#sort-direction').val('desc');
            loadComments({ page: 1, sort: 'id', direction: 'desc' }, true);
        }).fail(function (xhr) {
            var response = xhr.responseJSON || {};
            updateCsrf(response.csrfHash);
            renderValidation(response.errors);
            showNotification(response.message || 'Проверьте заполнение формы.', 'danger');
        }).always(function () {
            setFormBusy(false);
        });
    });
}(jQuery));
