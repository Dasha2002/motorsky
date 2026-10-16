<section class="news" id="news">
    <div class="container">
        <h2 class="news__title">Новости</h2>

        <div class="news__slider">
            <div class="swiper">
                <div class="swiper-wrapper news__track">
                    <?php
                    $motorsky_news = new WP_Query( motorsky_home_articles_query() );
                    if ( $motorsky_news->have_posts() ) :
                        while ( $motorsky_news->have_posts() ) : $motorsky_news->the_post();
                    ?>
                    <a class="swiper-slide news-card" href="<?php the_permalink(); ?>">
                        <div class="news-card__img">
                            <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'medium_large' ); else : ?>
                                <span class="ms-article-cover-placeholder" aria-hidden="true"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M6 3h8l4 4v14H6zM14 3v5h4M9 12h6M9 16h6"/></svg></span>
                            <?php endif; ?>
                        </div>
                        <div class="news-card__body">
                            <h3 class="news-card__title"><?php the_title(); ?></h3>
                            <p class="news-card__desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
                        </div>
                    </a>
                    <?php endwhile; else : ?>
                    <a class="swiper-slide news-card" href="<?php echo esc_url( motorsky_page_url( 'news-page.php', 'business-plan' ) ); ?>" target="_blank" rel="noreferrer">
                        <div class="news-card__img">
                            <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/news-2.jpg' ) ); ?>" alt="Бизнес-план">
                        </div>
                        <div class="news-card__body">
                            <h3 class="news-card__title">Бизнес-план</h3>
                            <p class="news-card__desc">В Перми создано производство бесколлекторных электродвигателей для БПЛА.</p>
                        </div>
                    </a>
                    <a class="swiper-slide news-card" href="<?php echo esc_url( motorsky_page_url( 'news-page.php', 'business-plan' ) ); ?>" target="_blank" rel="noreferrer">
                        <div class="news-card__img">
                            <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/news-1.jpg' ) ); ?>" alt="Участники Пермского НОЦ представили в Совете Федерации свои разработки">
                        </div>
                        <div class="news-card__body">
                            <h3 class="news-card__title">Участники Пермского НОЦ представили в Совете Федерации свои разработки в сфере БАС и медицине</h3>
                            <p class="news-card__desc">В рамках Дней Пермского края, которые прошли в Совете Федерации с 30 марта по 2 апреля, была организована выставка, посвященная истории, достопримечательностям и достижениям Прикамья.</p>
                        </div>
                    </a>
                    <?php endif; wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
