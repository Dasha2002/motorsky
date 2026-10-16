
<section class="servo-info" aria-labelledby="servo-info-title">
  <div class="servo-info__container">
    <div class="servo-info__overview">
      <h2 class="servo-info__title" id="servo-info-title"><?php motorsky_traction_text('overview_title'); ?></h2>

      <div class="servo-info__features">
        <div class="servo-info__feature">
          <span class="servo-info__accent" aria-hidden="true"></span>
          <p><?php motorsky_traction_text('feature_1'); ?></p>
        </div>

        <div class="servo-info__feature">
          <span class="servo-info__accent" aria-hidden="true"></span>
          <p><?php motorsky_traction_text('feature_2'); ?></p>
        </div>

        <div class="servo-info__feature">
          <span class="servo-info__accent" aria-hidden="true"></span>
          <p><?php motorsky_traction_text('feature_3'); ?></p>
        </div>

        <div class="servo-info__feature">
          <span class="servo-info__accent" aria-hidden="true"></span>
          <p><?php motorsky_traction_text('feature_4'); ?></p>
        </div>

        <div class="servo-info__feature">
          <span class="servo-info__accent" aria-hidden="true"></span>
          <p><?php motorsky_traction_text('feature_5'); ?></p>
        </div>

        <div class="servo-info__feature">
          <span class="servo-info__accent" aria-hidden="true"></span>
          <p><?php motorsky_traction_text('feature_6'); ?></p>
        </div>
      </div>
    </div>

    <div class="servo-types" aria-label="<?php motorsky_traction_attr('types_label'); ?>">
      <article class="servo-types__card">
        <?php if (motorsky_traction_value(get_the_ID(), 'type_1_image') !== '') : ?><img class="servo-types__image" src="<?php motorsky_traction_url('type_1_image'); ?>" alt="<?php motorsky_traction_attr('type_1_alt'); ?>" loading="lazy"><?php endif; ?>
        <div class="servo-types__content">
          <h3 class="servo-types__title"><?php motorsky_traction_text('type_1_title'); ?></h3>
          <p class="servo-types__description"><?php motorsky_traction_text('type_1_description'); ?></p>
        </div>
      </article>

      <article class="servo-types__card">
        <?php if (motorsky_traction_value(get_the_ID(), 'type_2_image') !== '') : ?><img class="servo-types__image" src="<?php motorsky_traction_url('type_2_image'); ?>" alt="<?php motorsky_traction_attr('type_2_alt'); ?>" loading="lazy"><?php endif; ?>
        <div class="servo-types__content">
          <h3 class="servo-types__title"><?php motorsky_traction_text('type_2_title'); ?></h3>
          <p class="servo-types__description"><?php motorsky_traction_text('type_2_description'); ?></p>
        </div>
      </article>

      <article class="servo-types__card">
        <?php if (motorsky_traction_value(get_the_ID(), 'type_3_image') !== '') : ?><img class="servo-types__image" src="<?php motorsky_traction_url('type_3_image'); ?>" alt="<?php motorsky_traction_attr('type_3_alt'); ?>" loading="lazy"><?php endif; ?>
        <div class="servo-types__content">
          <h3 class="servo-types__title"><?php motorsky_traction_text('type_3_title'); ?></h3>
          <p class="servo-types__description"><?php motorsky_traction_text('type_3_description'); ?></p>
        </div>
      </article>
    </div>

    <div class="servo-info__advantages">
      <h2 class="servo-info__title"><?php motorsky_traction_text('advantages_title'); ?></h2>

      <div class="servo-info__advantages-grid">
        <div class="servo-info__feature">
          <span class="servo-info__accent" aria-hidden="true"></span>
          <p><?php motorsky_traction_text('advantage_1'); ?></p>
        </div>

        <div class="servo-info__feature">
          <span class="servo-info__accent" aria-hidden="true"></span>
          <p><?php motorsky_traction_text('advantage_2'); ?></p>
        </div>

        <div class="servo-info__feature">
          <span class="servo-info__accent" aria-hidden="true"></span>
          <p><?php motorsky_traction_text('advantage_3'); ?></p>
        </div>

        <div class="servo-info__feature">
          <span class="servo-info__accent" aria-hidden="true"></span>
          <p><?php motorsky_traction_text('advantage_4'); ?></p>
        </div>
      </div>

      <p class="servo-info__description"><?php motorsky_traction_text('summary'); ?></p>
    </div>
  </div>
</section>
