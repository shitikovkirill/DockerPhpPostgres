<?php
use SaveList\Hook\Filter;
/**
 * SaveForLater List page template
 *
 * @author Your Inspiration Themes
 * @package YITH Save for Later
 * @version 1.0.0
 */
$elements=count( $savelist_items );
//echo '<pre>'; var_dump($savelist_items);

$show_wishlist_link =  ( defined('YITH_WCWL') && get_option('ywsfl_show_wishlist_link') == 'yes' );

add_image_size( 'saveforlater-thumb', 66, 88, true );
?>
<div id="ywsfl_general_content" data-num-elements="<?php echo $elements;?>">
<?php
if($elements > 0):?>
    <?php
    $text = sprintf( _n( '1 Product', '%s Products', count( $savelist_items ), 'yith-woocommerce-save-for-later' ), count( $savelist_items ) );
    ?>
    <div id="ywsfl_title_save_list"><h3><?php echo $title_list.'('.$text.' )';?></h3></div>
    <div id="ywsfl_container_list">
        <?php
            foreach( $savelist_items as $item ):

                if(isset($item['meta_data'])){
                    $product_meta = unserialize($item['meta_data']);
                    if(isset($product_meta ['fpd_data'])){
                        $pd_product_thumbnail = $product_meta ['fpd_data']['fpd_product_thumbnail'];
                        $fpd_product = json_decode(stripslashes($product_meta ['fpd_data']['fpd_product']));
                        $fpd_product = $fpd_product[0];
                    }
                }

                global $product;
              $product_id   =    $item['product_id'];
                $product_var = null;

                if( function_exists( 'wc_get_product' ) ) {
                    $product = wc_get_product( $product_id );
                }
                else{
                    $product = get_product( $product_id );
                }

                if( $product !== false && $product->exists() ) :
                    $availability = $product->get_availability();
                    $stock_status = $availability['class'];
                    $url =  esc_url( remove_query_arg( 'save_for_later' ), wp_get_referer() );
                ?>
                    <div class="row" id="row-<?php echo $item['product_id'];?>" data-row-id="<?php echo $item['product_id'];?>" data-row-variation-id="<?php echo $item['variation_id'];?>">
                        <?php
                          $args =   array(
                              'remove_from_savelist'    => $item['product_id'],
                              'variation_id'            => $item['variation_id'],
                              'save_for_later_id'       => $item['ID'],
                          );
                            $hidden_field   =   '<input type="hidden" name="'.strtolower( 'save_for_later_id' ).'" value="'.$item['ID'].'"/>';
                        ?>
                        <div class="delete_col"><a href="<?php echo esc_url( add_query_arg( $args, $url ) ) ?>" class="remove_from_savelist" data-save-for-later-id="<?php echo $item['ID']; ?>" data-product-id="<?php echo $item['product_id'];?>" data-variation-id="<?php echo $item['variation_id'];?>" title="Remove this product">&times;</a></div>
                        <div class="img_product" style="max-width: 250px;">
                            <a href="#" id="linck<?php echo $item['product_id'].$item['variation_id']; ?>">
                                <?php
                                if(isset($pd_product_thumbnail)){
                                    echo '<img width="250" height="200" sizes="(max-width: 250px) 200vw, 200px" srcset="'.$pd_product_thumbnail.'">';
                                } else {
                                    echo $product->get_image('saveforlater-thumb');
                                }
                                ?>
                            </a>
                        </div>
                        <div class="sub_container_product">
                            <div class="product_name">
                                <h4><a href="#" id="linck<?php echo $item['product_id'].$item['variation_id']; ?>"><?php echo apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ?></a></h4>
                                <script>
                                    jQuery('#linck<?php echo $item['product_id'].$item['variation_id']; ?>').popup({
                                        content : <?php  $html = "'<div style=\"overflow: auto; height: 515px;\"><h1>{$fpd_product->title}</h1>";

                                    if(isset($pd_product_thumbnail)){
                                        $thumbnail = '<img  width="350" height="280" sizes="(max-width: 350px) 280vw, 280px" srcset="'.$pd_product_thumbnail.'">';
                                    } else {
                                        $thumbnail = $product->get_image('saveforlater-thumb');
                                    }
                                    $html .= $thumbnail.'</div>\'';
                                        echo $html;
                                        ?>,
                                        type : 'html'
                                    });
                                </script>

                                <?php
                                if( $item['variation_id']!=-1 && !is_null( $item['variation_id'] ) ) {

                                    $product_var    =   wc_get_product( $item['variation_id'] );
                                    $variations_av = $product_var->get_variation_attributes();


                                    $item_data = array();

                                    // Variation data
                                    if (is_array($variations_av)) {

                                        foreach ($variations_av as $name => $value) {
                                            $label = '';

                                            if ('' === $value)
                                                continue;

                                            $taxonomy = wc_attribute_taxonomy_name(str_replace('attribute_pa_', '', urldecode($name)));

                                            // If this is a term slug, get the term's nice name
                                            if (taxonomy_exists($taxonomy)) {
                                                $term = get_term_by('slug', $value, $taxonomy);
                                                if (!is_wp_error($term) && $term && $term->name) {
                                                    $value = $term->name;
                                                }
                                                $label = wc_attribute_label($taxonomy);

                                            } else {

                                                if (strpos($name, 'attribute_') !== false) {
                                                    $custom_att = str_replace('attribute_', '', $name);

                                                    if ($custom_att != '') {
                                                        $label = wc_attribute_label($custom_att);
                                                    } else {
                                                        $label = $name;
                                                    }
                                                }

                                            }

                                            $item_data[] = array(
                                                'key' => $label,
                                                'value' => $value
                                            );

                                            $hidden_field .=   '<input type="hidden" name="'.strtolower( $name ).'" value="'.$value.'"/>';
                                        }
                                    }

                                    // Output flat or in list format
                                    if (sizeof($item_data) > 0) {
                                        echo '<div class="variation">';
                                        foreach ($item_data as $data) {
                                            echo '<div class="variation-' . $data['key'] . '">';
                                            echo '<span class="variation_name">' . esc_html($data['key']) . ':</span>';
                                            echo '<span class="variation_value">' . wp_kses_post($data['value']) . "</span>";
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                    }
                                }
                                ?>

                                <p class="display_price">
                                    <?php
                                    if( is_a( $product, 'WC_Product_Bundle' ) ){
                                        if( $product->min_price != $product->max_price ){
                                            echo sprintf( '%s - %s', wc_price( $product->min_price ), wc_price( $product->max_price ) );
                                        }
                                        else{
                                            echo wc_price( $product->min_price );
                                        }
                                    }
                                    elseif( $product->price != '0' ) {
                                        echo $product->get_price_html();
                                    }
                                    else {
                                        echo apply_filters( 'yith_free_text', __( 'Free!', 'yith-woocommerce-save-for-later' ) );
                                    }
                                    ?>
                                </p>
                                <p class="display_product_status">
                                    <?php
                                    if( $stock_status == 'out-of-stock' ) {
                                    $stock_status = "Out";
                                    echo '<span class="savelist-out-of-stock">' . __( 'Out of Stock', 'yith-woocommerce-save-for-later' ) . '</span>';
                                    } else {
                                    $stock_status = "In";
                                    echo '<span class="savelist-in-stock">' . __( 'In Stock', 'yith-woocommerce-save-for-later' ) . '</span>';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="cont_buttons">
                                <!-- Add to cart button -->
                                <?php if( isset( $stock_status ) && $stock_status != 'Out' ): ?>

                                  <form method="post">
                                    <?php
                                    new Filter();
                                    $product    =    isset( $product_var ) ? $product_var : $product;
                                    if (function_exists('wc_get_template')) {
                                        wc_get_template('loop/add-to-cart.php');
                                    } else {
                                        woocommerce_get_template('loop/add-to-cart.php');
                                    }

                                    if( $item['variation_id']!=-1 ) {

                                        echo '<input type="hidden" name="variation_id" value="'.$item['variation_id'].'"/>';
                                        echo $hidden_field;
                                    }

                                    ?>
                                    <?php if( $show_wishlist_link && ! YITH_WCWL()->is_product_in_wishlist($product_id) ) :
                                        echo do_shortcode('[yith_wcwl_add_to_wishlist]');
                                        ?>

                                    <?php endif;?>
                                <?php endif; ?>
                                  </form>

                            </div>

                    </div>
                    <?php else:?>
                    <?php
                    global $YIT_Save_For_Later;
                    $YIT_Save_For_Later->remove_no_available_product_form_save_list($item['product_id'], $item['variation_id'] );
                    ?>
                <?php endif;?>
        <?php endforeach;?>
    </div>
<?php endif;?>
</div>
