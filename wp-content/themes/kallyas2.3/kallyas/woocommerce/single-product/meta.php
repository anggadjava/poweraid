<?php
/**
 * Single Product Meta
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

global $post, $product;
?>
<div class="product_meta">
<p>
	<?php if ( $product->is_type( array( 'simple', 'variable' ) ) && get_option('woocommerce_enable_sku') == 'yes' && $product->get_sku() ) : ?>
		<span itemprop="productID" class="sku"><strong><?php _e('SKU:', THEMENAME); ?></strong> <?php echo $product->get_sku(); ?>.</span>
	<?php endif; ?>

	<?php echo $product->get_categories( ', ', ' <span class="posted_in"><strong>'.__('Category:', THEMENAME).'</strong> ', '.</span>'); ?>

	<?php echo $product->get_tags( ', ', ' <span class="tagged_as">'.__('Tags:', THEMENAME).' ', '.</span>'); ?>
</p>
</div>