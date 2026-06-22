import ProductPriceAutocalc from './product_autocalc';
import ProductGiftManager from './gift';
import { ShareModal } from './partials/share.js';
import { ProductTabs } from './partials/ProductTabs.js';
import { initProductGallery } from './partials/productGallery.js';
import { initTimers } from './partials/CountdownTimer.js';
import { ProductReviewManager } from './partials/ProductReviewManager.js';
import { ProductQuestionManager } from './partials/ProductQuestionManager.js';
import { BundleSliderManager } from './partials/BundleSliderManager';
$(document).ready(function() {
    window.priceAutocalc = new ProductPriceAutocalc();
    new ProductGiftManager();

    let shareModal = new ShareModal();
    shareModal.bindEvents('.js-share-btn');
    shareModal.initButtonPlacement();

    initProductGallery();

    $('.show_second_option').on('click', function (e) {
        e.preventDefault();
        let $input = $('#is_mirrored');
        let newValue = $input.val() == 1 ? 0 : 1;
        $input.val(newValue);
        $('.second_option').slideToggle(400);
    })

    let tabs = new ProductTabs();
    tabs.init();
    $('.bundles-slider-bundle').each(function() {
        new BundleSliderManager(this);
    });

    initTimers();
    new ProductReviewManager();
    new ProductQuestionManager();
});

