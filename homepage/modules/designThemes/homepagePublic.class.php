<?php

class homepagePublicDesignTheme extends DesignTheme
{
    public function initialize()
    {
        $controller = controller::getInstance();
        $pathsManager = $controller->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $this->cssPath = $tricksterPath . 'homepage/css/public/';
        $this->templatesFolder = $tricksterPath . 'homepage/templates/public/';
        $this->imagesFolder = 'homepage/images/public/';
        $this->imagesPath = $tricksterPath . $this->imagesFolder;
        $this->javascriptUrl = $controller->baseURL . $pathsManager->getRelativePath('trickster') . 'homepage/js/public/';
        $this->javascriptPath = $tricksterPath . 'homepage/js/public/';
        $this->javascriptFiles = [
            'logics.commentForm.js',
            'logics.galleries.js',
            'logics.contentToggler.js',
            'logics.maps.js',
            'logics.search.js',
            'logics.tabs.js',
            'logics.translations.js',
            'logics.subscription.js',
            'logics.ajaxForm.js',
            'logics.feedbackForm.js',
            'logics.googlead.js',
            'logics.tracking.js',
            'logics.emailConversion.js',
            'logics.orders.js',
            'logics.googleAnalytics.js',
            'logics.product.js',
            'logics.passwordReminder.js',
            'logics.lazyImage.js',
            'logics.events.js',
            'logics.dropDown.js',
            'logics.redirectSelect.js',
            'logics.textarea.js',
            'logics.banner.js',
            'logics.input.js',
            'logics.accordeon.js',
            'logics.fancyTitle.js',
            'logics.formSubmitLink.js',
            'logics.linkList.js',
            'logics.login.js',
            'logics.mainMenu.js',
            'logics.newWindowLink.js',
            'logics.subMenu.js',
            'logics.hiddenFields.js',
            'logics.scrollItems.js',
            'logics.mobileMenu.js',
            'logics.mobileCommonMenu.js',
            'logics.smoothScrollTo.js',
            'logics.spoiler.js',
            'logics.jsmart.js',
            'mixin.LazyLoading.js',
            'mixin.slides.js',
            'mixin.scrollPages.js',
            'mixin.carouselGallery.js',
            'mixin.slideOverlay.js',
            'component.smoothScrollTo.js',
            'component.input.js',
            'component.contentToggler.js',
            'component.gallery.js',
            'component.galleryImage.js',
            'component.bubble.js',
            'component.cookiePolicy.js',
            'component.subscriptionForm.js',
            'component.tabs.js',
         //   'component.tipPopup.js',
            'component.toolTip.js',
            'component.ajaxForm.js',
            'component.commentForm.js',
            'component.comment.js',
            'component.map.js',
            'component.passwordReminderForm.js',
            'component.lazyImage.js',
            'component.eventsList.js',
            'component.galleryImagesScroll.js',
            'component.searchForm.js',
            'component.redirectSelect.js',
            'component.fullScreenGallery.js',
            'component.galleryFullScreenButton.js',
            'component.darkLayer.js',
            'component.textarea.js',
            'component.banner.js',
            'component.accordeon.js',
            'component.dropDown.js',
            'component.fancyTitle.js',
            'component.formSubmitLink.js',
            'component.linkList.js',
            'component.login.js',
            'component.mainMenu.js',
            'component.newWindowLink.js',
            'component.galleryImagesSlide.js',
            'component.galleryImagesCarousel.js',
            'component.gallerySelector.js',
            'component.galleryButton.js',
            'component.galleryNextButton.js',
            'component.galleryPreviousButton.js',
            'component.galleryDescription.js',
            'component.galleryPlaybackButton.js',
            'component.staticGallery.js',
            'component.hiddenFields.js',
            'component.scrollItems.js',
            'component.mobileMenu.js',
            'component.mobileHeader.js',
            'component.tracking.js',
            'component.modalAction.js',
            'component.modal.js',
            'component.FeedbackForm.js',
            'component.mobileCommonMenu.js',
            'component.SubMenusPopupItemComponent.js',
            'component.SubmenuItemPopupComponent.js',
            'component.SubMenuItemComponent.js',
            'component.socialMediaShare.js',
            'component.spoiler.js',
            'mixin.scrollAttaching.js',
            'logics.ajaxProductList.js',
            'component.ajaxProductList.js',
        ];
    }
}