<?php
/**
 * Enqueue styles for the child theme.
 */
function ttf_child_enqueue_styles() {
    // Load parent theme styles if needed
    wp_enqueue_style(
        'ttf-parent-style', 
        get_template_directory_uri() . '/style.css'
    );

    // Load child theme styles
    wp_enqueue_style(
        'ttf-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('ttf-parent-style'), // Set dependency
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'ttf_child_enqueue_styles');

/**
 * Enqueue script for back to top button
 */
function ttf_scroll_to_top_script() {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const backToTop = document.querySelector(".wl-scroll-to-top");

            if (backToTop) {
                // Initially hide the button until user scrolls down
                backToTop.classList.add("hide");

                window.addEventListener("scroll", function () {
                    if (window.scrollY > 200) { // Show after scrolling 200px
                        backToTop.classList.add("show");
                        backToTop.classList.remove("hide");
                    } else {
                        backToTop.classList.remove("show");
                        backToTop.classList.add("hide");
                    }
                });

                backToTop.addEventListener("click", function () {
                    window.scrollTo({
                        top: 0,
                        behavior: "smooth"
                    });
                });
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'ttf_scroll_to_top_script');



/* SVG Support */
/**
 * Allow SVG uploads for administrator users.
 *
 * @param array $upload_mimes Allowed mime types.
 *
 * @return mixed
 */
add_filter(
	'upload_mimes',
	function ( $upload_mimes ) {
		// By default, only administrator users are allowed to add SVGs.
		// To enable more user types edit or comment the lines below but beware of
		// the security risks if you allow any user to upload SVG files.
		if ( ! current_user_can( 'administrator' ) ) {
			return $upload_mimes;
		}

		$upload_mimes['svg']  = 'image/svg+xml';
		$upload_mimes['svgz'] = 'image/svg+xml';

		return $upload_mimes;
	}
);

/**
 * Add SVG files mime check.
 *
 * @param array        $wp_check_filetype_and_ext Values for the extension, mime type, and corrected filename.
 * @param string       $file Full path to the file.
 * @param string       $filename The name of the file (may differ from $file due to $file being in a tmp directory).
 * @param string[]     $mimes Array of mime types keyed by their file extension regex.
 * @param string|false $real_mime The actual mime type or false if the type cannot be determined.
 */
add_filter(
	'wp_check_filetype_and_ext',
	function ( $wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime ) {

		if ( ! $wp_check_filetype_and_ext['type'] ) {

			$check_filetype  = wp_check_filetype( $filename, $mimes );
			$ext             = $check_filetype['ext'];
			$type            = $check_filetype['type'];
			$proper_filename = $filename;

			if ( $type && 0 === strpos( $type, 'image/' ) && 'svg' !== $ext ) {
				$ext  = false;
				$type = false;
			}

			$wp_check_filetype_and_ext = compact( 'ext', 'type', 'proper_filename' );
		}

		return $wp_check_filetype_and_ext;

	},
	10,
	5
);
