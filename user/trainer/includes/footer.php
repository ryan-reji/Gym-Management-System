<?php
/**
 * Footer template for trainer booking website
 *
 * @package TrainerBooking
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

</div><!-- Close main content container -->

<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-widgets">
            <div class="footer-widget about-us">
                <h4>About Us</h4>
                <p><?php echo get_theme_mod('footer_about_text', 'We are dedicated to providing exceptional personal training services tailored to your needs and goals.'); ?></p>
            </div>
            
            <div class="footer-widget quick-links">
                <h4>Quick Links</h4>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer-menu',
                    'container'      => false,
                    'menu_class'     => 'footer-links',
                    'fallback_cb'    => false,
                ));
                ?>
            </div>
            
            <div class="footer-widget contact-info">
                <h4>Contact Information</h4>
                <ul class="contact-details">
                    <?php if (get_theme_mod('contact_address')) : ?>
                        <li><i class="fas fa-map-marker-alt"></i> <?php echo get_theme_mod('contact_address'); ?></li>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('contact_phone')) : ?>
                        <li><i class="fas fa-phone"></i> <a href="tel:<?php echo get_theme_mod('contact_phone'); ?>"><?php echo get_theme_mod('contact_phone'); ?></a></li>
                    <?php endif; ?>
                    
                    <?php if (get_theme_mod('contact_email')) : ?>
                        <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo get_theme_mod('contact_email'); ?>"><?php echo get_theme_mod('contact_email'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="footer-widget booking-cta">
                <h4>Ready to Get Started?</h4>
                <p>Book your first training session today!</p>
                <a href="<?php echo esc_url(get_permalink(get_theme_mod('booking_page_id'))); ?>" class="btn booking-btn">Book Now</a>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-social">
                <?php if (get_theme_mod('social_facebook')) : ?>
                    <a href="<?php echo esc_url(get_theme_mod('social_facebook')); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                
                <?php if (get_theme_mod('social_instagram')) : ?>
                    <a href="<?php echo esc_url(get_theme_mod('social_instagram')); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                
                <?php if (get_theme_mod('social_twitter')) : ?>
                    <a href="<?php echo esc_url(get_theme_mod('social_twitter')); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                <?php endif; ?>
                
                <?php if (get_theme_mod('social_youtube')) : ?>
                    <a href="<?php echo esc_url(get_theme_mod('social_youtube')); ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                <?php endif; ?>
            </div>
            
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?>. All Rights Reserved.</p>
            </div>
            
            <?php if (get_theme_mod('show_privacy_links', true)) : ?>
                <div class="privacy-links">
                    <a href="<?php echo esc_url(get_privacy_policy_url()); ?>">Privacy Policy</a>
                    <?php if (get_theme_mod('terms_page_id')) : ?>
                        <span class="separator">|</span>
                        <a href="<?php echo esc_url(get_permalink(get_theme_mod('terms_page_id'))); ?>">Terms & Conditions</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (get_theme_mod('show_back_to_top', true)) : ?>
        <a href="#top" class="back-to-top"><i class="fas fa-chevron-up"></i></a>
    <?php endif; ?>
</footer>

<?php if (get_theme_mod('show_booking_floating_btn', true)) : ?>
    <div class="floating-booking-btn">
        <a href="<?php echo esc_url(get_permalink(get_theme_mod('booking_page_id'))); ?>">
            <i class="fas fa-calendar-alt"></i> Book a Session
        </a>
    </div>
<?php endif; ?>

<?php wp_footer(); ?>

<?php if (get_theme_mod('custom_footer_js')) : ?>
    <script>
        <?php echo get_theme_mod('custom_footer_js'); ?>
    </script>
<?php endif; ?>

</body>
</html>