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
                    console.log(response.data);
                    
                    // Replace content
                    $('.first-heading').text(response.data.first_text);
                    $('.second-heading').text(response.data.second_text);
                    $('.fourth-heading').text(response.data.fourth_first_text);
                    $('.fifth-heading').text(response.data.fourth_second_text);
                    $('.fifth-text').text(response.data.fifth_text);
                    $('.third-image').attr('src', response.data.third_image);
                    $('.fifth-image').attr('src', response.data.fifth_image);
                    
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
                        arrows: true,
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
                    
                    
                }
            }
        });
    });
});
