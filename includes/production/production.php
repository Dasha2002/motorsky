<?php $ms_previous_bg = '#000000'; for ( $ms_block = 1; $ms_block <= 6; $ms_block++ ) :
    $ms_prefix = 'block_' . $ms_block . '_';
    $ms_bg = motorsky_production_value( $ms_prefix . 'background' );
    $ms_title = motorsky_production_value( $ms_prefix . 'title' );
?>
<div class="ms-production-wrap" style="background-color:<?php echo esc_attr( $ms_previous_bg ); ?>">
<section class="production ms-production-section" style="background-color:<?php echo esc_attr( $ms_bg ); ?>" aria-labelledby="production-title-<?php echo $ms_block; ?>">
  <div class="container">
    <h2 class="prod-card__title" id="production-title-<?php echo $ms_block; ?>"><?php echo nl2br( esc_html( $ms_title ), false ); ?></h2>
    <article class="prod-card production-card">
      <div class="prod-card__left"><div class="prod-card__photos" data-production-gallery>
        <?php $ms_image = motorsky_production_value( $ms_prefix . 'image_1' ); if ( $ms_image ) : ?>
        <a class="prod-card__photo-main" data-production-photo data-fancybox="production-<?php echo $ms_block; ?>" data-caption="<?php echo esc_attr( esc_html( $ms_title ) ); ?>" href="<?php echo esc_url( $ms_image ); ?>" aria-label="<?php echo esc_attr( 'Увеличить: ' . $ms_title . ' — фото 1' ); ?>"><img src="<?php echo esc_url( $ms_image ); ?>" alt="<?php echo esc_attr( $ms_title ); ?>" loading="lazy"></a>
        <?php endif; ?>
        <?php if ( motorsky_production_value( $ms_prefix . 'image_2' ) || motorsky_production_value( $ms_prefix . 'image_3' ) ) : ?>
        <div class="prod-card__photo-thumbs">
          <?php for ( $ms_photo = 2; $ms_photo <= 3; $ms_photo++ ) : $ms_image = motorsky_production_value( $ms_prefix . 'image_' . $ms_photo ); if ( ! $ms_image ) { continue; } ?>
          <a class="prod-card__photo-thumb" data-production-photo data-fancybox="production-<?php echo $ms_block; ?>" data-caption="<?php echo esc_attr( esc_html( $ms_title ) ); ?>" href="<?php echo esc_url( $ms_image ); ?>" aria-label="<?php echo esc_attr( 'Увеличить: ' . $ms_title . ' — фото ' . $ms_photo ); ?>"><img src="<?php echo esc_url( $ms_image ); ?>" alt="<?php echo esc_attr( $ms_title . ' — фото ' . $ms_photo ); ?>" loading="lazy"></a>
          <?php endfor; ?>
        </div>
        <?php endif; ?>
      </div></div>
      <div class="prod-card__right">
        <?php $ms_purpose = motorsky_production_value( $ms_prefix . 'purpose' ); $ms_operations = motorsky_production_value( $ms_prefix . 'operations' ); if ( $ms_purpose || $ms_operations ) : ?>
        <div class="prod-card__block">
          <h3 class="prod-card__block-title"><?php echo esc_html( motorsky_production_value( $ms_prefix . 'purpose_label' ) ); ?></h3>
          <p class="prod-card__text"><?php echo nl2br( esc_html( $ms_purpose ), false ); ?></p>
          <?php if ( $ms_operations ) : $ms_operation_lines = preg_split( '/\R/u', $ms_operations ); ?>
          <p class="prod-card__text text-bold"><?php echo esc_html( array_shift( $ms_operation_lines ) ); ?></p>
          <div class="prod-card__text"><?php foreach ( $ms_operation_lines as $ms_line ) { $ms_pair = explode( ' – ', $ms_line, 2 ); echo '<p>' . ( count( $ms_pair ) === 2 ? '<strong>' . esc_html( $ms_pair[0] ) . '</strong> – ' . esc_html( $ms_pair[1] ) : esc_html( $ms_line ) ) . '</p>'; } ?></div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php foreach ( array( 'specs', 'products' ) as $ms_group ) : ?>
        <div class="prod-card__block">
          <h3 class="prod-card__block-title"><?php echo esc_html( motorsky_production_value( $ms_prefix . $ms_group . '_label' ) ); ?></h3>
          <ul class="prod-card__list"><?php motorsky_production_lines( motorsky_production_value( $ms_prefix . $ms_group ) ); ?></ul>
        </div>
        <?php endforeach; ?>
        <?php if ( motorsky_production_value( $ms_prefix . 'capacity' ) ) : ?>
        <div class="prod-card__productivity">
          <span class="prod-card__productivity-label"><?php echo esc_html( motorsky_production_value( $ms_prefix . 'capacity_label' ) ); ?></span>
          <span class="prod-card__productivity-value"><?php echo esc_html( motorsky_production_value( $ms_prefix . 'capacity' ) ); ?></span>
        </div>
        <?php endif; ?>
      </div>
    </article>
  </div>
</section></div>
<?php $ms_previous_bg = $ms_bg; endfor; ?>
