<?php
defined( 'ABSPATH' ) || exit;

function motorsky_setting( $key, $default = '' ) {
    $settings = get_option( 'motorsky_settings', array() );
    return is_array( $settings ) && isset( $settings[ $key ] ) && '' !== $settings[ $key ] ? $settings[ $key ] : $default;
}

function motorsky_settings_menu() {
    add_submenu_page( 'edit.php?post_type=motorsky_product', 'Настройки каталога', 'Настройки каталога', 'manage_options', 'motorsky-catalog-settings', 'motorsky_settings_screen' );
}
add_action( 'admin_menu', 'motorsky_settings_menu' );

function motorsky_sanitize_settings( $input ) {
    $output = array();
    if ( ! is_array( $input ) ) { return $output; }
    foreach ( array( 'recipient', 'metrika_id', 'catalog_title', 'catalog_description' ) as $key ) {
        $value = isset( $input[ $key ] ) && is_string( $input[ $key ] ) ? $input[ $key ] : '';
        if ( 'recipient' === $key ) { $output[ $key ] = is_email( trim( $value ) ) ? sanitize_email( $value ) : ''; }
        elseif ( 'metrika_id' === $key ) { $output[ $key ] = preg_match( '/^\d+$/D', $value ) ? $value : ''; }
        else { $output[ $key ] = sanitize_text_field( $value ); }
    }
    return $output;
}

function motorsky_register_settings() {
    register_setting( 'motorsky_catalog', 'motorsky_settings', array( 'sanitize_callback' => 'motorsky_sanitize_settings', 'type' => 'array' ) );
}
add_action( 'admin_init', 'motorsky_register_settings' );

function motorsky_settings_screen() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    ?>
    <div class="wrap"><h1>Настройки каталога MotorSky</h1>
        <?php settings_errors(); ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'motorsky_catalog' ); ?>
            <table class="form-table"><tbody>
            <?php foreach ( array( 'recipient' => 'Email для заявок (по умолчанию адрес администратора)', 'metrika_id' => 'ID уже установленного счётчика Яндекс Метрики', 'catalog_title' => 'Title каталога', 'catalog_description' => 'Description каталога' ) as $key => $label ) : ?>
                <tr><th><label for="ms-setting-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
                <td><input class="large-text" id="ms-setting-<?php echo esc_attr( $key ); ?>" type="<?php echo 'recipient' === $key ? 'email' : 'text'; ?>" name="motorsky_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( motorsky_setting( $key ) ); ?>"></td></tr>
            <?php endforeach; ?>
            </tbody></table><?php submit_button(); ?>
        </form>
        <p>Скрипты аналитики здесь не устанавливаются. Если GA / gtag или dataLayer уже подключены, события передаются автоматически. Для установленной Метрики укажите её ID выше.</p>
        <p>Укажите страницу политики в «Настройки → Конфиденциальность». Перед запуском проверьте отправку писем через почтовый транспорт хостинга или SMTP.</p>
        <h2>Подготовить модели из чек-листа</h2>
        <p>Создаёт недостающие черновики пяти моделей чек-листа. У 2807–1300, 2810–1500 и 3115–900 заполняет пустые поля характеристик и материалов по старым страницам MotorSky. Ваши заполненные поля, названия и статус публикации сохраняются. У 3115–400 и 3115–1300 пока доступны только название, серия, KV и тип двигателя. Графики, документацию и шестую модель нужно предоставить отдельно.</p>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="motorsky_create_product_drafts">
            <?php wp_nonce_field( 'motorsky_create_product_drafts' ); submit_button( 'Создать / дополнить пять товаров', 'secondary' ); ?>
        </form>
    </div>
    <?php
}

function motorsky_lead_types() {
    return array( 'contact' => 'Связаться', 'quote' => 'Получить КП', 'selection' => 'Подобрать двигатель', 'development' => 'Разработать двигатель под ТЗ' );
}

