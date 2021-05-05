<?php
get_header();
/////////////
$is_cao_site_list_blog = is_cao_site_list_blog();
if ($is_cao_site_list_blog) {
  $col_class = 'content-column col-lg-9';
  $latest_layout = 'bloglist';
}else{
  $col_class = 'col-lg-12';
  $latest_layout = _cao( 'latest_layout', 'grid' );
}
/////////////
?>
  <div class="container">
    <?php get_template_part( 'parts/filter-bar' ); ?>
    <div class="row">
      <div class="<?php echo $col_class;?>">
        <div class="content-area">
          <main class="site-main">
            <?php if ( have_posts() ) : ?>
              <div class="row posts-wrapper">
                <?php while ( have_posts() ) : the_post();
                  get_template_part( 'parts/template-parts/content', $latest_layout );
                endwhile; ?>
              </div>
              <?php get_template_part( 'parts/pagination' ); ?>
            <?php else : ?>
              <?php get_template_part( 'parts/template-parts/content', 'none' ); ?>
            <?php endif; ?>
          </main>
        </div>
      </div>
      <?php if ($is_cao_site_list_blog) : ?>
      <!-- 侧边栏 -->
      <div class="sidebar-column col-lg-3">
        <aside class="widget-area">
          <?php dynamic_sidebar( 'blog' ); ?>
        </aside>
      </div>
      <?php endif;?>
    </div>
  </div>
<?php
wp_reset_postdata();

get_footer();
