<?php
namespace Page;

class Constants
{
    public static $userName = 'rtcamp';
    public static $password = 'Test@1230';
    public static $customCssValue = '#buddypress #whats-new { height: 500px !important; overflow: hidden;}';
    public static $customCssEmptyValue = '';
    public static $thumbnailWidth = '150';
    public static $thumbnailHeight = '150';

    public static $topSaveButton = '.rtm-button-container.top input.rtmedia-settings-submit';

    public static $displayTabUrl = '#rtmedia-display';
    public static $buddypressTabUrl = '#rtmedia-bp';
    public static $mediaSizesTabUrl  = '#rtmedia-sizes';
    public static $privacyTabUrl = '#rtmedia-privacy';
    public static $customCssTabUrl = '#rtmedia-custom-css-settings';
    public static $otherSeetingsTabUrl = '#rtmedia-general';

    public static $displayTab = 'a#tab-rtmedia-display';
    public static $buddypressTab = 'a#tab-rtmedia-bp';
    public static $mediaSizesTab = 'a#tab-rtmedia-sizes';
    public static $privacyTab = 'a#tab-rtmedia-privacy';
    public static $customCssTab = 'a#tab-rtmedia-custom-css-settings';
    public static $otherSeetingsTab = 'a#tab-rtmedia-general';

    public static $strCommentCheckboxLabel = 'Allow user to comment on uploaded media';
    public static $strDirectUplaodCheckboxLabel = 'Enable Direct Upload';
    public static $strLightboxCheckboxLabel = 'Use lightbox to display media';
    public static $numOfMediaLabel = 'Number of media per page';
    public static $strMasonaryCheckboxLabel = 'Enable Masonry Cascading grid layout';
    public static $strMediaDisplayPaginationLabel = 'Media display pagination option';

    public static $strEnableMediaInProLabel = 'Enable media in profile';
    public static $strEnableMediaInGrpLabel = 'Enable media in group';
    public static $numOfMediaLabelActivity = 'Number of media items to show in activity stream';
    public static $strEnableAlbumLabel = 'Organize media into albums';
    public static $strMediaNotificationLabel = 'Enable media notification';
    public static $strActivityMediaLikeLabel = 'Create activity for media likes';
    public static $strActivityMediaCommentLabel = 'Create activity for media comments';
    public static $strMediaUploadFromActivityLabel = 'Allow upload from activity stream';

    public static $mediaPhotoLabel = 'Photo';
    public static $photoThumbnailLabel = 'Thumbnail';

    public static $privacyLabel = 'Enable privacy';
    public static $defaultPrivacyLabel = 'Default privacy';
    public static $privacyUserOverrideLabel = 'Allow users to set privacy for their content';

    public static $defaultStyleLabel = 'rtMedia default styles';
    public static $customCssLabel = 'Paste your CSS code';

    public static $strEnableJsonDataLabel = 'Enable JSON API';
    public static $strEnableUsageDataTrackingLabel = 'Allow usage data tracking';
    public static $adminbarMenuLabel = 'Admin bar menu integration';
    public static $footerLinkLabel = 'Add a link to rtMedia in footer';

    public static $numOfMediaTextbox = 'input[name="rtmedia-options[general_perPageMedia]"]';
    public static $numOfMediaTextboxActivity = 'input[name="rtmedia-options[buddypress_limitOnActivity]"]';

    public static $commentCheckbox = 'input[name="rtmedia-options[general_enableComments]"]';
    public static $directUploadCheckbox = 'input[name="rtmedia-options[general_direct_upload]"]';
    public static $lightboxCheckbox = 'input[name="rtmedia-options[general_enableLightbox]"]';
    public static $masonaryCheckbox = 'input[name="rtmedia-options[general_masonry_layout]"]';
    public static $loadmoreRadioButton = 'input[value="load_more"]';
    public static $paginationRadioButton = 'input[value="pagination"]';

    public static $enableMediaInProCheckbox = 'input[name="rtmedia-options[buddypress_enableOnProfile]"]';
    public static $enableMediaInGrpCheckbox = 'input[name="rtmedia-options[buddypress_enableOnGroup]"]';
    public static $enableAlbumCheckbox = 'input[name="rtmedia-options[general_enableAlbums]"]';
    public static $mediaNotificationCheckbox = 'input[name="rtmedia-options[buddypress_enableNotification]"]';
    public static $activityMediaLikeCheckbox = 'input[name="rtmedia-options[buddypress_mediaLikeActivity]"]';
    public static $activityMediaCommentCheckbox = 'input[name="rtmedia-options[buddypress_mediaCommentActivity]"]';
    public static $mediaUploadFromActivityCheckbox = 'input[name="rtmedia-options[buddypress_enableOnActivity]"]';

    public static $thumbnailWidthTextbox = 'input[name="rtmedia-options[defaultSizes_photo_thumbnail_width]"]';
    public static $thumbnailHeightTextbox = 'input[name="rtmedia-options[defaultSizes_photo_thumbnail_height]"]';
    public static $thumbnailCropCheckbox = 'input[name="rtmedia-options[defaultSizes_photo_thumbnail_crop]"]';

    public static $privacyCheckbox = 'input[name="rtmedia-options[privacy_enabled]"]';
    public static $privateRadioButton = 'input[value="60"]';
    public static $loggedInUsersRadioButton = 'input[value="20"]';
    public static $publicRadioButton = 'input#rtm-form-radio-4';
    public static $privacyUserOverrideCheckbox = 'input[name="rtmedia-options[privacy_userOverride]"]';

    public static $defaultStyleCheckbox = 'input[name="rtmedia-options[styles_enabled]"]';
    public static $cssTextaear= '.rtm-form-textarea';

    public static $enableJsonDataCheckbox = 'input[name="rtmedia-options[rtmedia_enable_api]"]';
    public static $enableUsageDataTrackingCheckbox = 'input[name="rtmedia-options[general_AllowUserData]"]';
    public static $adminbarMenuCheckbox = 'input[name="rtmedia-options[general_showAdminMenu]"]';
    public static $footerLinkCheckbox = 'input[name="rtmedia-options[rtmedia_add_linkback]"]';

    public static $closeButton = '.rtm-mfp-close';
    public static $masonryLayoutXpath = '//*[@id="rtm-gallery-title-container"]/h2';
    public static $masonryLayout = 'ul.masonry';
    public static $rtMediaAdminbar = '#wp-admin-bar-rtMedia';
    public static $footerLink = '.rtmedia-footer-link';
    public static $loadMore = 'a#rtMedia-galary-next';
    public static $paginationPattern = '.rtm-pagination .rtmedia-page-no';
    public static $mediaLinkOnProfile = 'a#user-media';
    public static $mediaLinkOnGroup = 'a#media';
    public static $mediaAlbumLink = 'a#rtmedia-nav-item-albums';
    public static $uploadButtonOnAtivityPage = '.rtmedia-add-media-button';
    public static $privacyDropdown = 'select#rtSelectPrivacy';

}