function motorsky_handle_lead() {
    if ( ! check_ajax_referer( 'motorsky_contact', 'motorsky_nonce', false ) ) { wp_send_json_error( array( 'message' => 'Обновите страницу и попробуйте ещё раз.' ), 403 ); }
    $fields = array();
    $keys = array( 'Name', 'Email', 'Phone', 'Company', 'Comment', 'website', 'FormType', 'ModelId', 'Consent', 'Platform', 'Power', 'Voltage', 'KV', 'Thrust', 'FileLink' );
    foreach ( $keys as $key ) {
        if ( isset( $_POST[ $key ] ) && ( ! is_string( $_POST[ $key ] ) || strlen( $_POST[ $key ] ) > ( 'Comment' === $key ? 10000 : 2000 ) ) ) {
            wp_send_json_error( array( 'message' => 'Некорректные данные формы.' ), 400 );
        }
        $fields[ $key ] = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
    }
    $type = '' !== $fields['FormType'] ? $fields['FormType'] : 'contact';
    $types = motorsky_lead_types();
    if ( ! isset( $types[ $type ] ) || '' !== $fields['website'] || '1' !== $fields['Consent'] ) {
        wp_send_json_error( array( 'message' => 'Проверьте форму и согласие на обработку персональных данных.' ), 400 );
    }
    $name = sanitize_text_field( $fields['Name'] );
    $email = sanitize_email( $fields['Email'] );
    $phone = preg_replace( '/\D/', '', $fields['Phone'] );
    if ( preg_match( '/^\s*\+7[\s()\-]*$/D', $fields['Phone'] ) ) { $phone = ''; }
    elseif ( 11 === strlen( $phone ) && in_array( $phone[0], array( '7', '8' ), true ) ) { $phone = substr( $phone, 1 ); }
    $company = sanitize_text_field( $fields['Company'] );
    if ( '' === $name || ( '' === $email && '' === $phone )
        || ( '' !== $fields['Email'] && ! is_email( $fields['Email'] ) )
        || ( '' !== $phone && 10 !== strlen( $phone ) )
        || ( in_array( $type, array( 'quote', 'development' ), true ) && '' === $company )
        || ( 'development' === $type && '' === trim( $fields['Comment'] ) ) ) {
        wp_send_json_error( array( 'message' => 'Укажите имя, телефон или корректный email и заполните обязательные поля.' ), 400 );
    }
    $model = '';
    if ( 'quote' === $type ) {
        $product = get_post( absint( $fields['ModelId'] ) );
        if ( ! $product || 'motorsky_product' !== $product->post_type || 'publish' !== $product->post_status ) {
            wp_send_json_error( array( 'message' => 'Выберите двигатель в каталоге.' ), 400 );
        }
        $model = get_the_title( $product );
    }
    $link = esc_url_raw( $fields['FileLink'], array( 'https', 'http' ) );
    if ( '' !== $fields['FileLink'] && ( '' === $link || ! wp_http_validate_url( $link ) ) ) {
        wp_send_json_error( array( 'message' => 'Укажите корректную http(s)-ссылку на файл ТЗ.' ), 400 );
    }
    $rate_key = 'motorsky_contact_' . hash( 'sha256', isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown' );
    if ( get_transient( $rate_key ) ) { wp_send_json_error( array( 'message' => 'Подождите минуту перед повторной отправкой.' ), 429 ); }
    set_transient( $rate_key, 1, MINUTE_IN_SECONDS );
    $message = $types[ $type ] . "\nМодель: {$model}\nИмя: {$name}\nКомпания: {$company}\nEmail: {$email}\nТелефон: " . ( '' !== $phone ? '+7' . $phone : '' ) . "\n";
    if ( 'selection' === $type ) {
        foreach ( array( 'Platform' => 'Платформа', 'Power' => 'Мощность', 'Voltage' => 'Напряжение', 'KV' => 'KV / обороты', 'Thrust' => 'Тяга' ) as $key => $label ) {
            $message .= $label . ': ' . sanitize_text_field( $fields[ $key ] ) . "\n";
        }
    }
    if ( 'development' === $type ) { $message .= 'Ссылка на ТЗ: ' . $link . "\n"; }
    $message .= "Согласие на обработку данных: да\n\n" . sanitize_textarea_field( $fields['Comment'] );
    $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
    if ( '' !== $email ) { $headers[] = 'Reply-To: ' . $email; }
    $recipient = motorsky_setting( 'recipient', get_option( 'admin_email' ) );
    if ( ! wp_mail( $recipient, 'MotorSky — ' . $types[ $type ] . ( '' !== $model ? ' — ' . $model : '' ), $message, $headers ) ) {
        delete_transient( $rate_key );
        wp_send_json_error( array( 'message' => 'Не удалось отправить письмо. Попробуйте позже или свяжитесь с нами по телефону.' ), 500 );
    }
    wp_send_json_success();
}

/** Refresh cached-page nonces just before submission. No personal data is returned. */
function motorsky_lead_nonce() { nocache_headers(); wp_send_json_success( array( 'nonce' => wp_create_nonce( 'motorsky_contact' ) ) ); }
add_action( 'wp_ajax_motorsky_lead_nonce', 'motorsky_lead_nonce' );
add_action( 'wp_ajax_nopriv_motorsky_lead_nonce', 'motorsky_lead_nonce' );
