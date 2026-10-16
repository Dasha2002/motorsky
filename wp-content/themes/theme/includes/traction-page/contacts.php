<section class="contacts" id="contacts">
    <div class="container">
        <div class="contacts__grid">
            <div class="contacts__card">
                <div>
                    <h2><?php motorsky_traction_text('contact_title'); ?></h2>
                    <p><?php motorsky_traction_text('contact_address'); ?></p>
                </div>
            </div>
            <div class="contacts__map">
                <?php if (motorsky_traction_value(get_the_ID(), 'contact_map') !== '') : ?><iframe
                    src="<?php motorsky_traction_url('contact_map'); ?>"
                    title="<?php motorsky_traction_attr('contact_map_title'); ?>"
                    loading="lazy"
                    allowfullscreen></iframe><?php endif; ?>
            </div>
        </div>
    </div>
</section>