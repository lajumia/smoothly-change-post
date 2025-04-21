<?php get_header(); ?>

<div class="property-container" style="margin: auto;">
    <section class="property-details-section">
      <!-- Top Section -->
      <div class="property-column">
        <p class="first-heading">
          <?php
            // Get the custom meta field value
            $property_heading = get_post_meta(get_the_ID(), 'first_paragraph', true);
            
            // Fallback if no value is set
            echo $property_heading ? esc_html($property_heading) : ' ';
          ?>
        </p>
      </div>
    </section>

    <section class="property-details-section-2">
        <div class="property-details-container">
    
            <div class="property-section-2-first">
              <p class="second-heading">
                <?php
                  // Fetch the paragraph meta field
                  $paragraph = get_post_meta(get_the_ID(), 'second_paragraph', true);
            
                  // Output the paragraph or fallback
                  echo $paragraph ? esc_html($paragraph) : ' ';
                ?>
              </p>
            </div>

            
            
            
           <!-- Logo Column -->
           <!-- Logo Column -->
            <div class="property-column third-logo property-section-2-second">
              <?php
                // Get image attachment ID
                $logo_img_id = get_post_meta(get_the_ID(), 'third_image_element', true);
            
                // Get image URL from ID
                $logo_img_url = wp_get_attachment_image_url($logo_img_id, 'full');
            
                // Get logo link URL
                $logo_url = get_post_meta(get_the_ID(), 'third_image_element_url', true);
            
                if (!empty($logo_img_url)) :
                  // If logo URL is provided, wrap image with <a>
                  if (!empty($logo_url)) {
                    echo '<a href="' . esc_url($logo_url) . '" target="_blank" rel="nofollow">';
                  }
            
                  echo '<img class="third-image" src="' . esc_url($logo_img_url) . '" alt="Property Logo" width="50" height="10" />';
            
                  if (!empty($logo_url)) {
                    echo '</a>';
                  }
                endif;
              ?>
            </div>






        
            <!-- Middle Column (2 Rows) -->
            <div class="property-section-2-third">
              <div class="property-row">
                <div class="property-subcolumn">
                  <p class="fourth-heading">
                    <?php
                      $left_content = get_post_meta(get_the_ID(), 'fourth_first_paragraph', true);
                      if (!empty($left_content)) {
                        echo nl2br(esc_html($left_content)); // Converts newlines to <br>
                      }
                    ?>
                  </p>
                </div>
                <div class="property-subcolumn">
                  <p class="fifth-heading">
                    <?php
                      $right_content = get_post_meta(get_the_ID(), 'fourth_second_paragraph', true);
                      if (!empty($right_content)) {
                        echo nl2br(esc_html($right_content)); // Converts newlines to <br>
                      }
                    ?>
                  </p>
                </div>
              </div>
            </div>

        
            <!-- Awards Column -->
            <div class="property-section-2-forth">
              <div class="property-row-forth">
                <div class="property-subcolumn">
                  <?php
                    // Fetching the URL and image ID from custom fields
                    $forbes_url = get_post_meta(get_the_ID(), 'fifth_first_image', true);
                    $forbes_image_id = get_post_meta(get_the_ID(), 'fifth_first_image', true);
                    $forbes_image_url = wp_get_attachment_image_url($forbes_image_id, 'full');
                    
                    // Check if both URL and image are set
                    if ($forbes_url && $forbes_image_url) : ?>
                      <a href="<?php echo esc_url($forbes_url); ?>" target="_blank" rel="nofollow">
                        <img class="fifth-image" src="<?php echo esc_url($forbes_image_url); ?>" alt="Forbes 5 Star" width="40" height="29" />
                      </a>
                    <?php endif; ?>
                </div>
                <div class="property-subcolumn">
                  <p class="fifth-text"><?php echo esc_html(get_post_meta(get_the_ID(), 'fifth_paragraph', true)); ?></p>
                </div>

              </div>
            </div>
        
            <!-- Navigation Column -->
                <!--<div class="property-section-2-first hidden-on-mobile">-->
                <!--  <div class="post-nav">-->
                <!--    <a class="prev prev-post-button" href="https://threearch.com/rosewood-mayakoba/">PREV</a> |-->
                <!--    <a class="next next-post-button" href="https://threearch.com/cap-rock-falls-club/">NEXT</a>-->
                <!--  </div>-->
                <!--</div>-->
        <?php
        global $post;
        
        if (!$post) return;
        
        $current_post_id   = $post->ID;
        $current_post_type = get_post_type($current_post_id);
        
        // Get terms from the custom taxonomy 'property_category'
        $terms = wp_get_post_terms($current_post_id, 'property_category');
        
        if (!empty($terms)) {
            $term_id = $terms[0]->term_id; // Use the first term (you can customize if needed)
        
            // Query all posts in this taxonomy term, ordered by menu_order
            $args = [
                'posts_per_page'   => -1,
                'post_type'        => $current_post_type,
                'post_status'      => 'publish',
                'orderby'          => 'menu_order',
                'order'            => 'ASC',
                'tax_query'        => [
                    [
                        'taxonomy' => 'property_category',
                        'field'    => 'term_id',
                        'terms'    => $term_id,
                    ],
                ],
                'suppress_filters' => false, // Critical for Post Types Order plugin
                'fields'           => 'ids'  // Fetch only post IDs for performance
            ];
        
            $post_ids = get_posts($args);
            $current_index = array_search($current_post_id, $post_ids);
        
            // Circular navigation logic
            $total_posts = count($post_ids);
            $prev_index = ($current_index - 1 + $total_posts) % $total_posts;
            $next_index = ($current_index + 1) % $total_posts;
        
            $prev_post_id = $post_ids[$prev_index];
            $next_post_id = $post_ids[$next_index];
            ?>
        
            <div class="property-section-2-first hidden-on-mobile">
                <div class="post-nav">
                    <a class="prev prev-post-button"
                       href="<?php echo esc_url(get_permalink($prev_post_id)); ?>"
                       data-post-id="<?php echo esc_attr($prev_post_id); ?>">
                        PREV
                    </a>
                    |
                    <a class="next next-post-button"
                       href="<?php echo esc_url(get_permalink($next_post_id)); ?>"
                       data-post-id="<?php echo esc_attr($next_post_id); ?>">
                        NEXT
                    </a>
                </div>
            </div>
        
            <?php
        }
        ?>





        
        </div>
    </section>
    <!--<section style="padding: 20px 0;">-->
    <!--  <div style="display: flex; justify-content: center; width: 100%;">-->
    <!--    <div style="flex: 1; max-width: 100%;">-->
          <!-- Spacer -->
    <!--      <div style="height: 40px;"></div>-->
    <!--    </div>-->
    <!--  </div>-->
    <!--</section>-->
    
    
    

    <?php
        $gallery_images = get_field('silk_slider_images'); // Use your actual gallery field name
        
        if ($gallery_images):
        ?>
        <section class="custom-slider-section">
            <div class="slider-wrapper">
        
                <!-- Main Slider -->
                <div id="mainSlider" class="main-slider">
                    <?php foreach ($gallery_images as $image): ?>
                        <div class="slide">
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
        
                <!-- Thumbnails -->
                <div id="sliderThumbnails" class="slider-thumbnails">
                    <?php foreach ($gallery_images as $image): ?>
                        <div class="thumb">
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
        
            </div>
        </section>
        <?php endif; ?>








 <!--Background Right Image -->
    <!--<div class="right-bg" style="background-image:url('https://threearch.com/wp-content/uploads/2023/05/Penninsula-AdobeStock_242909567_bw-72.png');"></div>-->

<script>jQuery(document).ready(function($) {
    // Initialize the main slider
    $('#mainSlider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        infinite: true,
        fade: true, // Optional, for fade effect between slides
    });

    // Initialize the thumbnail navigation
    $('#sliderThumbnails').slick({
        slidesToShow: 6, // Number of thumbnails to show at once
        slidesToScroll: 1,
        asNavFor: '.main-slider', // Link thumbnails to main slider
        focusOnSelect: true, // Select a thumbnail on click
        centerMode: true,
        centerPadding: '0',
    });
    // Change main slider on thumbnail hover
    $('.slider-thumbnails .thumb').on('mouseenter', function () {
        const index = $(this).index();
        $('.main-slider').slick('slickGoTo', index);
    });
});
</script>





</div>

<?php get_footer(); ?>
