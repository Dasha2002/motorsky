const fs = require('fs');
const read = f => fs.readFileSync(f, 'utf8');
const write = (f, text) => fs.writeFileSync(f, text, 'utf8');
let hero = read('includes/product-page/products-hero.php');
const bullets = [...hero.matchAll(/<li>([^<]+)<\/li>/g)].map(m => m[1]);
const phpArray = values => "array( " + values.map(value => "'" + value.replace(/'/g,"\\'") + "'").join(', ') + ' )';
hero = `<?php
$ms_id = get_the_ID();
$ms_images = motorsky_layout_images( $ms_id, 'gallery', array( 'e1-1.png', 'e1-2.png', 'e1-3.png', 'e1-4.png' ), true );
$ms_bullets = motorsky_layout_lines( $ms_id, 'hero_bullets', motorsky_layout_lines( $ms_id, 'features', motorsky_original_2807( $ms_id ) ? ${phpArray(bullets)} : array() ) );
?>\n` + hero;
hero = hero.replace(/<div class="swiper-wrapper">[\s\S]*?<\/div>\s*<!-- Навигация -->/, `<div class="swiper-wrapper">
                <?php foreach ( $ms_images as $ms_index => $ms_url ) : ?>
                <div class="swiper-slide">
                    <img src="<?php echo esc_url( $ms_url ); ?>" alt="<?php echo esc_attr( get_the_title() . ' — изображение ' . ( $ms_index + 1 ) ); ?>">
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Навигация -->`);
hero = hero.replace('STAR 2807—1300 KV</h1>', '<?php the_title(); ?></h1>');
hero = hero.replace('data-popup="contact"', `data-popup="quote" data-ms-event="quote_open" data-product-id="<?php echo esc_attr( $ms_id ); ?>" data-product-name="<?php echo esc_attr( get_the_title() ); ?>"`);
hero = hero.replace(/<ul class="hero-product__bullets">[\s\S]*?<\/ul>/, `<ul class="hero-product__bullets">
                <?php foreach ( $ms_bullets as $ms_bullet ) : ?><li><?php echo esc_html( $ms_bullet ); ?></li><?php endforeach; ?>
            </ul>`);
write('includes/product-page/dynamic-hero.php', hero);

let specs = read('includes/product-page/characteristic.php');
const details = [...specs.matchAll(/<p class="pspec__feature-text">([^<]+)<\/p>/g)].map(m=>m[1]);
const recommendations = [...specs.matchAll(/<p class="pspec__recs-text">([^<]+)<\/p>/g)].map(m=>m[1]);
const callouts = [...specs.matchAll(/<div class="pspec__callout">[\s\S]*?<p>([^<]+)<\/p>/g)].map(m=>m[1]);
specs = `<?php
$ms_id = get_the_ID();
$ms_original = motorsky_original_2807( $ms_id );
$ms_exploded = motorsky_product_value( $ms_id, 'exploded_image' );
if ( '' === $ms_exploded && $ms_original ) { $ms_exploded = get_theme_file_uri( 'assets/img/e2-3d.png' ); }
$ms_schemas = motorsky_layout_images( $ms_id, 'schematics', array( 'e2-shema-1.png', 'e2-shema-2.png', 'e2-shema-3.png' ) );
$ms_details = motorsky_layout_lines( $ms_id, 'detail_features', ${phpArray(details)} );
$ms_recs = motorsky_layout_lines( $ms_id, 'recommendations', $ms_original ? ${phpArray(recommendations)} : array() );
$ms_callout_keys = array( 'shaft', 'housing', 'magnet', 'wire', 'bearings', 'steel' );
$ms_callout_defaults = ${phpArray(callouts)};
?>\n` + specs;
specs = specs.replace(/<p class="pspec__intro">[\s\S]*?<\/p>/, `<p class="pspec__intro"><?php echo esc_html( motorsky_layout_intro( $ms_id ) ); ?></p>`);
specs = specs.replace(/<img class="pspec__exploded-img"[^>]*\?>"[^>]*>/, `<?php if ( $ms_exploded ) : ?><img class="pspec__exploded-img" src="<?php echo esc_url( $ms_exploded ); ?>" alt="<?php echo esc_attr( 'Разнесённая схема ' . get_the_title() ); ?>"><?php endif; ?>`);
let calloutIndex = 0;
specs = specs.replace(/(<div class="pspec__callout">[\s\S]*?<p>)([^<]+)(<\/p>)/g, (_,open,text,close) => {
 const i = calloutIndex++;
 return `${open}<?php $ms_callout = motorsky_product_value( $ms_id, $ms_callout_keys[${i}] ); echo esc_html( '' !== $ms_callout ? $ms_callout : ( $ms_original ? $ms_callout_defaults[${i}] : '—' ) ); ?>${close}`;
});
if(calloutIndex !== 6) throw new Error('Expected six original callouts');
const schemaStart = specs.indexOf('            <div class="swiper-wrapper">');
const schemaEnd = specs.indexOf('            <!-- Стрелки (видны только на мобильных) -->', schemaStart);
if(schemaStart < 0 || schemaEnd < 0) throw new Error('Schematic wrapper not found');
specs = specs.slice(0,schemaStart) + `            <div class="swiper-wrapper">
                <?php foreach ( $ms_schemas as $ms_index => $ms_schema ) : ?>
                <div class="swiper-slide pspec__schema-slide">
                    <img src="<?php echo esc_url( $ms_schema ); ?>" alt="<?php echo esc_attr( 'Чертёж ' . ( $ms_index + 1 ) . ' — ' . get_the_title() ); ?>">
                </div>
                <?php endforeach; ?>
            </div>
` + specs.slice(schemaEnd);
const tableStart = specs.indexOf('            <div class="pspec__col">');
const tableEnd = specs.indexOf('        <div class="group-pspec">', tableStart);
if(tableStart < 0 || tableEnd < 0) throw new Error('Specification columns not found');
specs = specs.slice(0,tableStart) + `            <?php foreach ( motorsky_layout_table( $ms_id ) as $ms_column ) : ?>
            <div class="pspec__col">
                <?php foreach ( $ms_column as $ms_row ) : ?><div class="pspec__row"><span><?php echo esc_html( $ms_row[0] ); ?></span><span><?php echo str_replace( '±', '<strong>±</strong>', esc_html( $ms_row[1] ) ); ?></span></div><?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>

` + specs.slice(tableEnd);
specs = specs.replace(/<p class="pspec__feature-text">[^<]+<\/p>(?:\s*<p class="pspec__feature-text">[^<]+<\/p>){3}/, `<?php foreach ( $ms_details as $ms_detail ) : ?><p class="pspec__feature-text"><?php echo esc_html( $ms_detail ); ?></p><?php endforeach; ?>`);
specs = specs.replace(/<p class="pspec__recs-text">[^<]+<\/p>(?:\s*<p class="pspec__recs-text">[^<]+<\/p>){2}/, `<?php foreach ( $ms_recs as $ms_rec ) : ?><p class="pspec__recs-text"><?php echo esc_html( $ms_rec ); ?></p><?php endforeach; ?>`);
write('includes/product-page/dynamic-characteristic.php', specs);

let app = read('includes/product-page/application.php');
app = `<?php $ms_applications = motorsky_layout_lines( get_the_ID(), 'applications' ); ?>\n` + app;
let index=0;
app = app.replace(/<h3 class="papp__card-title">([\s\S]*?)<\/h3>/g, (_,value)=> {
 const i=index++;
 return `<h3 class="papp__card-title"><?php if ( isset( $ms_applications[${i}] ) ) { echo esc_html( $ms_applications[${i}] ); } else { ?>${value}<?php } ?></h3>`;
});
if(index !== 4) throw new Error('Expected four application cards');
write('includes/product-page/dynamic-application.php',app);
console.log('Original markup reused for three dynamic product sections.');
