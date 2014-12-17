<?php

function translated_category_id($id = null)
{
    if(is_null($id)) {
        $id = get_the_ID();
    }

    if(function_exists('icl_object_id')) {
        return (int)icl_object_id($id, 'category', true);
    }

    return (int)$id;
}

function translated_page_id($id = null)
{
    if(is_null($id)) {
        $id = get_the_ID();
    }

    if(function_exists('icl_object_id')) {
        return (int)icl_object_id($id, 'page', true);
    }

    return (int)$id;
}

function translated_page_id_from_path($path)
{
    $page = get_page_by_path($path);
    if(!is_null($page)) {
        return translated_page_id($page->ID);
    }
}

function translated_post_id($id = null)
{
    if(is_null($id)) {
        $id = get_the_ID();
    }

    if(function_exists('icl_object_id')) {
        return (int)icl_object_id($id, 'post', true);
    }

    return (int)$id;
}

function translated_post_tag_id($id = null)
{
    if(is_null($id)) {
        $id = get_the_ID();
    }

    if(function_exists('icl_object_id')) {
        return (int)icl_object_id($id, 'post_tag', true);
    }

    return (int)$id;
}

function translated_attachment_id($id = null)
{
    if(is_null($id)) {
        $id = get_the_ID();
    }

    if(function_exists('icl_object_id')) {
        return (int)icl_object_id($id, 'attachment', true);
    }

    return (int)$id;
}
