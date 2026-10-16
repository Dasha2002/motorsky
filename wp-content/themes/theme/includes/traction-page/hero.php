<section class="shero" id="shero">
    <!-- Сетка -->
    <div class="shero__grid"></div>

    <!-- Голубой полукруг — КАРТИНКА сверху по центру -->
    <div class="shero__glow">
        <?php if (motorsky_traction_value(get_the_ID(), 'hero_glow') !== '') : ?><img src="<?php motorsky_traction_url('hero_glow'); ?>" alt=""><?php endif; ?>
    </div>

    <div class="shero__inner">

        <div class="shero__media">
            <?php if (motorsky_traction_value(get_the_ID(), 'hero_image') !== '') : ?><img src="<?php motorsky_traction_url('hero_image'); ?>" alt="<?php motorsky_traction_attr('hero_alt'); ?>"><?php endif; ?>
        </div>

        <div class="shero__copy">
            <div>
                <h1 class="shero__title"><span class="blue-heading"><?php motorsky_traction_text('hero_accent'); ?></span> <?php motorsky_traction_text('hero_title'); ?></h1>
                <div class="shero__cta">
                    <button class="btn-ms" data-popup="contact"><?php motorsky_traction_text('hero_button'); ?></button>
                </div>
            </div>

            <p class="shero__desc"><?php motorsky_traction_text('hero_description'); ?></p>

        </div>


    </div>
</section>