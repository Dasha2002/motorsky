<section class="papp">
    <div class="container">
        <h2 class="papp__head"><?php motorsky_traction_text('application_title'); ?></h2>

        <div class="papp__grid">
            <div class="papp__card">
                <div class="papp__card-icon">
                    <?php if (motorsky_traction_value(get_the_ID(), 'application_1_image') !== '') : ?><img src="<?php motorsky_traction_url('application_1_image'); ?>" alt="<?php motorsky_traction_attr('application_1_alt'); ?>"><?php endif; ?>
                </div>
                <h3 class="papp__card-title"><?php motorsky_traction_text('application_1_title'); ?></h3>
            </div>

            <div class="papp__card">
                <div class="papp__card-icon">
                    <?php if (motorsky_traction_value(get_the_ID(), 'application_2_image') !== '') : ?><img src="<?php motorsky_traction_url('application_2_image'); ?>" alt="<?php motorsky_traction_attr('application_2_alt'); ?>"><?php endif; ?>
                </div>
                <h3 class="papp__card-title"><?php motorsky_traction_text('application_2_title'); ?></h3>
            </div>

            <div class="papp__card">
                <div class="papp__card-icon">
                    <?php if (motorsky_traction_value(get_the_ID(), 'application_3_image') !== '') : ?><img src="<?php motorsky_traction_url('application_3_image'); ?>" alt="<?php motorsky_traction_attr('application_3_alt'); ?>"><?php endif; ?>
                </div>
                <h3 class="papp__card-title"><?php motorsky_traction_text('application_3_title'); ?></h3>
            </div>

            <div class="papp__card">
                <div class="papp__card-icon">
                    <?php if (motorsky_traction_value(get_the_ID(), 'application_4_image') !== '') : ?><img src="<?php motorsky_traction_url('application_4_image'); ?>" alt="<?php motorsky_traction_attr('application_4_alt'); ?>"><?php endif; ?>
                </div>
                <h3 class="papp__card-title"><?php motorsky_traction_text('application_4_title'); ?></h3>
            </div>
        </div>
    </div>
</section>