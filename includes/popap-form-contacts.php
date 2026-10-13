<div class="popup-overlay" data-popup-id="contact">
    <div class="popup-overlay__bg"></div>
    

    <div class="popup-card">
<div class="hero__grid-popap"></div>
        <div class="hero__glow popap-hero__glow">
            <img src="assets/img/hero_gradient.svg" alt="">
        </div>
        <button class="popup-close" aria-label="Закрыть">
            <svg width="23" height="23" viewBox="0 0 23 23" fill="none">
                <rect x="10.31" y="-3.69" width="2" height="30" transform="translate(11.31 11.31) rotate(-45) translate(-11.31 -11.31)" fill="#ffffff" />
                <rect x="10.31" y="-3.69" width="2" height="30" transform="translate(11.31 11.31) rotate(-315) translate(-11.31 -11.31)" fill="#ffffff" />
            </svg>
        </button>

        <img class="popup-icon" src="assets/img/hero-logo.svg" alt="">

        <form class="popup-form" novalidate>
            <div class="popup-form__field">
                <input type="text" name="Name" placeholder="Имя" required>
            </div>

            <div class="popup-form__field">
                <input type="email" name="Email" placeholder="Email" required>
            </div>

            <div class="popup-form__field popup-form__field--phone">
                <div class="t-input-phonemask__select"><span class="t-input-phonemask__select-flag" data-phonemask-flag="ru"></span><span class="t-input-phonemask__select-triangle"></span><span class="t-input-phonemask__select-code" style="font-weight: 400;">+7</span></div>
                <input type="tel" name="Phone" placeholder="(000) 000-00-00" maxlength="15" required>
            </div>

            <div class="popup-form__field">
                <textarea name="Comment" placeholder="Комментарий" rows="2"></textarea>
            </div>

            <button type="submit" class="popup-form__submit">Отправить</button>
        </form>

        <div class="popup-success is-hidden">
            <div class="popup-success__icon">
                <svg viewBox="0 0 24 24" width="28" height="28" fill="none">
                    <path d="M5 12l5 5L20 7" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <h3>Спасибо!</h3>
            <p>Ваше сообщение отправлено. Мы свяжемся с Вами в ближайшее время.</p>
        </div>
    </div>
</div>