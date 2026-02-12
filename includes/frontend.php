<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ipg_frontend_form_shortcode() {
    ob_start();
    ?>

    <div class="ipg-wrapper">
        <h2 class="ipg-title">Generate Instagram Post</h2>

        <form class="ipg-form">

            <!-- Category -->
            <div class="ipg-field">
                <label for="ipg-category">Select Post Category</label>
                <select id="ipg-category" name="selected_category">
                    <option value="">Choose Category</option>
                    <option value="Fashion">Fashion</option>
                    <option value="Fitness">Fitness</option>
                    <option value="Motivation">Motivation</option>
                    <option value="Product">Product</option>
                </select>
            </div>

            <!-- Styles -->
            <div class="ipg-field">
                <label>Select Styles</label>
                <div class="ipg-checkboxes">
                    <label><input type="checkbox" name="styles[]" value="Minimal"> Minimal</label>
                    <label><input type="checkbox" name="styles[]" value="Modern"> Modern</label>
                    <label><input type="checkbox" name="styles[]" value="Aesthetic"> Aesthetic</label>
                    <label><input type="checkbox" name="styles[]" value="Bold"> Bold</label>
                </div>
            </div>

            <!-- Number of Posts -->
            <div class="ipg-field">
                <label>How Many Posts?</label>
                <div class="ipg-radio">
                    <label><input type="radio" name="no_of_posts" value="1" checked> 1 Post</label>
                    <label><input type="radio" name="no_of_posts" value="2"> 2 Posts</label>
                </div>
            </div>

            <!-- Notes -->
            <div class="ipg-field">
                <label for="ipg-notes">Additional Description (Optional)</label>
                <textarea id="ipg-notes" name="additional_notes" placeholder="Write details if needed..."></textarea>
            </div>

            <!-- Submit -->
            <button type="button" class="ipg-btn">Submit Request</button>

        </form>
    </div>

    <?php
    return ob_get_clean();
}


add_shortcode( 'ig_post_form', 'ipg_frontend_form_shortcode' );
