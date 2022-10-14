<?php
/**
 * The searchform template.
 *
 * @package base
 */
?>
<form method="get" class="d-flex" action="<?= esc_url(home_url()) ?>">
	<input type="search" name="s" placeholder="<?= get_search_query() ?get_search_query() :__( 'Enter word for search', 'bruden' ) ?>" value="<?= get_search_query() ?>" class="form-control mr-2" aria-label="Search">
	<input type="submit" value="<?= __( 'Search', 'bruden' ); ?>" class="btn btn-outline-success" />
</form>