<?php defined( 'ABSPATH' ) || exit; $id = get_the_ID(); ?>
<div class="ms-product-content container">
    <section><h2>Испытания</h2><div class="ms-graph-grid">
        <?php foreach ( motorsky_product_fields() as $key => $field ) : if ( 'graph' !== $field[2] ) { continue; } $url = motorsky_product_value( $id, $key ); ?>
            <figure><figcaption><?php echo esc_html( $field[0] ); ?></figcaption>
                <?php if ( $url ) : ?><a href="<?php echo esc_url( $url ); ?>" data-ms-zoom aria-label="<?php echo esc_attr( 'Увеличить: ' . $field[0] ); ?>"><img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( get_the_title() . ' — ' . $field[0] ); ?>" loading="lazy"><span>Увеличить график ↗</span></a>
                <?php else : ?><p class="ms-media-pending">График готовится</p><?php endif; ?>
            </figure>
        <?php endforeach; ?>
    </div></section>
    <section><h2>Техническая документация</h2><ul class="ms-document-list">
        <?php foreach ( motorsky_product_fields() as $key => $field ) : if ( 'document' !== $field[2] ) { continue; } $url = motorsky_product_value( $id, $key ); ?>
            <li><?php if ( $url ) : ?><a href="<?php echo esc_url( $url ); ?>"<?php echo 'doc_step' === $key ? ' download' : ''; ?> target="_blank" rel="noopener noreferrer" data-ms-event="document_download" data-document="<?php echo esc_attr( $key ); ?>" data-product-id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( get_the_title() . ' — ' . $field[0] ); ?> <?php echo 'doc_step' === $key ? '↓' : '↗'; ?></a>
            <?php else : ?><span><?php echo esc_html( get_the_title() . ' — ' . $field[0] ); ?>: файл готовится</span><?php endif; ?></li>
        <?php endforeach; ?>
    </ul></section>
</div>
