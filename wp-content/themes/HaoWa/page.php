<?php get_header();?>
<?php if (have_posts()) : while (have_posts()) : the_post();?>
<link href="<?php bloginfo('template_url');?>/style.css" rel="stylesheet">
<style>
.content .row {
	padding: 15px 25px 20px 25px;
}
.items p {
	line-height: 26px;
	font-size: 14px;
}
.items img {
	max-width: 100%;
}
.aligncenter {
	display: block;
	margin-left: auto;
	margin-right: auto;
}
.aligncenter p.wp-caption-text {
	display: block;
	margin-left: auto;
	margin-right: auto;
	text-align: center;
}
</style>
<div class="content">
  <div class="container">
    <div class="row">
      <div class="col-md-1 sidebar">
        <div class="content-sidebar">
          <dl id="goto">
            <dt style="top: 10.5px;"><span class="show-list"></span></dt>
          </dl>
        </div>
      </div>
      <div class="col-md-11">
        <div class="part current" id="web" data-title="<?php the_title();?>">
          <h2 class=""><strong>
            <?php the_title();?>
            </strong></h2>
          <div class="items">
            <div class="row">
              <p>
                <?php the_content('Read more...');?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endwhile;else: ;endif;?>
<?php get_footer(); ?>
