<?php
$background = get_post_meta( $post->ID, '_kad_adv_page_background', true ) ?? '';
global $post;
$_content = $post->post_content;
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="ktap-html" itemtype="https://schema.org/WebPage">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
		<?php
		wp_head();
		?>

		<?php if ( $background ) : ?>
			<style>
				body {
					--ktap-page-background-color: <?php echo esc_attr( $background ); ?>;
				}
			</style>
		<?php endif; ?>
	</head>

	<body class="ktap-body">
		<main id="main" class="site-main" role="main">
			<div class="ktap-content-wrap">
				<?php
				// the_content()

				$_content = apply_filters( 'the_content', $_content );
				$_content = str_replace( ']]>', ']]&gt;', $_content );
				echo $_content;
				?>
			</div>
		</main>
		<?php
		wp_footer();
		?>
	</body>
</html>
