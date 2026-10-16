<?php $motorsky_search_id = motorsky_search_input_id(); ?>
<form role="search" method="get" class="motorsky-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label class="screen-reader-text" for="<?php echo esc_attr( $motorsky_search_id ); ?>">Поиск по сайту</label>
    <input id="<?php echo esc_attr( $motorsky_search_id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Название, серия или KV…" maxlength="200" required>
    <button type="submit" class="btn-ms">Найти</button>
</form>
