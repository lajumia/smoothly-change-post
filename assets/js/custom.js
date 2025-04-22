jQuery(document).ready(function($) {
    $('.next-post-button, .prev-post-button').on('click', function(e) {
        e.preventDefault();

        let postId = $(this).data('post-id'); // You set this dynamically

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'fetch_custom_post_data',
                post_id: postId
            },
            success: function(response) {
                if (response.success) {
                    console.log(response.data.container_background);
                    
                    // Replace content
                    $('.first-heading').text(response.data.first_text);
                    $('.second-heading').text(response.data.second_text);
                    $('.fourth-heading').text(response.data.fourth_first_text);
                    $('.fifth-heading').text(response.data.fourth_second_text);
                    $('.fifth-text').text(response.data.fifth_text);
                    $('.third-image').attr('src', response.data.third_image);
                    $('.fifth-image').attr('src', response.data.fifth_image);
                    $('.right-bg').css('background-image', 'url(' + response.data.container_background + ')');
                    
                    // ✅ Update Previous Button
                    $('.prev-post-button')
                        .data('post-id', response.data.prev_post_id)
                        .attr('href', response.data.prev_post_url);
                        
                    // ✅ Update Next Button
                    $('.next-post-button')
                        .data('post-id', response.data.next_post_id)
                        .attr('href', response.data.next_post_url);    
                    
                    
                    // ✅ Update the URL in the browser without reloading
                    history.pushState(null, '', response.data.permalink);

                    // ✅ Replace Silk Slider Gallery
                    let mainSlider = $('#mainSlider');
                    let thumbnails = $('#sliderThumbnails');

                    // Remove previous slides
                    if (mainSlider.hasClass('slick-initialized')) {
                        mainSlider.slick('unslick');
                    }
                    if (thumbnails.hasClass('slick-initialized')) {
                        thumbnails.slick('unslick');
                    }

                    mainSlider.empty();
                    thumbnails.empty();

                    // Append new slides
                    $.each(response.data.gellery, function(index, url) {
                        mainSlider.append(`
                            <div class="slide">
                                <img src="${url}" alt="Gallery Image">
                            </div>
                        `);
                        thumbnails.append(`
                            <div class="thumb">
                                <img src="${url}" alt="Thumbnail">
                            </div>
                        `);
                    });

                    // Reinitialize slick slider
                    mainSlider.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false,
                        dots: false,
                        infinite: true,
                        fade: true, // Optional, for fade effect between slides
                    });

                    thumbnails.slick({
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
                    
                    function handleMobileSlider() {
                        if (window.innerWidth < 768) {
                          // Prevent reinit
                          if ($('#mainSlider').hasClass('slick-initialized')) {
                            $('#mainSlider').slick('unslick');
                          }
                    
                          // Remove Slick-related styles and classes
                          $('#mainSlider .slide').each(function () {
                            $(this)
                              .removeAttr('style') // remove inline styles
                              .removeClass('slick-slide slick-current slick-active')
                              .addClass('mobile-visible') // Add helper class
                              .css({
                                'opacity': '1',
                                'display': 'block',
                                'visibility': 'visible',
                                'width': '100%' // make image full width
                              });
                          });
                    
                          // Optional: Remove Slick DOM wrappers like .slick-track, .slick-list
                          $('#mainSlider').removeClass('slick-initialized slick-slider');
                          $('#mainSlider .slick-track, #mainSlider .slick-list').contents().unwrap().unwrap();
                    
                          // Hide thumbnail nav (if exists)
                          $('.thumbnail-slider').hide(); // or any class you have for thumbs
                        }
                      }
                    
                      // Call on load
                      handleMobileSlider();
                    
                      // Also call on resize if user switches screen size
                      $(window).on('resize', function () {
                        handleMobileSlider();
                      });
                      
                     
                    
                    



                    
                    
                }
            }
        });
    });
});
