<?php foreach ( motorsky_lead_types() as $type => $title ) : $popup_id = 'contact' === $type ? 'contact' : $type; ?>
<div class="popup-overlay" data-popup-id="<?php echo esc_attr( $popup_id ); ?>" aria-hidden="true">
    <div class="popup-overlay__bg"></div>
    <div class="popup-card ms-lead-card" role="dialog" aria-modal="true" aria-labelledby="ms-form-title-<?php echo esc_attr( $type ); ?>">
        <div class="hero__grid-popap" aria-hidden="true"></div>
        <div class="hero__glow popap-hero__glow" aria-hidden="true">
            <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero_gradient.svg' ) ); ?>" alt="">
        </div>
        <button class="popup-close" type="button" aria-label="Закрыть">✕</button>
        <img class="popup-icon" src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero-logo.svg' ) ); ?>" alt="MotorSky">
        <h2 id="ms-form-title-<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $title ); ?></h2>
        <?php if ( 'selection' === $type ) : ?><p>Оставьте параметры — инженер свяжется с вами и поможет подобрать двигатель.</p><?php endif; ?>
        <form class="popup-form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
            <input type="hidden" name="action" value="motorsky_contact">
            <input type="hidden" name="FormType" value="<?php echo esc_attr( $type ); ?>">
            <input type="hidden" name="ModelId" value="">
            <input type="hidden" name="Model" value="">
            <input type="hidden" name="motorsky_nonce" value="<?php echo esc_attr( wp_create_nonce( 'motorsky_contact' ) ); ?>">
            <div class="motorsky-honeypot" aria-hidden="true"><label>Сайт<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <p class="ms-selected-model" data-selected-model></p>
            <p class="popup-form__error" role="alert" hidden></p>
            <div class="popup-form__field"><label>Имя *<input type="text" name="Name" autocomplete="name" maxlength="150" required></label></div>
            <div class="popup-form__field"><label>Компания<?php echo in_array( $type, array( 'quote', 'development' ), true ) ? ' *' : ''; ?><input type="text" name="Company" autocomplete="organization" maxlength="200" <?php echo in_array( $type, array( 'quote', 'development' ), true ) ? 'required' : ''; ?>></label></div>
            <p>Укажите телефон или email для ответа.</p>
            <div class="popup-form__field"><label>Email<input type="email" name="Email" autocomplete="email" maxlength="254"></label></div>
            <div class="popup-form__field popup-form__field--phone"><label>Телефон<input type="tel" name="Phone" placeholder="+7 (000) 000-00-00" autocomplete="tel" inputmode="tel" maxlength="18"></label></div>
            <?php if ( 'selection' === $type ) : foreach ( array( 'Platform' => 'Тип платформы', 'Power' => 'Требуемая мощность, Вт', 'Voltage' => 'Напряжение, В / аккумулятор, S', 'KV' => 'KV / обороты (если известны)', 'Thrust' => 'Требуемая тяга, г' ) as $key => $label ) : ?>
                <div class="popup-form__field"><label><?php echo esc_html( $label ); ?><input type="text" name="<?php echo esc_attr( $key ); ?>" maxlength="200"></label></div>
            <?php endforeach; endif; ?>
            <div class="popup-form__field"><label><?php echo 'development' === $type ? 'Описание задачи / требований *' : 'Комментарий'; ?><textarea name="Comment" maxlength="10000" rows="3" <?php echo 'development' === $type ? 'required' : ''; ?>></textarea></label></div>
            <?php if ( 'development' === $type ) : ?>
                <div class="popup-form__field"><label>Ссылка на файл ТЗ (необязательно)<input type="url" name="FileLink" placeholder="https://…" maxlength="2000"></label></div>
            <?php endif; ?>
            <label class="ms-consent"><input type="checkbox" name="Consent" value="1" required><span>Согласен на обработку персональных данных<?php $policy = get_privacy_policy_url(); if ( $policy ) : ?> в соответствии с <a href="<?php echo esc_url( $policy ); ?>" target="_blank" rel="noopener">политикой конфиденциальности</a><?php endif; ?>.</span></label>
            <button type="submit" class="popup-form__submit">Отправить заявку</button>
        </form>
        <div class="popup-success is-hidden" role="status"><h3>Спасибо!</h3><p>Ваша заявка отправлена. Мы свяжемся с вами в ближайшее время.</p></div>
    </div>
</div>
<?php endforeach; ?>
