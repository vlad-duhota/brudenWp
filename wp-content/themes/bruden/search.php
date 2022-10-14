<?php
/**
 * Search template.
 *
 * @package base
*/
?>
<?php get_header() ?>

<section class="container search_page">
	<div class="row">
		<div class="col-md-12">
			<h2 class="text-center"><?= __('Search page') ?></h2>
		</div>
	</div>
	<div class="blog_listing">
	<?php
		$args = array_merge( $wp_query->query, array( 'post_type' => "post") );
		query_posts($args);
		if(have_posts()){
			while(have_posts()){
				the_post();
				echo 'Is result';
			}
		}else{
			echo 'No result';
		} ?>
	</div>
</section>

<?php get_footer() ?>