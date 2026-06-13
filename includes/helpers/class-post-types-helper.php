<?php

namespace ELKLab\ELKAnalytics\Helpers;

class PostTypesHelper {
    public static function getPostTypes() {
        return get_post_types();
    }

    public static function getPublicPostTypes() {
        return get_post_types(['public' => true]);
    }

    public static function getPrivatePostTypes() {
        return get_post_types(['public' => false]);
    }

    public static function getPostType($postType) {
        return get_post_type_object($postType);
    }

    public static function postTypeExists($postType) {
        return post_type_exists($postType);
    }

    public static function isPostTypePublic($postType) {
        $postType = self::getPostType($postType);
        return $postType->public;
    }
}
