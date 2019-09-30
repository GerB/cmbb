<?php

/**
 *
 * cmBB [Russian]
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Translated By : Ekaterina <http://code.ekatherine.net>
 *
 */
if (!defined('IN_PHPBB')) {
    exit;
}

if (empty($lang) || !is_array($lang)) {
    $lang = [];
}

$lang = array_merge($lang, [
    'ACL_U_CMBB_POST_ARTICLE' => 'Может создавать статьи в cmBB',
    'ARTICLE_HIDDEN_WARNING'  => 'Эта статья скрыта, поэтому в данный момент доступна только модераторам',
    'ARTICLES'                => 'Статьи',
    'ARTICLES_TOTAL'          => 'Всего статей',
    'BACK'                    => 'Назад',
    'CATEGORY'                => 'Категория',
    'CMBB_UPLOAD_BROWSE'      => 'Или выберите',
    'CMBB_UPLOAD_DRAG'        => 'Перетащите файлы сюда',
    'CMBB_UPLOAD_EXPLAIN'     => 'Загрузите файлы, используя блок ниже <br /> Разрешённые типы файлов: ',
    'COMMENTS'                => 'Добавить/посмотреть комментарии',
    'COMMENTS_DISABLED'       => 'Комментарии отключены',
    'CONTENT'                 => 'Содержимое статьи',
    'DELETE_ARTICLE'          => 'Скрыть статью',
    'EDIT_ARTICLE'            => 'Отредактировать статью',
    'ERROR_MUCH_REMOVED'      => 'Вы удалили слишком много данных из статьи. Это похоже на ошибку. Данные НЕ сохранены.',
    'ERROR_TITLE'             => 'Заголовок некорректный',
    'FEATURED_IMG'            => 'Изображение',
    'LOG_ARTICLE_VISIBILLITY' => 'Видимость статьи изменена',
    'NEW_ARTICLE'             => 'Новая статья',
    'NO_HIDDEN'               => 'Не найдено скрытых статей',
    'NO_REACTIONS_ARTICLE'    => 'Отключить комментарии <small>(в настройках категории комментирование уже может быть отключено)</small>',
    'READ_MORE'               => 'Читать далеe...',
    'RESTORE_ARTICLE'         => 'Восстановить статью',
    'SEARCH_USER_ARTICLES'    => 'Поиск по статьям пользователя',
    'SHOW_HIDDEN'             => 'Показать скрытые статьи',
    'TITLE'                   => 'Заголовок',
    'USE_AVATAR'              => '-использовать текущий аватар-',
    'WELCOME_USER'            => 'Здравствуйте, %s!',
]);
