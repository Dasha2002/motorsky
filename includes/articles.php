<?php
defined( 'ABSPATH' ) || exit;

/** Use native posts so existing articles, links, revisions and search keep working. */
function motorsky_customize_articles() {
    $type = get_post_type_object( 'post' );
    if ( ! $type ) { return; }
    $labels = array(
        'name' => 'Статьи', 'singular_name' => 'Статья', 'menu_name' => 'Статьи',
        'name_admin_bar' => 'Статью', 'all_items' => 'Все статьи',
        'add_new' => 'Добавить статью', 'add_new_item' => 'Добавить статью',
        'edit_item' => 'Редактировать статью', 'new_item' => 'Новая статья',
        'view_item' => 'Посмотреть статью', 'view_items' => 'Посмотреть статьи',
        'search_items' => 'Найти статью', 'not_found' => 'Статьи не найдены',
        'not_found_in_trash' => 'В корзине статей нет',
        'featured_image' => 'Обложка статьи', 'set_featured_image' => 'Выбрать обложку статьи',
        'remove_featured_image' => 'Удалить обложку статьи', 'use_featured_image' => 'Использовать как обложку',
        'archives' => 'Архив статей', 'insert_into_item' => 'Вставить в статью',
        'uploaded_to_this_item' => 'Загружено для этой статьи',
        'items_list' => 'Список статей', 'items_list_navigation' => 'Навигация по статьям',
        'filter_items_list' => 'Фильтровать статьи',
        'item_published' => 'Статья опубликована.', 'item_updated' => 'Статья обновлена.',
        'item_reverted_to_draft' => 'Статья переведена в черновик.',
        'item_scheduled' => 'Публикация статьи запланирована.',
    );
    foreach ( $labels as $key => $value ) { $type->labels->$key = $value; }
    $type->label = 'Статьи';
    add_post_type_support( 'post', array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ) );
}
add_action( 'init', 'motorsky_customize_articles', 20 );

function motorsky_article_help_box() {
    add_meta_box( 'motorsky-article-help', 'Статья на сайте MotorSky', 'motorsky_article_help', 'post', 'side', 'high' );
}
add_action( 'add_meta_boxes_post', 'motorsky_article_help_box' );

function motorsky_article_help() {
    ?>
    <p><strong>Название</strong> — заголовок статьи и её карточки на главной.</p>
    <p><strong>Обложка статьи</strong> — выберите изображение из медиатеки. Оно появится в карточке и над текстом статьи.</p>
    <p><strong>Отрывок</strong> — отдельное описание для карточки на главной. Заполните его в панели настроек статьи. Если оставить пустым, описание будет взято из начала текста. Заголовок показывается целиком; длинное описание сокращается на сайте. Полный текст статьи сохраняется.</p>
    <p><strong>Основной редактор</strong> — полный текст: абзацы, подзаголовки, списки, цитаты, ссылки и изображения.</p>
    <p>Нажмите <strong>«Опубликовать»</strong>: статья автоматически появится в секции <strong>«Новости»</strong> на главной. Показываются шесть последних опубликованных статей, новые идут первыми. Черновики, закрытые и защищённые паролем статьи на главной не показываются.</p>
    <?php
}

function motorsky_home_articles_query() {
    return array(
        'post_type' => 'post', 'post_status' => 'publish', 'has_password' => false,
        'posts_per_page' => 6, 'ignore_sticky_posts' => true, 'no_found_rows' => true,
        'orderby' => array( 'date' => 'DESC', 'ID' => 'DESC' ),
    );
}
